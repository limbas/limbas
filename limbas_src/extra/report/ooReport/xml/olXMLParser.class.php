<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */

define('DEBUG',  false);
define("OLXML_TYPE_TEXT", 0x01);
define("OLXML_TYPE_IMGURL", 0x02);
define("OLXML_TYPE_IMGRES", 0x03);

class olXMLParser {
	var $filename = NULL;
	var $domdoc = NULL;
	var $rowcounter = 0;
	var $template = NULL;
	var $table = array();
	var $firstitem = true;
	
	function __construct($filename = NULL, $template = NULL) {
		$this->constructor($filename, $template);
	}

	function constructor($filename = NULL, $template = NULL) {
		$this->filename = $filename;
		$this->template = $template;
		$this->domdoc = new DOMDocument;
		$this->domdoc->load($this->filename); 
	}
	
	function parse() {
		return $this->_process();
		//return $this->domdoc->saveXML();
	}
	
	function save() {
		$this->_process();
		return $this->domdoc->save($this->filename);
	}
	
	function _process() {
		if (!DEBUG)
			ob_start();
		$this->__process(); 
		if (DEBUG)
			exit(1);
		else
			ob_get_clean();
	}

	function _getAttr($el, $name) {
		foreach($el->attributes as $attr)
			if ($attr->name == $name)
				return $attr->value;
		return "";
	}
	
	function __process() {
		$this->___process($this->domdoc, ""); 
		$this->template->process();
	}
	
	function ___process_text($node, $_V){ 
		$name = $this->_getAttr($node, 'name');
		$type = "TEXT";

		if (lmb_strlen($_V))
			$this->table[$this->rowcounter][$name] = $node->nodeValue;
		else
			$this->template->addText($name, $node->nodeValue);
	}
	
	function ___process_imgurl($node, $_V){ 
		$name = $this->_getAttr($node, 'name');
		$src =  $this->_getAttr($node, 'src');
		$type = "IMGURL";

		if (lmb_strlen($_V))
			$this->table[$this->rowcounter][$name] = $src;
		else
			$this->template->addImage($name, $src);
	}
	
	function ___process_imgres($node, $_V){ 
		$name = $this->_getAttr($node, 'name');
		$src =  $this->_getAttr($node, 'src');
		$width =  $this->_getAttr($node, 'width');
		$height =  $this->_getAttr($node, 'height');
		$type = "IMGRES";

		$info = $this->template->resources->addPictureFile($src);
		if (!$info)
			return;
			
		$_fe = $this->template->createEmbeddedImage($info['href'], $info['width'], $info['height']);
		
		//if ($_fe != NULL)
		//	var_dump($_fe);

		if (lmb_strlen($_V))
			$this->table[$this->rowcounter][$name] = $_fe; /* . (
				($width && $height) ? " ({$width}x{$height})" : ""
			);*/
		else
			$this->template->addElement($name, $_fe);
	}
	
	function ___process_table(&$node){ 
		$name = $this->_getAttr($node, 'name');
		$this->rowcounter = -1;
			
		$this->___process($node, $name);
				
		$this->template->addTable($name, $this->table);
		
		unset($this->table);
		$this->table = array();
		
		$node = $node->nextSibling; // skip the rest of the table
	}
	
	function ___process($domnode, $_V) { 
		$domnode = $domnode->firstChild; 
		while (!is_null($domnode)) {
			if ($domnode->nodeType == XML_ELEMENT_NODE){
				switch($domnode->nodeName){
					case 'report':
						$name = $this->_getAttr($domnode, 'name');
						//echo "<h1>{$name}</h1>\n";
						break;
					case 'item':
						if ($this->firstitem)
							$this->firstitem = false;
						else
							$this->template->process();
						break;
					case 'row':
						$this->rowcounter++;
						break;
					case 'text':
						$this->___process_text($domnode, $_V);
						break;
					case 'imgurl':
						$this->___process_imgurl($domnode, $_V);
						break;
					case 'imgres':
						$this->___process_imgres($domnode, $_V);
						break;
					case 'table':
						$this->___process_table($domnode, $_V);
						break;
					default:
						echo "$_V...: ".$domnode->nodeName."<br>";
				}
			}
			if ($domnode->hasChildNodes())
				$this->___process($domnode, $_V);
			$domnode = $domnode->nextSibling;
		}	
	}
}
?>
