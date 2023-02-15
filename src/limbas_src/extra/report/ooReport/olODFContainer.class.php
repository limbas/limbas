<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



class olODFContainer {
	var $name = NULL;
	var $suffix = 'odt';
	var $tempdir = 'temp';
	var $docdir = 'temp/doc';
	
	function __construct() {
		$this->constructor();
	}

	function constructor() {
	}

	/* get name of the (first) document template in tempdir */
	function getDocumentName($include_path = false){
		if (!$this->name)
			$this->name = $this->_getDocumentName();

		return ($include_path)
			? $this->tempdir . PATHSEP . $this->name
			: $this->name;
	}

	function _getDocumentName(){
		$ret = NULL;
		if (is_resource($d = opendir($this->tempdir))){
			while (($f = readdir($d))!==false)
				if (preg_match('/\.'.$this->suffix.'$/i', $f)){
					$ret = $f;
					break;
				}
			closedir($d);
		}
		return $ret;
	}
	
	/* extract files from document */
	function ExtractDocumentFiles($fname = NULL){
		if (!$fname)
			$fname = $this->tempdir . PATHSEP . $this->getDocumentName();
			
		/* cleanup document directory */
		echo `rm -r {$this->docdir}`;
		mkdir($this->docdir);

		$ret = `unzip -q $fname -d {$this->docdir} && echo ok || echo failed extracting document`;
		if (!preg_match('/^ok/i', $ret))
			return $ret;

		return NULL;
	}

	/* compress files into a document */
	function CompressDocument($fname = NULL){
		if ($fname){
			@unlink($fname); 
			$fname = '..' . PATHSEP . $fname;
		}
		
		chdir($this->docdir);
		
		if ($fname)
			$ret = `zip -Dqr $fname . && echo ok`;
		else {
			system("zip -Dqr - .");
			$ret = "ok";
		}
		
		chdir('..'. PATHSEP .'..');
		
		if (!preg_match('/^ok/i', $ret)){
//			echo "ERROR: $ret";
			return false;
		}
		
		return true;
	}

	/* cleanup tempdir, upload and extract document */
	function upload($file){
		switch ($file['error']){
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return "file size exceeded";
			case UPLOAD_ERR_NO_FILE:
				return "choose file first";
			default:
				return "error #{$file['error']}";
		}

		if (!preg_match('/\.'.$this->suffix.'$/', $file['name']))
			return "only documents with the extension '.{$this->suffix}' are supported!";
		
		$this->_cleanup();

		$ret = move_uploaded_file($file['tmp_name'],
				$this->tempdir . PATHSEP . basename($file['name']));

		if ($ret)
			$ret = $this->ExtractDocumentFiles();
		else
			$ret = "security failure";
		
		return ($ret) ? $ret : NULL; 
	}
	
	function download($filename = NULL, $include_headers = true){
		$outfile = $filename ? $filename : $this->getDocumentName();
		
		if ($include_headers){
			header("Content-type: application/vnd.oasis.opendocument.text");
			header("Content-Disposition: attachment; filename=\"{$outfile}\"");
		}

		return $this->CompressDocument(NULL);
	}

	function save($filename = NULL){
		$outfile = $filename ? $filename : $this->getDocumentName();
		return $this->CompressDocument($outfile.".zip");
	}

	/* delete all documents in tempdir */
	function _cleanup(){
		$d = opendir($this->tempdir);
		if ($d){
			while (($f=readdir($d)) !== false){
				if ( !preg_match('/\.'.$this->suffix.'$/i', $f) )
					continue;
				@unlink($this->tempdir . PATHSEP . $f);
			}
			closedir($d);
		}
	}
}

?>
