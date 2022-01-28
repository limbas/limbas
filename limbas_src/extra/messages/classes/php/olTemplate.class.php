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

class olTemplate {
	var $buffer, $file;
	var $s = array();
	var $r = array();
	
	function __construct($path="", $nofile = false, $tpl_path = NULL) {
		$this->constructor($path, $nofile, $tpl_path);
	}
	
	function constructor($path="", $nofile = false, $tpl_path = NULL) {
		global $umgvar;

		if (!$tpl_path)
			$tpl_path = $umgvar['path'] . '/extra/messages/templates';
			
		if ($nofile){
			$this->buffer = $path;
			$this->file = 'temporary';
		} else if($path!="") {
			$this->file = $path;
			$f = fopen($tpl_path."/".$this->file, "r");
			
			if (!is_resource($f))
				echo "<strong>Error: template '<i>{$tpl_path}/{$this->file}</i>' not found!</strong>";
			else {
				$this->buffer = "";
				while (!feof($f))
					$this->buffer .= fgets($f, 4096);
				fclose($f);
			}
		}
	}

	function _getindex($s){
		$idx = 0;
		foreach ($this->s as $val){
			if ($val=='/\$\{'.$s.'\}/')
				return $idx;
			$idx++;
		}
		return -1;
	}
	
	function search($s){
		$idx = $this->_getindex($s);
		return ($idx>=0) ? $this->r[$idx] : NULL;
	}

	function add($s, $r){
		if (($i = $this->_getindex($s))<0) {
			array_push($this->s, '/\$\{'.$s.'\}/');
			array_push($this->r, $r);
		} else
			$this->r[$i] = $r;
	}

	function add_array($arr){
		foreach ($arr as $s=>$r)
			$this->add($s, $r);
	}

	function php_eval($buf){
		$php_tag = '/<\?php[[:space:]]*(.*)[[:space:]]*\?>/sm';
		preg_match($php_tag, $buf, $matches, PREG_OFFSET_CAPTURE);
		
		if (isset($matches[1]) && isset($matches[1][0]))
			eval($matches[1][0]);

		return preg_replace($php_tag, '', $buf);
	}

	function clear(){
		if (count($this->s)){
			unset($this->s);
			$this->s=array();
		}
		if (count($this->r)){
			unset($this->r);
			$this->r=array();
		}
	}

	function parse($opt_a = NULL){
		global $gtabid;

		if ($opt_a!=NULL)
			$this->add_array($opt_a);

		$this->add("_path", 'extra/messages/');
		$this->add("_self", 'main_dyns.php?actid=messages&gtabid=' . $gtabid);
		$temp = $this->php_eval($this->buffer);

		return preg_replace($this->s, $this->r, $temp);
	}

	function output($opt_a = NULL){
		echo $this->parse($opt_a);
	}
}
?>
