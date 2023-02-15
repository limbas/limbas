<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/* USAGE EXAMPLE: */
/*
	$mm = new olMimeMessage(
		"From: me <me@example.com>\n".
		"To: you <you@example.com>\n".
		"Subject: mime testmessage\n"
	);
	
	$mm->addHeader("X-Optional-Header: blah...\n");
	$mm->addAttachment($message, true);   // attach inline message
	$mm->addAttachment("screenshot.png"); // attach file
	$mm->addBody("... a cute, little testmessage.\n");
	
	$mm->output();
*/
class olMimeMessage {
	var $_header = "";
	var $_body = "";
	var $_parts = "";
	var $_multipart = false;
	var $_boundary = "";
	
	function __construct($header = NULL, $body = NULL, $attachments = NULL) {
		$this->constructor($header, $body, $attachments);
	}
	
	function constructor($header = NULL, $body = NULL, $attachments = NULL) {
		$this->_boundary = $this->_mime_boundary();
		
		if ($header)
			$this->_header = $header;
			
		if ($body)
			$this->_body = $body;
			
		if ($attachments && lmb_count($attachments))
			foreach($attachments as $type=>$data)
				$this->addAttachment($type, $data);
	}

	function addHeader($header){
		$this->_header .= $header;
	}
	
	function addBody($body){
		$this->_body .= $body;
	}
	
	function addAttachment($filename, $path){
		switch(lmb_substr(lmb_strrchr($filename, '.'), 1)){
			case 'png':
				$CONTENT_TYPE = 'image/png';
				$CONTENT_DISPOSITION = 'inline';
				$TRANSFER_ENCODING = 'base64';
				break;
			case 'gif':
				$CONTENT_TYPE = 'image/gif';
				$CONTENT_DISPOSITION = 'inline';
				$TRANSFER_ENCODING = 'base64';
				break;
			case 'jpg':
			case 'jpeg':
				$CONTENT_TYPE = 'image/jpeg';
				$CONTENT_DISPOSITION = 'inline';
				$TRANSFER_ENCODING = 'base64';
				break;
			case 'eml':
				$CONTENT_TYPE = 'message/rfc822';
				$CONTENT_DISPOSITION = 'inline';
				$TRANSFER_ENCODING = '8bit';
				break;
			default:
				$CONTENT_TYPE = 'application/binary';
				$CONTENT_DISPOSITION = 'attachment';
				$TRANSFER_ENCODING = 'base64';
		}
		
		/* read the whole file into buffer or fail. */
		if (!($f = fopen($path."/".$filename, "rb")))
			return false;
		$buf = "";
		while(!feof($f)) /* TODO: handle file read errors */
			$buf .= fread($f, 4096);
		fclose($f);
		
		if ($TRANSFER_ENCODING == 'base64')
			$buf = imap_binary($buf);

		/* prepare a properly encoded name and filename...  */
		$_name = '=?UTF-8?B?'.base64_encode($filename).'?=';
		
		$_filename = "UTF-8''"; /* TODO: fix syntax for long filenames */
		for ($i=0; $i<lmb_strlen($filename); $i++)
			$_filename .= sprintf("%%%02X", ord(lmb_substr($filename, $i, 1)));

		/* ... and output part header and body. */
		$this->_parts .= "--{$this->_boundary}\n".
			"Content-Type: {$CONTENT_TYPE};\n name=\"{$_name}\"\n".
			"Content-Transfer-Encoding: {$TRANSFER_ENCODING}\n".
			"Content-Disposition: {$CONTENT_DISPOSITION};\n filename*={$_filename}\n\n";
			
		$this->_parts .= $buf."\n";
		$this->_multipart = true;
		return true;
	}

	function prepare(&$header){
		if ($this->_multipart){
			$this->_header .= "MIME-Version: 1.0\n"; /* mandatory for multi-part MIME message */
			$this->_header .= "Content-Type: multipart/mixed;\n boundary=\"{$this->_boundary}\"\n";

			/* include an informal message for mail clients which are not MIME capable */
			$this->_body = "This is a multi-part message in MIME format.\n\n".
				"--{$this->_boundary}\n".
				"Content-Type: text/plain; charset=UTF-8; format=flowed\n".
				"Content-Transfer-Encoding: 8bit\n\n".$this->_body;
		} else {
			$this->_header .= "Content-Type: text/plain; charset=UTF-8; format=flowed\n";
			$this->_header .= "Content-Transfer-Encoding: 8bit\n";
		}
		
		if (!preg_match('/^X-IP-Address:/i', $this->_header))
			$this->_header .= "X-IP-Address: {$_SERVER['REMOTE_ADDR']}\n";
		
		$header = $this->_header;
		return $this->_body.(($this->_multipart) ? "\n{$this->_parts}\n--{$this->_boundary}--\n" : "\n");
	}

	function output(){
		$header = "";
		$body = $this->prepare($header);
		echo $header."\n".$body;
	}
	
	function _mime_boundary(){
		mt_srand( intval( microtime( true ) * 1000 ) );
		$b = md5( uniqid( mt_rand(), true ), true );
		
		$b[6] = chr( ( ord( $b[6] ) & 0x0F ) | 0x40 );
		$b[8] = chr( ( ord( $b[8] ) & 0x3F ) | 0x80 );
		
		return implode('-', unpack( 'H32', $b ) );
	}
}
?>
