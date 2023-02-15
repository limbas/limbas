<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



class olUpload {
	var $max_file_size = 0;
	var $upload_dir = "./files";
	var $template_path = "./templates";
	var $_self = "";
	var $sid = "";

	function __construct($upload_dir = NULL, $template_path = NULL, $max_file_size = NULL){
		$this->constructor($upload_dir, $template_path, $max_file_size);
	}
	
	function constructor($upload_dir = NULL, $template_path = NULL, $max_file_size = NULL) {
		if ($upload_dir)
			$this->upload_dir = $upload_dir;
			
		$this->max_file_size = ($max_file_size) ? $max_file_size : $this->_get_upload_max_filesize();
	
		if ($template_path)
			$this->template_path = $template_path;
		else
			$this->template_path = dirname($_SERVER['SCRIPT_FILENAME'])."/templates";

		$this->_self = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		$this->sid = session_id(); 	
		//debug_print(__CLASS__."->".__FUNCTION__."();");
	}

	function _sanititize_filename($fname){
		return preg_replace("/(\/)|(\.\.)|(^\~)/", "", $fname);
	}
	
	function _get_upload_max_filesize(){
		$val = trim(ini_get('upload_max_filesize'));
		$last = lmb_strtolower(lmb_substr($val, -1));
	   
		if($last == 'g')
			$val = $val * 1024 * 1024 * 1024;
		if($last == 'm')
			$val = $val * 1024 * 1024;
		if($last == 'k')
			$val = $val * 1024;
		   
		return $val;
	}

	function beautify_filename($fname){
		$s = array('/�/', '/�/', '/�/', '/�/',	'/�/', '/�/', '/�/', '/\s+/');
		$r = array('Ae', 'ae',	'Oe', 'oe',	'Ue', 'ue', 'ss', '_');
		return preg_replace($s, $r, utf8_decode($fname));
	}
	
	function html_code(){
		// TODO: fix this non-flash fallback.
		$tpl = new olTemplate('upload_container.tpl', false, $this->template_path);
		return $tpl->parse(array(
			"self" => $this->_self,
			"max_file_size" => $this->max_file_size,
			"sid" => $this->sid
		));
	}

	function html_header_code($debug=false){
		$tpl = new olTemplate('upload_header.tpl', false, $this->template_path);
		return $tpl->parse(array(
			"self" => $this->_self,
			"max_file_size" => $this->max_file_size,
			"sid" => $this->sid
		));
	}

	function handle() {
		$err = (int)$_FILES['Filedata']['error'];
		switch($err){
			case 0:
				if (isset($_POST['realname']))
					$fname = $_POST['realname'];
				else 
					$fname = $_FILES['Filedata']['name'];
				
				$fname = $this->beautify_filename($this->_sanititize_filename($fname));
				
				if (!move_uploaded_file($_FILES['Filedata']['tmp_name'], "{$this->upload_dir}/$fname")){
					header("HTTP/1.1 500 File upload error");
					echo "File upload error\n";
					return false;
				} 

				chmod("{$this->upload_dir}/$fname", 0600);
				break;

			default:
				header("HTTP/1.1 ".(520+$err)." File Upload Error #$err");
				return false;
		}
		return true;
	}
}
?>
