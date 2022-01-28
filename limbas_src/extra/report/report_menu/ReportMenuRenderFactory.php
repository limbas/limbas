<?php


class ReportMenuRenderFactory {

    public static function getMenuRender($menuStyle='old') {
        
        $menuRender = null;
        switch ($menuStyle) {
            case 'bs4':
                $menuRender = new ReportMenuRenderBs4();
                break;
            case 'old':
            default:
                $menuRender = new ReportMenuRenderOld();
                break;
        }
        
        return $menuRender;        
    }
}
