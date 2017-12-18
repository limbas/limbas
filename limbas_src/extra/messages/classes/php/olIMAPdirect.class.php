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

class olIMAPdirect {
	var $config = array();
	var $_sock = null;
	var $_sequence_number = 0;
	var $_error_msg = '';
	var $_bufsz = 1440; // MAX_MTU - 60 ???
	
	function olIMAPdirect($user, $pass, $host, $port=143) {
		$this->constructor($user, $pass, $host, $port);
	}
	
	function constructor($user, $pass, $host, $port=143) {
		$this->config['host'] = $host;
		$this->config['port'] = $port;
		$this->config['user'] = $user;
		$this->config['pass'] = $pass;
	}
	
	function disconnect() {
		if (!$this->_sock)
			return;
		$this->_command("LOGOUT");
		fclose($this->_sock);
		$this->_sock = NULL;
	}
	
	function connect() {
		$_error_num = 0;
		$_error_msg = "fsockopen({$this->config['host']}, {$this->config['port']}, $_error_num, $_error_msg, 8);";
		$this->_sock = fsockopen($this->config['host'], $this->config['port'],
			$_error_num, $_error_msg, 8);

		if (!$this->_sock){
			$this->_error_msg = "{$_error_msg} ($_error_num)";
			return false;
		}

		$buf = fread($this->_sock, $this->_bufsz);

		if (!$this->_command("LOGIN \"{$this->config['user']}\" \"{$this->config['pass']}\"")){
			$this->_error_msg = "login failed. ({$this->_error_msg})";
			$this->disconnect();
			return false;
		}
			
		return true;
	}

	function examine($mbox) {
		if (!$this->_command("EXAMINE \"$mbox\"")){
			$this->_error_msg = "cannot examine folder $mbox. ({$this->_error_msg})";
			return false;
		}
		return true;
	}

	function idfromuid($id) {
		$msgid = intval($this->_command("UID FETCH {$id} UID", 'FETCH'));
		if ($msgid < 1){
			if ($this->_error_msg != '')
				$this->_error_msg = "failed fetching message id for uid $id. ({$this->_error_msg})";
			else
				$this->_error_msg = "no message with uid $id found.";
			return 0;
		}
		return $msgid;
	}

	function folderlist() {
		$ret = array();
		if (!($buf = $this->_command("LSUB \"\" \"*\"", 'LSUB'))){
			$this->_error_msg = "LSUB command failed. ({$this->_error_msg})";
			return NULL;
		}
		foreach(explode("\r\n", $buf) as $n){
			array_push($ret, array(
				'flags' => preg_replace('/\((.*)\).*/', '$1', $n),
				'delim' => preg_replace('/\(.*\) \"(.)\".*/', '$1', $n),
				'name' => preg_replace('/\(.*\) \".\" (.*)/', '$1', $n)
			));
		}
		return $ret;
	}
	
	function listmbox($mbox) {
		if (($mbox != NULL) && (!$this->examine($mbox)))
			return NULL;
	
		if (!($buf = $this->_command("SEARCH ALL", 'SEARCH'))){
			$this->_error_msg = "failed searching folder $mbox. ({$this->_error_msg})";
			return NULL;
		}

		return explode(" ", $buf);
	}
	
	function fetch_part($mbox, $uid, $part, $bufsz = 32768) {
		if (($mbox != NULL) && (!$this->examine($mbox)))
			return NULL;

		if (($msgid = $this->idfromuid($uid)) <= 0)
			return NULL;
			
		$mime = $this->_mime_part(NULL, $msgid, $part);

		header("Content-Type: " . (isset($mime["CONTENT-TYPE"])
				? $mime["CONTENT-TYPE"]	: 'application/binary'));

		$offset = 0;
		$pad='';
		
		while(true){
			$buf = $this->_fetch_cmd($msgid, "BODY.PEEK[{$part}]<{$offset}.{$bufsz}>");
			$offset += $bufsz;
			
			if (!$buf || (strlen($buf)==0))
				break;
				
			if (isset($mime["CONTENT-TRANSFER-ENCODING"]) &&
					($mime["CONTENT-TRANSFER-ENCODING"]=='base64')){
				/* on-the-fly base64 decoding */
				$all = explode("\n", $buf);
				foreach($all as $one){
					$one = $pad.preg_replace('/\s/', '', $one);
					$p = (strlen($one)%4);
					
					echo base64_decode(substr($one, 0, strlen($one)-$p));
				
					if ($p)
						$pad = substr($one, strlen($one)-$p, $p);
					else
						$pad ='';
				}
			} else
				echo $buf;
			ob_flush(); flush(); 
		}
		return "";
	}
	
	function fetch($mbox, $id, $is_uid = true) {
		if (($mbox != NULL) && (!$this->examine($mbox)))
			return NULL;

		if ($is_uid){
			if (($msgid = $this->idfromuid($id)) <= 0)
				return NULL;
		} else
			$msgid = $id;

		$command = sprintf("FETCH %d (RFC822)\n", $msgid);
		
		fprintf($this->_sock, "%08d %s\n", $this->_sequence_number, $command);

		$buf = fgets($this->_sock, $this->_bufsz);
		if (intval($this->_getstatus($buf))==$this->_sequence_number){
			$this->_error_msg = "no such message #$msgid, uid:$uid ({$this->_error_msg})";
			return NULL;
		}

		$msgsz = preg_replace('/^\* ([0-9]*) FETCH \(.* {([0-9]*)}/i', '$2', $buf);
		if ($msgsz < 1){
			$this->_error_msg = "error getting size for message #$msgid, uid:$uid ({$this->_error_msg})";
			return NULL;
		}

		/* get the whole message */
		$ret = $this->_read_block($msgsz);

		/* verify that we got everything */
		if (trim(fgets($this->_sock, $this->_bufsz)) != ')'){
			$this->_error_msg = "error retrieving content of message #$msgid (uid:$uid)";
			return NULL;
		}
		
		$buf = fgets($this->_sock, $this->_bufsz);
		$_sequence_number = intval($this->_getstatus($buf));
		
		if ($_sequence_number != $this->_sequence_number){
			$this->_error_msg = "error while fetching message #$msgid (uid:$uid)";
			return NULL;
		}
				
		$this->_sequence_number++;

		return $ret;
	}
	
	function last_error() {
		return $this->_error_msg;
	}

	function mime_part($mbox, $uid, $part) {
		if (($mbox != NULL) && (!$this->examine($mbox)))
			return NULL;

		if (($msgid = $this->idfromuid($uid)) <= 0)
			return NULL;
	
		return _mime_part(NULL, $msgid, $part);
	}
	
	function _mime_part($mbox, $msgid, $part) {
		if (($mbox != NULL) && (!$this->examine($mbox)))
			return NULL;

		$ret = array();

		$mime = $this->_fetch_cmd($msgid, "BODY.PEEK[{$part}.MIME]");
		
		$a = preg_split('/([a-z0-9\-]*):\s/i', $mime, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		for($i=0;$i<count($a);$i+=2)
			$ret[strtoupper($a[$i])] = trim($a[$i+1]);

		return $ret;
	}
	
	function _fetch_cmd($id, $cmd) {
		fprintf($this->_sock, "%08d FETCH %d %s\n",
			$this->_sequence_number, $id, $cmd);
			
		$buf = fgets($this->_sock, $this->_bufsz);
		if (intval($this->_getstatus($buf))==$this->_sequence_number){
			$this->_error_msg = "no data. [$cmd] ({$this->_error_msg})";
			return NULL;
		}

		$msgsz = trim(preg_replace('/^\* ([0-9]*) FETCH \(.* {([0-9]*)}/i', '$2', $buf));
		if (intval($msgsz) < 1){
			if ($msgsz!='0')
				$this->_error_msg = "error getting size. [$cmd] ({$this->_error_msg})";
			return NULL;
		}

		/* get the whole message */
		$ret = $this->_read_block($msgsz);

		/* verify that we got everything */
		if (trim(fgets($this->_sock, $this->_bufsz)) != ')'){
			$this->_error_msg = "error retrieving content. [$cmd]";
			return NULL;
		}
		
		/* verify status of FETCH command */
		$buf = fgets($this->_sock, $this->_bufsz);
		if (intval($this->_getstatus($buf)) != $this->_sequence_number){
			$this->_error_msg = "error fetching data. [$cmd]";
			return NULL;
		}
		$this->_sequence_number++;

		return $ret;
	}

	function _read_block($count, $_bufsz = 0) {
		
		$ret = '';
		if ($_bufsz==0)
			$_bufsz = $this->_bufsz;

		while(true){
			// set_time_limit(300);
			if (!($s = $this->__select($streams = array(0 => $this->_sock), $key, 1)))
				continue;

			if (feof($s)){
				fclose($s);
				$this->_sock = NULL;
				break;
			}
			$buf = fread($s, $count > $_bufsz ? $_bufsz : $count);
			$read = strlen($buf);
			$count -= $read;
			$ret .= $buf;
			
			if ($count<=0)
				break;
		}
		return $ret;
	}
	
	function _getstatus($line){
		$seq = trim(preg_replace('/([a-z\*0-9]) .*/is', '$1', $line));
		$sts = trim(preg_replace('/([a-z\*0-9]*) ([a-z]*) .*/is', '$2', $line));
		return (strcasecmp($sts, "OK") == 0) ? trim($seq) : null;
	}

	function _command($command=null, $options=null){
		$ret = false;
		$value = '';
		
		if ($command != null)
			fprintf($this->_sock, "%08d %s\n", ++$this->_sequence_number, $command);
		
		while(true){
			$buf = fgets($this->_sock, $this->_bufsz);

			$seq = intval(preg_replace('/([a-z\*0-9]) .*/is', '$1', $buf));
			if ($seq){
				if (intval($this->_getstatus($buf)) == $this->_sequence_number)
					return ($options != null) ? trim($value) : true;
				else {
					$this->_error_msg = trim($buf);
					return false;
				}
			}
			//echo "'$seq' $buf";
			//exit(1);
			if ($options == 'FETCH')
				$value = preg_replace('/^\* ([0-9]*) '.$options.' .*/is', '$1', $buf);
			else if ($options != null)
				$value .= preg_replace('/^\* '.$options.' ([0-9 ]*)/i', '$1', $buf);
		}
		return false;
	}

	function __select($streams, &$vkey, $timeout=0){
		$select = array();
		$null = NULL;

		foreach($streams as $key => $fd){
			if (!$fd)
				continue;
			$x = count($select);
			$select[$x] = $fd;
			$keys[$x] = $key;
		}

		if(stream_select($select, $null, $null, $timeout)){
			foreach($keys as $key){
				if($streams[$key] == $select[0]){
					$vkey = $key;
					return($select[0]);
				}
			}
		}
	}
}

?>
