<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\form;

use Limbas\admin\setup\fonts\Font;
use Limbas\admin\setup\tinymce\TinyMceConfig;

class TinyMce
{
    protected static int $tinyMceCounter = 0;
    
    protected array $options;
    protected ?string $onChangeJs = null;
    
    public function __construct(protected string $elementId, protected ?int $tabId = null, protected ?TinyMceConfig $tinyMceConfig = null)
    {
        static::$tinyMceCounter++;
        $this->loadDefaultOptions();
    }

    
    public function setOptions(array $options, bool $fullReplace = false): void
    {
        if($fullReplace) {
            $this->options = $options;
        }
        else {
            $this->options = array_merge($this->options, $options);
        }
    }

    public function setOption(string $option, mixed $value): void
    {
        $this->options[$option] = $value;
    }
    
    public function getOptions(): array
    {
       return $this->options; 
    }
    
    public function setHeight(int $height): void {
        $this->options['height'] = $height;
    }

    public function setOnChangeEvent(string $javascript): void {
        $this->onChangeJs = $javascript;
    }
    
    
    protected function loadDefaultOptions(): void
    {
        global $umgvar;
        global $gtab;
        global $session;

        // get user language
        $lang = getLangShort();
        if ($lang === 'fr') {
            // french is only available as 'fr_FR'
            $lang = 'fr_FR';
        }

        // read fonts
        $fonts = Font::getUniqueFontFamilies();
        $fontFamilies = [];
        foreach ($fonts as $font) {
            $fontFamilies[] = $font->family . '=' . $font->family;
        }
        $fontList = implode(';', $fontFamilies);

        // layout
        $skin = 'oxide';
        $contentCss = 'default';
        if (lmbIsDarkLayout()) {
            $skin = 'oxide-dark';

            // although user has dark layout, leave content white if the current table is a template table s.t. it looks
            //  consistent with the out coming pdf
            if(empty($this->tabId) || intval($gtab['typ'][$this->tabId]) !== 8)
            {
                $contentCss = 'dark';
            }
        }


        // template wysiwyg extension
        $externalPlugins = [
            'lmbTemplate' => BASE_URL . 'assets/js/extra/template/wysiwyg/lmbTemplate.js'
        ];
        
        $contentStyle = str_replace("\n", ' ', file_get_contents(COREPATH . 'extra/template/wysiwyg/lmbTemplate.css'));

        $this->options = [
            'license_key' => 'gpl',
            'cache_suffix' => '?v=' . $umgvar['version'],
            'language' => $lang,
            'selector' => 'textarea#' . $this->elementId,
            'width' => '100%',
            'font_formats' => $fontList,
            'promotion' => false,
            'auto_focus' => '',
            'skin' => $skin,
            'content_css' => $contentCss,
            'content_style' => $contentStyle,
            'browser_spellcheck' => true,
            'contextmenu' => false,
            'custom_elements' => '~lmb,style', // ~ to behave like span instead of div
            'menubar' => 'file edit view insert format tools table lmbTemplate upload',
            'toolbar' => 'fontsize bold italic alignleft aligncenter alignright alignjustify bullist numlist outdent indent link image forecolor backcolor',
            'menu' => [
                'file' => [
                    'title' => 'File',
                    'items' => 'newdocument | preview | print ',
                ],
                'edit' => [
                    'title' => 'Edit',
                    'items' => 'undo redo | cut copy paste pastetext | selectall | searchreplace',
                ],
                'view' => [
                    'title' => 'View',
                    'items' => 'code | visualaid visualblocks | preview fullscreen',
                ],
                'insert' => [
                    'title' => 'Insert',
                    'items' => 'image link media inserttable | charmap hr | anchor | insertdatetime | lmb-page-break',
                ],
                'format' => [
                    'title' => 'Format',
                    'items' => 'bold italic underline strikethrough superscript subscript codeformat | formats blocks fontfamily fontsize align lineheight | forecolor backcolor | removeformat',
                ],
                'tools' => [
                    'title' => 'Tools',
                    'items' => 'code wordcount',
                ],
                'table' => [
                    'title' => 'Table',
                    'items' => 'inserttable | cell row column | tableprops deletetable | lmb-data-row lmb-data-row-filter',
                ],
                'lmbTemplate' => [
                    'title' => 'Limbas Platzhalter',
                    'items' => 'lmbTemplateData lmbTemplateSubTemplate | lmbTemplateDynamicData lmbTemplateGroup | lmbTemplateFunction lmbTemplateIf lmbTemplateElseIf lmbTemplateElse lmbTemplateEndif | lmbTemplateHeaderFooter lmbTemplateBackground',
                ],
            ],
            'plugins' => 'lmbTemplate advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
            'automatic_uploads' => true,
            'image_uploadtab' => true,
            'images_upload_url' => 'main_dyns.php?actid=manageTemplates&action=wysiwyg&taction=uploadImage&gtabid=' . $this->tabId,
            'extended_valid_elements' => '@[nobr],table[*],lmb[type|name|target|class|title|param|value|src|condition|alt|id]',
            'table_sizing_mode' => 'relative', // for table plugin
            'external_plugins' => $externalPlugins,
            'paste_block_drop' => false,
            'paste_as_text' => true
        ];
        
        
        // check if any user configuration exists
        if(!empty($this->tinyMceConfig)) {
            $tinyMceConfig = $this->tinyMceConfig;
        }
        elseif (!empty($session['settings']['tinymceConfig'])) {
            $tinyMceConfig = TinyMCEConfig::get(intval($session['settings']['tinymceConfig']));
        }
        else {
            $tinyMceConfig = TinyMceConfig::getDefault();
        }

        if(!empty($tinyMceConfig)) {
            $this->setOptions($tinyMceConfig->config);
        }


    }
    
    public function applyTinyMceConfig(TinyMceConfig $tinyMceConfig, bool $fullReplace = false): void
    {
        $this->setOptions($tinyMceConfig->config, $fullReplace);
    }
    
    public function getConfigurationScript(): string
    {
        $options = json_encode($this->options);
        $counter = static::$tinyMceCounter;
        
        $out = <<<EOD
    <script>
        function initTinyMce$counter() {
            const defaults = $options;
            const additionalOptions = {
                setup: function (ed) {
                    ed.on('change', function () {
                        $this->onChangeJs
                    })
                }
            };
            const combinedOptions = { ...defaults, ...additionalOptions };
            tinymce.init(combinedOptions)
        }
        initTinyMce$counter();
    </script>
    EOD;

        return $out;
    }
    
}
