<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\setup\schema;

use Limbas\layout\Layout;
use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class ColorSchema extends LimbasModel
{
    protected static string $tableName = 'LMB_COLORSCHEMES';
    
    private static array $iniCache = [];
    
    public array $variables = [];
    public array $edited = [];
    public array $customVariables = [];

    public bool $valid = true;
    public bool $regenerate = true;
    
    public function __construct(
        public int $id,
        public string $name,
        public string $layout,
        public string $theme,
        
    )
    {
        $this->load();
    }

    /**
     * @param int $id
     * @return ColorSchema|null
     */
    public static function get(int $id): ColorSchema|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }

    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {
        $rs = Database::select(self::$tableName, where: $where, orderBy: ['ID' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                lmbdb_result($rs, 'NAME'),
                lmbdb_result($rs, 'LAYOUT'),
                lmbdb_result($rs, 'THEME') ?? 'light',
            );

        }

        return $output;
    }

    public function save(): bool
    {
        $data = [
            'NAME' => $this->name,
            'LAYOUT' => $this->layout,
            'THEME' => $this->theme
        ];

        lmb_StartTransaction();

        if (empty($this->id)) {

            $nextId = next_db_id(self::$tableName);

            $data['ID'] = $nextId;
            //TODO: Web default values
            //WEB1,WEB2,WEB3,WEB4,WEB5,WEB6,WEB7,WEB8,WEB9,WEB10,WEB11,WEB12,WEB13,WEB14
            
            $result = Database::insert(self::$tableName, $data);

            if ($result) {
                $this->id = $nextId;
            }
            
        } else {
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }

        if ($result) {
            lmb_EndTransaction(1);
        } else {
            lmb_EndTransaction(0);
        }
        
        if($this->id !== 0) {
            $this->load();
            $this->generateCss();
        }
        
        return $this->id !== 0;
    }

    
    
    public function load(bool $useDefault = false): void
    {

        $layouts = Layout::getAvailableLayouts();
        
        $this->valid = in_array($this->layout, $layouts);
        
        //load layout specific variables
        if (!$this->valid) {
            return;
        }

        //load variables from inifile
        $iniFile = EXTENSIONSPATH . 'layout/' . $this->layout . '/variables.ini';
        if (!file_exists($iniFile)) {

            $iniFileName = 'variables.ini';
            if ($this->theme === 'dark') {
                $iniFileName = 'variables-dark.ini';
            }

            $iniFile = COREPATH . 'layout/' . $iniFileName;
        }

        if (array_key_exists($iniFile, self::$iniCache)) {
            $this->variables += self::$iniCache[$iniFile];
        } else if (file_exists($iniFile)) {
            $inivars = parse_ini_file($iniFile, true);
            self::$iniCache[$iniFile] = $inivars;
            $this->variables += $inivars;
        }


        //load variables from db
        if (!$useDefault) {
            $rs2 = Database::select('LMB_COLORVARS', where: ['SCHEMA_ID' => $this->id]);
            while (lmbdb_fetch_row($rs2)) {
                $this->edited[lmbdb_result($rs2, 'name')] = lmbdb_result($rs2, 'value');
                $this->customVariables[lmbdb_result($rs2, 'name')] = lmbdb_result($rs2, 'value');
            }
            
            //override defaults
            foreach ($this->variables as $gname => $group) {
                foreach ($group as $key => $value) {
                    if (array_key_exists($key, $this->edited)) {
                        $this->variables[$gname][$key] = $this->edited[$key];
                        unset($this->customVariables[$key]);
                    }
                }
            }
        }
    }


    public function delete(): bool
    {
        Database::delete('LMB_COLORVARS',['SCHEMA_ID' => $this->id]);
        return Database::delete('LMB_COLORSCHEMES',['ID' => $this->id]);
    }

    public function saveColors(?string $addVariableTitle = null, ?string $addVariableValue = null): void
    {
        $request = Request::createFromGlobals();

        $db = Database::get();
        
        $vars = $request->get('var');

        //update legacy vars
        $update = [];
        for ($i = 1; $i <= 14; $i++) {
            $update['WEB' . $i] = $vars["web$i"];
        }

        $update['name'] = $request->get('name');
        $update['theme'] = $request->get('theme');
        if (!empty($update['custom_theme']) && $update['custom_theme'] !== 'light' && $update['custom_theme'] !== 'dark') {
            $update['theme'] = $request->get('custom_theme');
        }

        Database::update('LMB_COLORSCHEMES', $update, ['ID' => $this->id]);
        
        $this->name = $update['name'];
        $this->theme = $update['theme'];

        //check if vars are same as default or edited/added by user
        $toChange = [];
        foreach ($this->variables as $group) {
            foreach ($group as $key => $value) {

                //check if variable was submitted and differs from default
                if (array_key_exists($key, $vars)) {
                    if (strtolower($vars[$key]) != strtolower($value)) {
                        $toChange[strtolower($key)] = strtolower($vars[$key]);
                    }
                    unset($vars[$key]);
                }
            }
        }

        //left variables are not defined in layout.ini
        foreach ($vars as $key => $value) {
            $toChange[$key] = $value;
        }

        //delete all variables that are not assigned anymore
        $sqlquery = 'DELETE FROM LMB_COLORVARS WHERE SCHEMA_ID = ' . $this->id . ' AND NAME NOT IN (\'' . implode("','", array_keys($toChange)) . '\')';
        lmbdb_exec($db, $sqlquery);

        //update all existing variables
        $rs = Database::select('LMB_COLORVARS', ['SCHEMA_ID'=>$this->id]);
        while (lmbdb_fetch_row($rs)) {
            $var = lmbdb_result($rs, 'NAME');
            if (array_key_exists($var, $toChange)) {
                Database::update('LMB_COLORVARS',['VALUE'=>$toChange[$var]],['ID' => lmbdb_result($rs, 'ID')]);
                unset($toChange[$var]);
            }
        }

        //insert another custvar if filled
        if (!empty($addVariableTitle)) {
            $toChange[$addVariableTitle] = $addVariableValue;
        }

        //insert new variables
        foreach ($toChange as $key => $value) {
            $nextId = next_db_id('LMB_COLORVARS');
            Database::insert('LMB_COLORVARS', ['ID'=>$nextId,'SCHEMA_ID'=>$this->id,'NAME'=>$key,'VALUE'=>$value]);
        }
        
        $this->load();
        $this->generateCss();
    }

    public function reset(): bool|string
    {
        Database::delete('LMB_COLORVARS',['SCHEMA_ID' => $this->id]);
        $this->load( true);
        return $this->generateCss();
    }


    public function generateCss(): true|string
    {

        $scssPath = EXTENSIONSPATH . 'layout/' . $this->layout . '/scss/';
        if (!file_exists($scssPath)) {
            $scssPath = COREPATH . 'layout/scss/';
        }
        $bootstrapPath = COREPATH . 'resources/vendor';

        $scss = new Compiler();
        $scss->setImportPaths([$scssPath, $bootstrapPath]);

        # include extensions
        $extScss = '';
        $extScssV = '';
        if ($GLOBALS['gLmbExt']['ext_scssv.inc']) {
            foreach ($GLOBALS['gLmbExt']['ext_scssv.inc'] as $key => $extFile) {
                $extScssV .= file_get_contents($extFile);
            }
        }
        if ($GLOBALS['gLmbExt']['ext_scss.inc']) {
            foreach ($GLOBALS['gLmbExt']['ext_scss.inc'] as $key => $extFile) {
                $extScss .= file_get_contents($extFile);
            }
        }

        //insert edited variables from db
        $extScssV .= "\n\n";
        foreach ($this->edited as $key => $value) {
            $extScssV .= "\$$key: $value;\n";
        }
        $extScssV .= "\n\n";

        foreach ($this->variables['Legacy'] as $key => $value) {
            $key = strtoupper($key);
            $extScssV .= "\$farbschema$key: $value;\n";
        }

        try {

            # include extensions
            $extCss = '';
            if ($GLOBALS['gLmbExt']['ext_css.inc']) {
                $buf = '';
                foreach ($GLOBALS['gLmbExt']['ext_css.inc'] as $key => $extFile) {
                    require($extFile);
                }
                $extCss = $buf;
            }

            $variablesFile = '_lmbvariables';
            if ($this->theme === 'dark') {
                $variablesFile = '_lmbvariables-dark';
            }

            $layoutCss = $scss->compileString(
                '@import "' . $variablesFile . '";
        ' . $extScssV . '
        @import "lmbbootstrap";
        @import "limbas";
        ' . $extScss . ' ' . $extCss
            )->getCss();

            $cssDir = LOCALASSETSPATH . 'css/';
            $filepath = $cssDir . $this->layout . '-' . $this->id . '.css';

            if (!is_writable($cssDir) || (file_exists($filepath) and !is_writable($filepath))) {
                return 'Directory or file not writable (' . $filepath . ')';
            } else {
                file_put_contents($filepath, $layoutCss);
            }

        } catch (SassException $e) {
            $error = 'SCSS parse error: ' . $e->getMessage();
            if (!empty($extCss)) {
                $error .= '<br>Hint: css extensions that use old $farbschema variable must include <span class="font-italic">global $farbschema;</span>';
            }
            return $error;
        } catch (Throwable $t) {
            return 'Error: ' . $t->getMessage();
        }

        return $this->generateLegacyCss();
    }
    private function generateLegacyCss(): true|string
    {
        global $umgvar;


        if ($umgvar['waitsymbol']) {
            $waitsymbol = $umgvar['waitsymbol'];
        } else {
            $waitsymbol = '../images/legacy/wait1.gif';
        }

        $fontc3 = lmbSuggestColor($this->variables['Legacy']['web14'], '222222', '7D7D7D');
        $fontc7 = lmbSuggestColor($this->variables['Legacy']['web7'], '5a5a5a', '333333');
        $fontc14 = lmbSuggestColor($this->variables['Legacy']['web14']);

        $dark_fontc3 = lmbSuggestColor($this->variables['Legacy']['web14'], 'FFFFFF', 'FFFFFF');
        $dark_fontc7 = lmbSuggestColor($this->variables['Legacy']['web7'], 'FFFFFF', 'FFFFFF');
        $dark_fontc14 = lmbSuggestColor($this->variables['Legacy']['web14'], '000000', 'FFFFFF');

        $variables = '';
        for ($i = 1; $i <= 14; $i++) {
            $variables .= '$farbschemaWEB' . $i . ': ' . $this->variables['Legacy']['web' . $i] . ' !default;' . "\n";
        }

        $variables .= '

$waitsymbol: url("' . $waitsymbol . '");

:root,
[data-bs-theme="light"] {
    --lmb-fontc3: ' . $fontc3 . ';
    --lmb-fontc7: ' . $fontc7 . ';
    --lmb-fontc14: ' . $fontc14 . ';
}
[data-bs-theme="dark"] {
    --lmb-fontc3: ' . $dark_fontc3 . ';
    --lmb-fontc7: ' . $dark_fontc7 . ';
    --lmb-fontc14: ' . $dark_fontc14 . ';
}


$sgc1: ' . lmbSuggestColor('74ba6a') . ';
$sgc2: ' . lmbSuggestColor('f09b4d') . ';
$sgc3: ' . lmbSuggestColor('f0cd4d') . ';
$sgc4: ' . lmbSuggestColor('ff725c') . ';
$sgc5: ' . lmbSuggestColor('c593ed') . ';
$sgc6: ' . lmbSuggestColor('32a852') . ';
$sgc7: ' . lmbSuggestColor('74ba6a') . ';
$sgc8: ' . lmbSuggestColor('74ba5c') . ';


';

        //insert edited variables from db
        $variables .= "\n\n";
        foreach ($this->edited as $key => $value) {
            $variables .= "\$$key: $value;\n";
        }
        $variables .= "\n\n";

        $scsspath = EXTENSIONSPATH . 'layout/' . $this->layout . '/scss/';
        if (!file_exists($scsspath)) {
            $scsspath = COREPATH . 'layout/scss/';
        }

        $vendorPath = COREPATH . 'resources/vendor';

        $scss = new Compiler();
        $scss->setImportPaths([$scsspath, $vendorPath]);

        try {

            # include extensions
            $extCss = '';
            if ($GLOBALS['gLmbExt']['ext_css.inc']) {
                $buf = '';
                foreach ($GLOBALS['gLmbExt']['ext_css.inc'] as $key => $extfile) {
                    require($extfile);
                }
                $extCss = $buf;
            }

            $variablesFile = '_lmbvariables';
            if ($this->theme === 'dark') {
                $variablesFile = '_lmbvariables-dark';
            }

            $layoutCss = $scss->compileString(
                '@import "' . $variablesFile . '";
            ' . $variables . '
            @import "legacy";
            ' . $extCss
            )->getCss();

            $cssdir = LOCALASSETSPATH . 'css/';
            $filepath = $cssdir . 'legacy-' . $this->id . '.css';

            if (!is_writable($cssdir) || (file_exists($filepath) and !is_writable($filepath))) {
                return 'Directory or file not writable (' . $filepath . ')';
            } else {
                file_put_contents($filepath, $layoutCss);
            }

        } catch (SassException $e) {
            $error = 'SCSS parse error (legacy): ' . $e->getMessage();
            if (!empty($extCss)) {
                $error .= '<br>Hint: css extensions that use old $farbschema variable must include <span class="font-italic">global $farbschema;</span>';
            }
            return $error;
        } catch (Throwable $t) {
            return 'Error: ' . $t->getMessage();
        }

        return true;
    }
}
