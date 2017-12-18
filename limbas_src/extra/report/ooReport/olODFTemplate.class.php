<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */
 
define('DEBUG_olODFTemplate',  0);
define ('STYLE_NAME_PAGEBREAK', 'olPageBreak');

class olODFTemplate {
	var $container = NULL;
	var $filename = NULL;
	var $domdoc = NULL;
	var $domBlock = NULL;
	var $domCurrent = NULL;
	var $resources = NULL;
	var $tables = array();
	var $texts = array();
	var $elements = array();
	var $images = array();
	
	function olODFTemplate($container) {
		$this->constructor($container);
	}

	function constructor($container) {
		$this->container = $container;
		$this->container->ExtractDocumentFiles();
		$this->filename = $container->docdir . PATHSEP . "content.xml";

		$this->resources = new olODFPicture($container->docdir . PATHSEP);

		$this->domdoc = new DOMDocument;
		$this->domdoc->load($this->filename); 

		$this->_block_readin();
		$this->_insertPageBreakStyle();
		$this->_block_current();
	}
	
	function _block_current() {
		$this->domCurrent = $this->domBlock->cloneNode(true);
	}
	
	function _block_readin() {
		if (($_textNode = $this->_getNode("text")) == NULL)
			return false;
			
		$this->domBlock = $_textNode->cloneNode(true);
		$this->domBlock->appendChild($this->_createPageBreakElement());
		
		$_textNode->parentNode->removeChild($_textNode);
		return true;
	}
	
	function addTable($name, $value)	{ $this->tables[$name] = $value; }
	function addText($name, $value)		{ $this->texts[$name] = $value; }
	function addElement($name, $value)	{ $this->elements[$name] = $value; }
	function addImage($name, $value)	{ $this->images[$name] = $value; }

	function parse() {
		$this->process();
		return $this->domdoc->saveXML($this->domdoc->firstChild);
	}
	
	function save() {
		//$this->process();
		return $this->domdoc->save($this->filename);
	}
	
	function process() {
		if (!DEBUG_olODFTemplate)
			ob_start();
			
		$this->_block_current();

		$this->_processTables(); 
		$this->_processTextsAndImages(); 
		
		if (($_textNode = $this->_getNode("body")) != NULL)
			$_textNode->appendChild($this->domCurrent->cloneNode(true));

		if (DEBUG_olODFTemplate)
			exit(1);
		else
			ob_get_clean();
	}

	function _getNode($name) {
		$el = $this->domdoc->getElementsByTagName($name);
		return ($el->length) ? $el->item(0) : NULL;
	}
	
	function _createPageBreakElement() {
		$el = $this->domdoc->createElement("text:p");
		$el->setAttribute("text:style-name", STYLE_NAME_PAGEBREAK);
		return $el;
	}
	
	function _insertPageBreakStyle() {
		$as = $this->domdoc->getElementsByTagName("automatic-styles");
		if (!$as->length)
			return;
		$s = $this->domdoc->createElement("style:style");
		$p = $this->domdoc->createElement("style:paragraph-properties");

		$s->setAttribute("style:name", STYLE_NAME_PAGEBREAK);
		$s->setAttribute("style:family", "paragraph");
		$s->setAttribute("style:parent-style-name", "Standard");
		$p->setAttribute("fo:break-after", "page");
 
		$s->appendChild($p);
		$as->item(0)->appendChild($s);
	}

	function _subImages(&$data, $row, $cell) { 
		$this->__subImages($data, $row, $cell);
	}
	
	function __subImages(&$data, $row, $domnode) { 
		$domnode = $domnode->firstChild; 
		while (!is_null($domnode)) {
			if($domnode->nodeType == XML_ELEMENT_NODE){
				switch($domnode->nodeName){
					case 'draw:image':
						$href = $domnode->getAttribute('xlink:href');
						echo "xx... ".$domnode->nodeName." href='$href'<br>\n";
						
						$var = preg_replace('/template:\/\/\$%7B(.*)%7D/i', "$1", $href);
						$tbl = preg_replace('/([a-z0-9_]*):[a-z0-9_]*/i', "$1", $var);
						$idx = preg_replace('/[a-z0-9_]*:([a-z0-9_]*)/i', "$1", $var);
						
						echo "+++ {$tbl}[{$row}][{$idx}] : '{$data[$idx]}'<br>\n";
						$domnode->setAttribute('xlink:href', $data[$idx]);
						break;
					default:
						echo "xx... ".$domnode->nodeName."<br>\n";
				}
			}
			
			if ($domnode->hasChildNodes())
				$this->__subImages($data, $row, $domnode);
				
			$domnode = $domnode->nextSibling;
		}
	}
	
	function _processTextsAndImages() { 
		$this->__processTextsAndImages($this->domCurrent);
	}
	
	function __processTextsAndImages($domnode) { 
		$domnode = $domnode->firstChild; 
		
		while (!is_null($domnode)) {
			if (trim($domnode->nodeValue) != "") {
				switch($domnode->nodeType){
					case XML_ELEMENT_NODE:
						echo "... ".$domnode->nodeName."<br>\n";
						if ($domnode->nodeName == 'draw:frame'){ // TODO: fix firstchild
							foreach($domnode->firstChild->attributes as $attr){
								if ($attr->name != 'href')
									continue;
								
								if (!preg_match('/^template:\/\/\$%7B.*%7D/i', $attr->value))
									continue;
									
								$var = preg_replace('/^template:\/\/\$%7B(.*)%7D/i',
									"$1", $attr->value);
								
								$attr->value = $this->images[$var];
								echo "!!! {$attr->name} == $var <br>\n";
								break;
							}
						}
						break;

					case XML_TEXT_NODE:
						if (preg_match('/^\${{.*}}$/', $domnode->nodeValue)){
							$name = preg_replace('/\${{(.*)}}/', '$1', $domnode->nodeValue);
							
							if (!isset($this->elements[$name]))
								break;
							$_newnode = $this->elements[$name]->cloneNode(true);
							$domnode->parentNode->replaceChild($_newnode, $domnode);
						} elseif (preg_match('/\${.*}/', $domnode->nodeValue)){
							$tpl = new olTemplate($domnode->nodeValue, true);
							$domnode->nodeValue = $tpl->parse($this->texts);
							unset($tpl);
							//$this->_createPageBreakElement($domnode->parentNode);
						/*} elseif (preg_match('/lltext/', $domnode->nodeValue)){
							$pn = $domnode->parentNode;
							$pn->parentNode->insertBefore($this->_createPageBreakElement(), $pn);*/
						}
						break;
				}
				if ($domnode->hasChildNodes())
					$this->__processTextsAndImages($domnode);
			}
			$domnode = $domnode->nextSibling;
		}
	}
	
	function _processTables() {
		foreach($this->tables as $n=>$v){
			echo "\$this->_processTable(\$this->domCurrent, $n, $v);<br>\n";
			$this->_processTable($this->domCurrent, $n, $v);
		}
	}

	function _processTable($domnode, $name, &$array) { 
	  $array_ptr = &$array; 
	  
	  $domnode = $domnode->firstChild; 
	  
	  while (!is_null($domnode)) { 
		if (! (trim($domnode->nodeValue) == "") ) { 
		  if ($domnode->nodeType == XML_ELEMENT_NODE) { 
			  /* search for table-rows */
			  if ($domnode->nodeName=="table:table-row"){
				//echo "ROW: ".$domnode->nodeName." [".$domnode->nodeValue."]<br>";
	//			var_dump($domnode);
				
				/* search for table-cells containing template variables within the row */
				$nrow_cells = array();
				foreach($domnode->childNodes as $n => $v){
					if ($v->nodeName == 'table:table-cell'){
						if (preg_match('/\${.*}/', $v->firstChild->nodeValue))
							array_push($nrow_cells, $v->cloneNode(true));
	//					echo "#$n:".$v->nodeName.":".$v->nodeValue."<br>\n";//var_dump($n);
					}
				}
				
				/* duplicate current table-row and fillin table-cells with the 
				 * substituted content. */
				if (count($nrow_cells)){
//					echo "+++ outputting ".count($nrow_cells)." x ".count($array_ptr)." table '{$name}'...<br>\n";
					$row_idx = 0;
					$nextrow = $domnode->nextSibling;
					foreach ($array_ptr as $i){
						$nrow = $domnode->cloneNode(false);
						$col_idx = 0;
						$_done=false;
						
						foreach($nrow_cells as $cell){
							$ncell = $cell->cloneNode(true);
							$var = preg_replace('/.*(\${.*}).*/', "$1", $ncell->firstChild->nodeValue);
							$var_name = preg_replace('/\${([a-z0-9_]*):[a-z0-9_]*}/i', "$1", $var);
							$var_index = preg_replace('/\${[a-z0-9_]*:([a-z0-9_]*)}/i', "$1", $var);
							if ($var_name == $name){
								echo "******** ".(is_string($i[$var_index]) ? 'TRUE' : 'FALSE')." *********<br>\n";
								if (is_string($i[$var_index])){
									$ncell->firstChild->nodeValue = 
										preg_replace('/\${.*}/', trim($i[$var_index]),
											$ncell->firstChild->nodeValue);
										echo "+++ ($col_idx) {$var_name}[{$row_idx}][{$var_index}] : '{$i[$var_index]}'<br>\n";
								} elseif (is_object($i[$var_index])){
									$_newnode = $i[$var_index]->cloneNode(true);
									$ncell->firstChild->nodeValue = '';
									$ncell->firstChild->appendChild($_newnode);
									//$ncell->replaceChild($_newnode, $ncell->firstChild);
								}
								$_done = true;
							} else
								$this->_subImages($i, $row_idx, $ncell);

							$nrow->appendChild($ncell);
							$col_idx++;
						}
						
						/*if ($var_name != $name){
								echo "+++ ($var != $name) - ".$row_idx.":".$col_idx."<br>\n";
							break;
						}*/
						if (!$_done)
							break;
							
						if ($nextrow){
							echo "nextSibling: '".$domnode->nextSibling->firstChild->firstChild->nodeValue."'<br>";
							$domnode->parentNode->insertBefore($nrow, $nextrow);
						} else
							$domnode->parentNode->appendChild($nrow);
						$row_idx++;
						unset($nrow);
					}
					$pn = $domnode->parentNode;
					if ($_done){
						$pn->removeChild($domnode); // remove template row itself
						echo "------------- template row removed.<br>\n";
						//$domnode = $pn->nextSibling; // skip the rest of the table
						//$domnode = $this->domBlock->lastChild; // skip the rest of the table
						//$domnode = $this->domBlock->firstChild; 
						return;
					}
//					echo "#####################".$pn->nodeValue."<br>\n";
					//$domnode = $this->domBlock->lastChild; // skip the rest of the table
				}
			  }
		  } 
		  if ( $domnode->hasChildNodes() )
			$this->_processTable($domnode, $name, $array_ptr); 
		} 
		$domnode = $domnode->nextSibling; 
	  } 
	} 

	function createEmbeddedImage($href, $width, $height){
		$frm = $this->domdoc->createElement('draw:frame');
		$frm->setAttribute("text:anchor-type", "paragraph");
		$frm->setAttribute("svg:width", $width);
		$frm->setAttribute("svg:height", $height);
		$img = $this->domdoc->createElement('draw:image');
		$img->setAttribute("xlink:href", $href);
		$img->setAttribute("xlink:type", "simple");
		$img->setAttribute("xlink:show", "embed");
		$img->setAttribute("xlink:actuate", "onLoad");
		$frm->appendChild($img);
		return $frm;
	}
	
/*	function ___________pxtocm_96dpi($px){
		return ($px / 96.0 * 2.54)."cm";
	}

	function _______createEmbeddedImage2($url, $title=NULL, $size=NULL){
		if (!$title)
			$title = $url;
			
		$ret = $this->domBlock->createElementNS(
			"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0", 'frame');
			
		$ret->setAttribute('text:anchor-type', "as-char");
		if ($size){
			$ret->setAttribute('svg:width', $this->_pxtocm_96dpi($size[0]));
			$ret->setAttribute('svg:height', $this->_pxtocm_96dpi($size[1]));
		}		
		
		$el_image = $this->domBlock->createElementNS(
			"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0", 'image');
			
		$_attr = array(
			'xlink:type' => "simple",
			'xlink:show' => "embed",
			'xlink:actuate' => "onLoad",
		);

		foreach($_attr as $_n=>$_v)
			$el_image->setAttribute($_n, $_v);
			
		$el_image->setAttribute('xlink:href', $url);

		$el_title = $this->domBlock->createElementNS(
			"urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0", 'title', $title);
			
		$ret->appendChild($el_image);
		$ret->appendChild($el_title);
		
		return $ret;
	}*/
}
?>
