<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */

class olMailTreeList{
	var $all = array();
	
	function __construct() {
		$this->constructor();
	}

	function constructor() {}

	function destructor() {}
	
	function output(){
		echo $this->_list('');
	}

	function add($folder, $delim, $data){
		$folder = preg_replace('/^.*\}/', "", $folder);
		$parent = preg_replace('/\.$/', "", $folder);
		
		$x = explode($delim, $folder);
		array_pop($x);
		$parent = implode($delim, $x);
			
		$found = false;
		foreach($this->all as $n=>$v){
			if ($n==$parent){
				$this->all[$n][$folder]=$data;
				$found = true;
			}
		}
		if (!$found)
			$this->all[$parent] = array($folder=>$data);
	}

	function quote_utf8($s){
		return "=?utf-8?b?".base64_encode($s)."?=";
	}
	
	function _make_mailaddr_cb($name, $addr){
		return $this->quote_utf8($name)." <{$addr}>";
	}

	function make_mailaddr($addr){
	    return preg_replace_callback('/(.*) <(.*)>/', function($matches) {
            return $this->_make_mailaddr_cb($matches[0], $matches[1]);
        }, $addr); // TODO not sure if correct
//	    return preg_replace('/(.*) <(.*)>/e', "\$this->_make_mailaddr_cb(\"$1\", \"$2\")", $addr);
	}

	function utf7toutf8($s){
		/* provide a fallback for an latin-1 -> utf-8 exclusive encoding,
		 * in case the multibyte string (mbstring) functions are not available. */
		if (!function_exists("mb_convert_encoding"))
			return $s;//utf8_encode(imap_utf7_decode($s));
		return mb_convert_encoding($s, "UTF-8", "UTF7-IMAP");
	}
	
	function _list($fn,$sub=''){
		if ((!isset($this->all[$fn])) || (!count($this->all[$fn])))
			return;
			
		$folders='';
		$tpl = new olTemplate('folderlist.tpl');
                
                $last = count($this->all[$fn]);
                $count = 1;                
		foreach ($this->all[$fn] as $n=>$v){
			$subfolders = isset($this->all[$n]) ? count($this->all[$n]) : 0;
			$namepart = preg_replace('/.*\./',"", $this->utf7toutf8($n));
                        //<img src="pic/outliner/blank.gif">
                        //<img src="pic/outliner/joinbottom.gif">
			$toggle = ($subfolders>0) ? 
				"<img src=\"pic/outliner/plusonly.gif\" class=\"mail_toggle\" onclick=\"return mail_foldertoggle('$n')\" id=\"toggle_$n\"/>" : //"<div class=\"mail_toggle\" onclick=\"return mail_foldertoggle('$n')\" id=\"toggle_$n\">-</div>"
				"<img src=\"pic/outliner/".((($count == 1 && $sub == '')) ? 'jointop' : 'joinbottom').".gif\" id=\"toggle_$n\"/>";

			$tpl_row = new olTemplate('folderlist_row.tpl');
			$folders .= $tpl_row->parse(array(
                                "sub" => $sub,
                                "display" => ($sub == '') ? '' : 'display:none',
                                "style1" => (($count == 1 || $count == $last) && $last != 2  ) ? '' : 'background-image: url(\'pic/outliner/line.gif\'); background-repeat: repeat-y;',
                                "style2" => (($subfolders <= 0 && $count == $last)) ? '' : 'background-image: url(\'pic/outliner/line.gif\'); background-repeat: repeat-y;',
				"n" => $n, "mbox" => urlencode($n),
				"class" => ($v->Unread) ? 'mail_unread' : 'mail',
				"namepart" => ($v->Unread) ? "$namepart ({$v->Unread})" : $namepart,
				"next" => $this->_list($n,'foldinglist'),
				"toggle" => $toggle
			));
                           
                         $count++;       
		}
		return $tpl->parse(array("folders" => $folders));
	}
}
?>