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

define("IS_WEBDAV",1);

$pt = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
$uri = $_SERVER['REQUEST_URI'];

$uri = $_SERVER['PHP_SELF'];
$uri = str_replace('index.php','',$uri);

$path = explode("/", $pt);
array_pop($path);
array_pop($path);
$path = implode("/",$path);

set_include_path($path);
chdir($path);

require_once('inc/include_db.lib');
require_once('lib/include.lib');
require_once('lib/session.lib');
require_once('gtab/gtab.lib');
require_once('extra/explorer/filestructure.lib');
require_once('extern/SabreDAV/vendor/autoload.php');

#webdav://localhost/limbas_dev/dependent/webdav.php


// settings
#date_default_timezone_set('Canada/Eastern');
$publicDir = '/';
$tmpDir = 'tmpdata';

// get directories in global $filestruct
get_filestructure();

class MyCollection extends Sabre\DAV\Collection {

	private $lid;
	private $myPath;
	private $collection;

	function __construct($lid, $myPath) {
		$this->lid = $lid;
		$this->myPath = $myPath;
		$this->collection = array();

	}

	function getChildren() {

		# LEVEL
		if($this->myPath == "/"){
			$lid = 0;
		}else{
			$lid = parse_path(lmb_utf8_decode($this->myPath));
		}

		global $db;
		global $session;
		global $gtab;

		# -----Folder ------
		$this->getFolderChildren();

		# -----Files ------
		$this->getFilesChildren();

		return $this->collection;

	}


	private function getFilesChildren(){

		#$ffilter = get_userShow($this->lid,0);
		$ffilter["nolimit"][$this->lid] = 1;
		$ffilter["page"][$this->lid] = 1;
		$ffilter["anzahl"][$this->lid] = 'all';
		if($query = get_fwhere($this->lid,$ffilter,1)){
			if($ffile = get_ffile($query,$ffilter,$this->lid,1)){
				if ($ffile['id']) {
					foreach($ffile['name'] as $id => $name) {
						$name = lmb_utf8_encode($name);
						$this->collection[$name] = new MyFile($this->lid,$id, $this->myPath . (1 != lmb_strlen($this->myPath) ? '/' : '') . $name, $ffile['size'][$id], $ffile['mimetype'][$id], dateToStamp($ffile['editdatum'][$id]), $ffile['realname'][$id]);
					}
				}
			}
		}

	}

	private function getFolderChildren() {

		global $filestruct;
		# LEVEL
		/*
		if($this->myPath == "/"){
		$lid = 0;
		}else{
		$lid = parse_path(lmb_utf8_decode($this->myPath));
		}
		*/
		foreach($filestruct["id"] as $fid => $value){
			if($filestruct["level"][$fid] == $this->lid AND $filestruct["view"][$fid]){
				$name = lmb_utf8_encode($filestruct["name"][$fid]);
				$this->collection[$name] = new MyCollection($fid, $this->myPath . (1 != lmb_strlen($this->myPath) ? '/' : '') . $name);
			}
		}
	}

	function getChild($name) {

		if (isset($this->collection[$name])) {
			return $this->collection[$name];
		}
		else {
			$this->getFolderChildren();
			if (isset($this->collection[$name])) {
				return $this->collection[$name];
			}
			else {
				$this->getFilesChildren();
				if (isset($this->collection[$name])) {
					return $this->collection[$name];
				}
			}
		}
		throw new Sabre\DAV\Exception\NotFound('File not found: ' . $this->myPath . ': ' . $name);

	}


	public function delete(){
		if(!delete_dir($this->lid)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to delete node');
		}else{
			flag_filestructure();
		}

	}

	function getName() {
		return basename($this->myPath);
	}
	
	
	public function setName($newName){

		$did = parse_path(lmb_utf8_decode(lmb_getPrevPath($this->myPath)));
		if(!rename_dir($this->lid,lmb_utf8_decode($newName),$did)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to rename node');
		}else{
			flag_filestructure();
		}

	}

	public function childExists($name) {
		global $db;
		
		$sqlquery = "SELECT ID FROM LDMS_FILES WHERE LEVEL = ".$this->lid." AND LOWER(NAME) = '".parse_db_string(lmb_strtolower(lmb_utf8_decode($name)),40)."'";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(odbc_result($rs, "ID")){
			return true;
		}
		return false;
		
	}
	
	
	public function createDirectory($name){
		global $filestruct;
		
		if(add_file($this->lid,$name)){
			flag_filestructure();
			return true;
		}else{
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to add node');
		}
	}
	

	public function createFile($name,$data=null){
		global $umgvar;
		global $session;
		
		# temp upload path
		$tmpdir = $umgvar['pfad']."/USER/".$session['user_id']."/temp/";
		$tmppath = $tmpdir.crc32($tmpdir.$name);
		
		file_put_contents($tmppath,$data);
		
		# size
		if(filesize($tmppath) > $session["uploadsize"]){
			throw new Sabre\DAV\Exception\Forbidden('Insufficient Storage');
		}
		
		# LIMBAS uploadfunktion : replace mod
		$file["file"][] = $tmppath;$file["file_name"][] = $name;$file["file_type"][] = 0;$file["file_archiv"][] = 0;$dublicate["type"][] = "overwrite";
		if($fl = upload($file,$this->lid,array("datid" => 0,"gtabid" => 0,"fieldid" => 0),1,$dublicate)){
		# temp Datei löschen
		unlink($tmppath);
		}
		
		return true;
	}
	
	function put($data){
	
		error_log("put data folder");

	}
	
	# move folder
	public function move($destination){
		
		$did = parse_path(lmb_utf8_decode($destination));
		if(!move_dir(array($this->lid),$did)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to move node');
		}else{
			flag_filestructure();
		}

	}
	
	# copy folder
	public function copy($destination){
	
		$did = parse_path(lmb_utf8_decode($destination));
		if(!copy_dir(array($this->lid),$did,0)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to copy node');
		}else{
			flag_filestructure();
		}
	
	}
	
}

class MyFile extends \Sabre\DAV\File {
	private $lid;
	private $fid;
	private $path;
	private $filesize;
	private $type;
	private $lastModified;
	private $realname;

	function __construct($lid,$fid, $path, $filesize, $type, $lastModified, $realname) {
		$this->lid = $lid;
		$this->fid = $fid;
		$this->path = $path;
		$this->filesize = $filesize;
		$this->type = $type;
		$this->lastModified = $lastModified;
		$this->realname = $realname;
	}

	function getName() {
		
		return basename($this->path);
		
	}

	function get() {
		
		if($url = file_download($this->fid)){
			return fopen($url["path"], "r");
		}else{
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to open file');
		}
		
	}

	function delete(){
		
		if(!del_file($this->fid,0)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to delete file');
		}
		
	}

	function getSize() {
		
		return $this->filesize;
	}
	

	function getContentType() {
		
		return $this->type;
		
	}

	function getLastModified() {
		
		return $this->lastModified;
		
	}

	function put($data){
		global $session;
		global $umgvar;
		global $db;
		
		# temp upload path
		$name = lmb_getFileFromPath($this->path);
		$tmpdir = $umgvar['pfad']."/USER/".$session['user_id']."/temp/";
		$tmppath = $tmpdir.crc32($tmpdir.$name);
		file_put_contents($tmppath,$data);
		
		# size
		if(filesize($tmppath) > $session["uploadsize"]){
		    throw new Sabre\DAV\Exception\Forbidden('Insufficient Storage');
		}
		
		# LIMBAS uploadfunktion : replace mod
		$file["file"][] = $tmppath;$file["file_name"][] = $name;$file["file_type"][] = 0;$file["file_archiv"][] = 0;$dublicate["type"][] = "overwrite";
		if($fl = upload($file,$this->lid,array("datid" => 0,"gtabid" => 0,"fieldid" => 0),1,$dublicate)){
		# temp Datei löschen
			unlink($tmppath);
		}
		
		/*
		error_log("put data file");
		if($this->realname){
			$path = $umgvar['path'].'/UPLOAD/'.$this->realname;
			
			$sqlquery = "UPDATE LDMS_FILES SET EDITDATUM = ".LMB_DBDEF_TIMESTAMP.", EDITUSER = ".$session['user_id'].", SIZE = ".parse_db_int(filesize($path))." WHERE LEVEL = ".$this->lid." AND ID = ".$this->fid;
			
			error_log($sqlquery);
			
			$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			if(!$rs){
				return false;
			}
			return file_put_contents($path,$data);
		}
		*/

		#$custom_path=lmb_substr($this->myPath,0,lmb_strrpos($this->myPath,"/"));//<- the target folder
		#$obj=new MyDirectory($custom_path,$this->db); //<- my Collection extention class
		#$obj->createFile(basename($this->myPath),$data);//<- filename from path
		#throw new Sabre_DAV_Exception_Forbidden('');
	}
	
	
	# rename
	public function setName($newName){

		if(!rename_file($this->fid,lmb_utf8_decode($newName),$this->lid)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to rename file');
		}
		
	}
	
	# move file
	public function move($destination){
		
		$did = parse_path(lmb_getPrevPath(lmb_utf8_decode($destination)));
		if(!move_file(array($this->fid),$did)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to move file');
		}
		
	}
	
	# copy file
	public function copy($destination){
	
		$did = parse_path(lmb_getPrevPath(lmb_utf8_decode($destination)));
		if(!copy_file(array($this->fid),$did)){
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to copy file');
		}

	}
	
}


class MyObjectTree extends Sabre\DAV\Tree {
	public function __construct(Sabre\DAV\ICollection $rootNode) {
		parent::__construct($rootNode);
	}

	public function move($sourcePath, $destinationPath) {
		$s = $this->getNodeForPath($sourcePath);
		
		# rename
		if(lmb_getPrevPath($sourcePath) == lmb_getPrevPath($destinationPath)){
			$newName = lmb_getFileFromPath($destinationPath);
			$s->setName($newName);
		# move
		}else{
			$s->move($destinationPath);
		}
		

		#error_log('source: ' . $sourcePath);
		#error_log('dest: ' . $destinationPath);
		
		
		/*
		list($sourceDir, $sourceName) = URLUtil::splitPath($sourcePath);
		list($destinationDir, $destinationName) = URLUtil::splitPath($destinationPath);

		if ($sourceDir===$destinationDir) {
		$renameable = $this->getNodeForPath($sourcePath);
		$renameable->setName($destinationName);
		} else {
		$this->copy($sourcePath,$destinationPath);
		$this->getNodeForPath($sourcePath)->delete();
		}
		$this->markDirty($sourceDir);
		$this->markDirty($destinationDir);
		*/
	}
	
	public function copy($sourcePath, $destinationPath) {
		
		$s = $this->getNodeForPath($sourcePath);
		$s->copy($destinationPath);

	}

}


function lmb_getFileFromPath($path){
	$fn = explode('/',$path);
	return array_pop($fn);
	#return $fn[count($fn)-1];
}

function lmb_getPrevPath($path){
	$fn = explode('/',$path);
	array_pop($fn);
	return implode('/',$fn);
}






// Make sure there is a directory in your current directory named 'public'. We will be exposing that directory to WebDAV
$rootNode = new \MyCollection(0, $publicDir);

// The rootNode needs to be passed to the server object.
$server = new \Sabre\DAV\Server(new MyObjectTree($rootNode));

// Support for html frontend
$server->addPlugin(new \Sabre\DAV\Browser\Plugin());

// Automatically guess (some) contenttypes, based on extesion
$server->addPlugin(new \Sabre\DAV\Browser\GuessContentType());

// Temporary file filter
//$server->addPlugin(new \Sabre\DAV\TemporaryFileFilterPlugin($path.'/tmp'));
$server->addPlugin(new \Sabre\DAV\TemporaryFileFilterPlugin('/tmp'));

$server->setBaseUri($uri);

// And off we go!
$server->exec();











/*
 #summary How to use SabreDAV in high-traffic situations

= Summary = 

While the default library suffices for small-scall WebDAV installations, there are areas that can be optimized for maximum perfmance, and operations in a load-balanced environment.

This manual aims to cover the most important bits

= Move and Copy =

When you deal with a large file-tree, and the system is implemented with the [ObjectTree] api, operations like COPY and MOVE can be really slow.

Each of these methods tranferses the entire tree, and operates on them one-by-one, as an example, a move of a directory follows the following pattern:

  * The ObjectTree tranverses the entire source tree, for each item it will
    * do a get() on the source file, and a createFile on the destination (heavy)
	* make subdirectories if directories are encountered
	* copy all the properties over in a similar fashion (if IProperties is used)
  * Next, it will traverse the entire destination tree again, deleting all files and directories one-by-one

The reason for this complex behaviour, is that SabreDAV assumes there might not be a relation between the back-ends of different parts of your virtual filesystem,
by implementing this algorithm, correct behaviour is guaranteed while keeping a simple to extend API.

If it is possible, it is recommended to extend the standard Sabre_DAV_ObjectTree class and override these methods.

The best example for this would be, if you would implement objects purely based on an operating systems' actual filesystem. The standard behaviour would suffice, but passing a simple 'copy' operation takes that all away.

= Delete =

Deleting files has similar problems, but it might be possible to solve this on a higher level. If your directory represents a list of database-records
(and thus each file is based on a single record), instead of individually deleting every record by its primary key, the parent object could issue a single DELETE query

= Load Balanced SabreDAV =

SabreDAV has a few standard utilities for locking and catching 'temporary files'. These classes (TemporaryFileFilter and Sabre_DAV_LockManager_FS) store
information on the filesystem.

It would be possible to store these files on a network location instead, as long as the network filesystem has proper support for flock() (which NFS as an example doesn't).

Storing locks could be implemented in a database server or caching layer such as memcached, the temporary files are a bit bigger, so that might pose some issues.
We chose to not supply any plugins for these alternative backends, as there are a lot of options out there and impossible to cover them all. Instead it was made really easy to extend these subsystems.

Currently 'temporary files' don't get cleaned up, and a cron job should be made to delete older files (>1 hour).

= Often used methods = 

The following methods can be called quite oftenly, even in a single HTTP request:

  * Sabre_DAV_INode::getSize()
  * Sabre_DAV_INode::getLastModified()
  * Sabre_DAV_INode::getName()
  * Sabre_DAV_IDirectory::getChild()
  * Sabre_DAV_IDirectory::getChildren()

This also applies to the counterparts in the [Tree] class. The reason for this is that a lot of introspection needs to be done to figure out if a request is valid, and reasons why it possibly might not be.
The other design-option was to push all this logic in the Tree and Node classes, but this would make it a lot harder to create virtual filesystems.

It should be assumed that these methods are called multiple times, and its a really good idea to keep the information around for a second use. If your 'File' object represents a dataaserecord of a blog artile, the best way to go is to load all the meta-data and information when the node is first instantatiated and serve that content back.

Besides all this, standard server-side caching strategies still apply.

 
  */

?>