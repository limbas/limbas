<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace gtab\forms;


class LMBAction
{
    private $actionId;
    
    private $forbidden;
    
    private $gtabid;

    private $datid;

    private $gresult;
    
    private $hasData;
    
    
    public function __construct(int $actionId) {
        global $LINK;

        $this->actionId = $actionId;
        
        if (!empty($this->actionId) && !$LINK[$this->actionId]) {
            $this->forbidden = true;
        }
        
    }
    
    
    
    public function render($type,$active=false,$event=null,$class='') {
        
        $output = '';
        
        if (!$this->forbidden) {
            $output = match ($type) {
                'icon' => $this->renderIcon($active,$event),
                'dropdown' => $this->renderDropdown($active,$event,$class),
            };
        }
        
               
        return $output;
    }

    private function renderIcon($active=false,$event=null,$class='nav-link') {
        global $LINK;
        global $lang;
        
        if (!$event) {
            $event = 'onclick="' . $LINK['link_url'][$this->actionId] . '"';
        }
        
        $icon = $LINK['icon_url'][$this->actionId];

        return '<a ' . $event . ' class="' . $class . ' ' . (($active) ? 'active' : '') . '" href="#" title="' . $lang[$LINK['desc'][$this->actionId]] . '"><i class="lmb-icon ' . $icon . '"></i></a>';
    }

    private function renderDropdown($active=false,$event=null,$class='nav-link') {
        global $LINK;
        global $lang;

        if($LINK[$this->actionId] || $this->actionId == "0") {
            $id = intval($id);
            $tmp = $zv ? $zv : $lang[$LINK["name"][$id]];

            if ($active) {
                $activeicon = "<i class=\"lmbContextRight lmb-icon lmb-check\" border=\"0\"></i>";
                #$acst = "style=\"color:#AAAAAA\"";
                #$acst = "style=\"font-weight:bold;\"";
            }

            $class = "";
            $img = "";

            if ($imgclass) {
                $img = "<i class=\"lmbContextLeft lmb-icon $imgclass\" border=\"0\"></i>";
                $class = "Icon";
            } elseif ($LINK["icon_url"][$id]) {
                $img = "<i class=\"lmbContextLeft lmb-icon {$LINK["icon_url"][$id]}\" border=\"0\"></i>";
                $class = "Icon";
            }

            echo <<<EOD
        <a class="lmbContextLink" onclick="{$zl}{$LINK["link_url"][$id]};return false;" title="{$lang[$LINK["desc"][$id]]}">
            $img
            <span class="lmbContextItem$class" ID="pop_menu_{$id}" $acst>$tmp</span>
            $activeicon
        </a>
EOD;

        }
        return '<a ' . $event . ' class="' . $class . ' ' . (($active) ? 'active' : '') . '" href="#" title="' . $lang[$LINK['desc'][$this->actionId]] . '"><i class="lmb-icon ' . $icon . '"></i></a>';
    }
    

    public function assignData(int $gtabid, int $datid, array $gresult)
    {
        $this->gtabid = $gtabid;
        $this->datid = $datid;
        $this->gresult = $gresult;
        $this->hasData = true;
    }


    /**
     * Render 
     * 
     * @param int $actionId
     * @param string $type
     * @param bool $active
     * @param string|null $event
     * @param string $class
     * @return string
     */
    public static function ren(int $actionId, string $type, bool $active=false, string $event=null, string $class='' ) {
        return self::get($actionId)->render($type, $active, $event, $class);
    }
    
    
    public static function get( int $actionId ): LMBAction|null
    {

        return new self( $actionId );
        
        $className = match ($actionId) {
            1 => 'Text',
            10 => 'Textblock',
            11 => 'Datetime',
            //12 => 'Auswahl (Select)',
            13 => 'Upload',
            14 => 'Auswahl (Radio)',
            15 => 'PHP-Argument',
            16 => 'Zahl',
            18 => 'Auswahl (Checkbox)',
            19 => 'Numerische Kommazahl',
            20 => 'Boolean',
            21 => 'Numerische Kommazahl (Prozent)',
            22 => 'Auto-ID',
            23 => 'Verknüpfung rückwertig',
            24 => 'Verknüpfung n:m',
            25 => 'Verknüpfung 1:n direkt',
            26 => 'Zeit',
            default => 'Text',
        };

        if (empty($className)) {
            return null;
        }

        $className = 'gtab\\forms\\actions\\Action' . $className;

        if (!class_exists($className)) {
            $file = __DIR__ . '/' . $className . '.php';
            if (!file_exists($file)) {
                return null;
            }
            require_once __DIR__ . '/' . $className . '.php';
        }



        return new $className();
        
    }
    
}
