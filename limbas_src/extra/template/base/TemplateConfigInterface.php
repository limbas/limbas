<?php

interface TemplateConfigInterface {
    
    
    public function getTemplateElementInstance($templateElementGtabid, $name, &$html, $id = 0, $recursion = 0);
    
    
}
