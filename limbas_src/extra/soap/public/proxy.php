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
require_once("lib/include.lib");

#echo session_id();
#echo "<br>";
#echo session_name();




if(!$LIM["use_soap"]){
	$rpath = $LIM["lmpath"]."/extra/explorer/filestructure.lib";
	require_once($rpath);
}

function LMB_cacheDbFile($array){
	global $LIM;

	$fp = fopen(rtrim($LIM["cache_home"],"/")."/".rtrim($LIM["dbFolder"],"/") . "/db.dat","w");
	fputs($fp,serialize($array));
	fclose($fp);
	if(DEBUG) error_log("[proxy][".__LINE__."] create db.dat file");
}

function LMB_readCacheDbFile(){
	global $LIM;
	
	$dbfile = rtrim($LIM["cache_home"],"/")."/".rtrim($LIM["dbFolder"],"/") . "/db.dat";
	if(is_file($dbfile)){
		$file = fopen($dbfile,"r");
		$content = fread($file,filesize($dbfile));
		fclose($file);
	}
	
	if($content){
		return unserialize($content);
	}

	return array();
}

function LMB_cacheFile($urlMd5,$result){
	global $LIM;
	
	$path = rtrim($LIM["cache_home"],"/")."/".trim($LIM["cacheFolder"],"/");
	
	if(DEBUG) error_log("[proxy] cache file $urlMd5");
	if(getCacheSize($path)>1024*1024*$LIM["cacheSizeLimit"]){
		freeCache($path);
	}

	$file = fopen($path."/".$urlMd5,"w+");
	fwrite($file,$result);
	fclose($file);
	if(DEBUG) error_log("[proxy][".__LINE__."] end cache file $file");
}

function freeCache($path){
	global $LIM;
	global $cacheToFree;
	global $db_;

	while($LIM["cacheToFree"]<1-(getCacheSize($path)/($LIM["cacheSizeLimit"]*1024*1024))){
		deleteSeldomFile($path);
	}
}

function getHeaderValues($header){

	if(! strpos($header[0],"200")){
		header($header[0],true);
		exit;
	}

	$headers = array();
	foreach($header as $key => $val){
		$nameVal = explode(";",trim($val));
		if($nameVal){
		foreach ($nameVal as $key2 => $val2){
			$nameVal2 = explode(":",trim($val2));
			if($nameVal2 AND trim($nameVal2[1])){
				#$headers[$nameVal2[0]] = eregi_replace("[\"']","",trim($nameVal2[1]));
				$headers[$nameVal2[0]] = str_replace("\"","",trim($nameVal2[1]));
				$headers[$nameVal2[0]] = str_replace("'","",$headers[$nameVal2[0]]);
			}
			$nameVal2 = explode("=",trim($val2));
			if($nameVal2 AND trim($nameVal2[1])){
				#$headers[$nameVal2[0]] = eregi_replace("[\"']","",trim($nameVal2[1]));
				$headers[$nameVal2[0]] = str_replace("\"","",trim($nameVal2[1]));
				$headers[$nameVal2[0]] = str_replace("'","",$headers[$nameVal2[0]]);
			}
		}
		}
	}
	
	return $headers;
}

function deleteSeldomFile($path){
	global $db_;

	if(DEBUG) error_log("[proxy][".__LINE__."] free cache");

	$minAccess = 0;


	foreach ($db_ as $key => $val){
		if($minAccess==0 || $minAccess>$val["count"]){
			$minAccess = $val["count"];
			$selected = array($key);
		}else{
			$selected[] = $key;
		}
	}

	foreach ($selected as $key => $val){
		unlink(rtrim($path,"/")."/".$val);
		$db_[$val]["cached"] = false;
		if(DEBUG) error_log("[proxy][".__LINE__."] $val free cache");
	}
	if(DEBUG) error_log("[proxy][".__LINE__."] end free cache");
}


function getCacheSize($path){
	$fileSize = exec("du --max-depth=1 . |grep ./cache|awk '{print $1;}'");
	return $fileSize;
}


function displayFileFromCache($fileName,$urlMd5){
	global $db_;
	
	header("Content-Type: ".$db_[$urlMd5]["Content-Type"]);
	header('Content-Disposition: inline; filename="'.trim($db_[$urlMd5]["filename"]).'"');
	
   	readfile($fileName);

	if(DEBUG) error_log("[proxy][".__LINE__."] display file from cache {$fileName}\ndb_:\nct[Content-Type: {$db_[$urlMd5]["Content-Type"]}]\ncd[".'Content-Disposition: inline; filename="'.trim($db_[$urlMd5]["filename"]).'"'."]\n".print_r($db_[$urlMd5],1));
	if(DEBUG) error_log("[proxy][".__LINE__."] end display file from cache $urlMd5");
	exit();
}

function saveCookie($cookieValue,$cookie){
	global $LIM;

	if($cookie OR !$LIM["use_cookie"] OR !$LIM["use_soap"]){
		return;
	}

	if(DEBUG) error_log("[proxy][".__LINE__."] save cookie $cookieValue");

	if($cookieValue){
		$cookieValue = explode("=",$cookieValue);
		soapCookieSet($cookieValue[0],$cookieValue[1]);
	}
	
	return $cookie;
}

function doHttpRequest($server,$port,$username,$password,$url,$cookie=null){
	$result = "";
	
	$url .= "&sendas=1";
	
	$fp = fsockopen($server,$port, $errno, $errstr, 30);
	if(!$fp){
		error_log("proxy][".__LINE__."] file not available: $errstr ($errno)");
		return null;
	}else{
		$out = "GET " . $url . " HTTP/1.1\r\n"; ///limbas/main.php?&action=download&ID=$FILEID
		$out .= "Authorization: Basic ".base64_encode($username . ":" . $password) . "\r\n";
		if($cookie){
			$out .= "Set-Cookie: $cookie\r\n";
		}
		$out .= "Host: SOAP\r\n";
		$out .= "Connection: Close\r\n\r\n";
		if(DEBUG) error_log("[proxy][".__LINE__."] send GET headers: $out");

		fwrite($fp, $out);
		while (!feof($fp)) {
		   $result .= fgets($fp);
		}
		fclose($fp);

		if(DEBUG) error_log("[proxy][".__LINE__."] get GET response: $result");
	}

	return $result;
}


##################################
############   BEGIN    ##########
##################################


if(!$cookie AND $LIM["use_cookie"] AND $LIM["use_soap"]){
	$cookie = soapCookieGet();
	$cookie = $cookie[$LIM["session_name"]]["val"];
}else{
	$cookie = $lmbs["session"]["s_id"];
}

$URL = base64_decode($_REQUEST["url"]);
if(DEBUG) error_log("[proxy][".__LINE__."] url requested: ".base64_decode($_REQUEST["url"]));

$tmpURL = explode("?",$URL);

if($cookie){
	$URL = rtrim($LIM["lim_url"],"/")."/".ltrim($tmpURL[0],"/")."?".$LIM["session_name"]."=".$cookie."&".$tmpURL[1];
}else{
	$URL = rtrim($LIM["lim_url"],"/")."/".ltrim($tmpURL[0],"/")."?".$tmpURL[1];
}

if(DEBUG) error_log("[proxy][".__LINE__."] mk url: $URL");

//name of the cached filename
$urlMd5 = md5($_REQUEST["url"]);
if(DEBUG) error_log("[proxy][".__LINE__."] mk cached filename: $urlMd5");

$db_ = LMB_readCacheDbFile();
$db_[$urlMd5]["count"]++;

$fileName = rtrim($LIM["cache_home"],"/")."/".trim($LIM["cacheFolder"],"/")."/".$urlMd5;
if(DEBUG) error_log("[proxy][".__LINE__."] mk filename: $fileName");

//if file exists read it from cache
if($LIM["cacheFile"] && file_exists($fileName)){
	if($LIM["cacheMinExpiration"]>0 && $LIM["cacheMinExpiration"] > (time()-$db_[$urlMd5]["date"])){
		displayFileFromCache($fileName,$urlMd5);
	}
}

# get parameter
if($LIM["use_soap"]){

	$result = doHttpRequest($LIM["lim_server"],80,$LIM["username"],$LIM["pass"],$URL,$cookie);

	if(DEBUG) error_log("[proxy][".__LINE__."] called doHttpRequest: args [{$LIM["lim_server"]}],[80],[{$LIM["username"]}],[{$LIM["pass"]}],[$URL],[$cookie]");
	# write header
	$lines = explode("\r\n\r\n",$result);
	$header = array_shift($lines);

	$headers = explode("\r\n",$header);
	$headers = getHeaderValues($headers);
	
	$cookie = saveCookie($headers["Set-Cookie"],$cookie);
	
	if(isset($headers["filename"])) $headers["filename"] = urldecode($headers["filename"]);

	header("Content-Type: ".$headers["Content-Type"]);
	header('Content-Disposition: inline; filename="'.$headers["filename"].'"');

	if(DEBUG) error_log("[proxy][".__LINE__."] returned as [{$result["filename"]}][{$headers["filename"]}]");

	$db_[$urlMd5]["filename"] = trim($headers["filename"]);
	$db_[$urlMd5]["Content-Type"] = $headers["Content-Type"];
	$db_[$urlMd5]["Content-Length"] = $headers["Content-Length"];
	$db_[$urlMd5]["cached"] = true;
	$db_[$urlMd5]["date"] = time();

	$result = implode("\r\n\r\n",$lines);
	if($LIM["cacheFile"]){
		LMB_cacheDbFile($db_);
		LMB_cacheFile($urlMd5,$result);
	}

	if($result){
		echo $result;
	}
	
}else{
	parse_str($tmpURL[1],$req);
	if($req["ID"]){
		if($req["action"]=="explorer_convert"){
			$file = preview_archive(array($req["ID"]),$req["method"],null);
			$file["name"][0] = trim($file["name"][0]);
			#header('Content-Description: File Transfer');
			#header("Content-Length: ".$file["size"][0]);
			header('Content-Type: '.$file["mimetype"][0].'; name="'.$file["name"][0].'"');
			header('Content-Disposition: inline; filename="'.$file["name"][0].'"');
			#header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		   	header('Pragma: private');
		   	readfile("{$file[0]}");
		}else{
			if($file = file_download($req["ID"])){
				chdir($LIM["lmpath"]);
				$file["name"] = trim($file["name"]);
				header('Content-Description: File Transfer');
				header("Content-Length: ".filesize($file["path"]));
				if($req["name"]){# to show inline
					header('Content-Type: '.$file["mimetype"]);
				}else{
					header('Content-Type: '.$file["mimetype"].'; name="'.$file["name"].'"');
				}
				header('Content-Disposition: inline; filename="'.$file["name"].'"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			   	header('Pragma: private');
				readfile("{$file["path"]}");
			}
		}
	}
}
?>