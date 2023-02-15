<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


 
define("IMGTYPE_JPEG", "jpg");
define("IMGTYPE_GIF", "gif");
define("IMGTYPE_PNG", "png");

class olODFPicture {
	var $docdir = NULL;
	var $docManifest = NULL;
	
	var $supported_types = array(
		IMGTYPE_PNG => "image/png",
		IMGTYPE_GIF => "image/gif",
		IMGTYPE_JPEG => "image/jpeg"
	);
	
	function __construct($docdir = NULL) {
		$this->constructor($docdir);
	}

	function constructor($docdir = NULL) {
		$this->docdir = $docdir;
		
		$this->docManifest = new DOMDocument;
		$this->docManifest->load("{$this->docdir}META-INF/manifest.xml");
	}
	
	function _create_unique_filename(){
		$ts = gettimeofday();
		return sprintf("olODFPicture_%012X%06X", $ts['sec'], $ts['usec']);
	}
	
	function _getImageType($fname){
		$ret = array();
		$ret['suffix'] = IMGTYPE_JPEG;
		
		$res = @imagecreatefromjpeg($fname);
		
		if (!$res){
			$ret['suffix'] = IMGTYPE_PNG;
			$res = @imagecreatefrompng($fname);
		}

		if (!$res){
			$ret['suffix'] = IMGTYPE_GIF;
			$res = @imagecreatefromgif($fname);
		}
		
		if (!$res)
			return NULL;
			
		$ret['media-type'] = $this->supported_types[$ret['suffix']];
		
		// convert pixel to cm (96 dpi)
		$ret['width']  = sprintf("%.3fcm", imagesx($res) * 2.54 / 96.0 );
		$ret['height'] = sprintf("%.3fcm", imagesy($res) * 2.54 / 96.0 );
		
		imagedestroy($res);

		return $ret;
	}

	function _copyfile($source_fname, $destination_fname) {
		$s = fopen($source_fname, "rb");
		if (!is_resource($s))
			return false;
			
		$d = fopen($destination_fname, "wb");
		if (!is_resource($d)){
			fclose($s);
			return false;
		}

		while (!feof($s)){
			$buf = fread($s, 512);
			fwrite($d, $buf);
		}
		
		fclose($d);
		fclose($s);
		return true;
	}
	
	function addPictureFile($file, $path = 'Pictures/') {
		
		if (!($info = $this->_getImageType($file)))
			return NULL; // unsupported (not jpeg, gif or png) or damaged image file. 

		if (!$this->_meta_findFileEntry($path))
			$this->_meta_addFileEntry($path);

		if (!file_exists($this->docdir.$path))
			if (!mkdir($this->docdir.$path))
				return NULL; // could not create folder $path
	
		$info['href'] = $path.$this->_create_unique_filename().'.'.$info['suffix'];
		$this->_meta_addFileEntry($info['href'], $info['media-type']);
	
		if (!$this->_copyfile($file, $this->docdir.$info['href']))
			return NULL; // error creating file entry $info['href']
	
		if (!$this->docManifest->save("{$this->docdir}META-INF/manifest.xml"))
			return NULL; // error saving manifest
	
		unset($info['suffix']);
		return $info;
	}
	
	function _meta_findFileEntry($full_path) { 
		$n = $this->docManifest->firstChild->firstChild; 
		while (!is_null($n)) {
			if ($n->nodeType == XML_ELEMENT_NODE)
				if($n->nodeName == 'manifest:file-entry')
					if ($n->getAttribute("manifest:full-path") == $full_path)
						return true;
			$n = $n->nextSibling;
		}	
		return false;
	}
	
	function _meta_addFileEntry($full_path, $media_type = "") {		
		$el = $this->docManifest->createElement('manifest:file-entry');
		$el->setAttribute("manifest:media-type", $media_type);
		$el->setAttribute("manifest:full-path", $full_path);
		$this->docManifest->firstChild->appendChild($el);
	}
}
?>
