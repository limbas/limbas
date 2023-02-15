<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
			$tpl_path = COREPATH . 'extra/messages/templates';
			
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
		if (lmb_count($this->s)){
			unset($this->s);
			$this->s=array();
		}
		if (lmb_count($this->r)){
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
