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
 * ID: 9
 */

/* --- alte Dateien löschen --------------------------------------------- */
$rsc = "rm $umgvar[pfad]/USER/$session[user_id]/menuicons/*";
system($rsc);

if($action == "user_change_admin"){$userid = $ID;} else {$userid = $session[user_id];}


$RGB1 = explode(",",hexdec(lmb_substr($farbschema[WEB1], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB1], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB1], 5, 2)));
$RGB2 = explode(",",hexdec(lmb_substr($farbschema[WEB2], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB2], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB2], 5, 2)));
$RGB3 = explode(",",hexdec(lmb_substr($farbschema[WEB3], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB3], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB3], 5, 2)));
$RGB4 = explode(",",hexdec(lmb_substr($farbschema[WEB4], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB4], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB4], 5, 2)));
$RGB5 = explode(",",hexdec(lmb_substr($farbschema[WEB5], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB5], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB5], 5, 2)));
$RGB6 = explode(",",hexdec(lmb_substr($farbschema[WEB6], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB6], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB6], 5, 2)));
$RGB7 = explode(",",hexdec(lmb_substr($farbschema[WEB7], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB7], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB7], 5, 2)));
$RGB8 = explode(",",hexdec(lmb_substr($farbschema[WEB8], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB8], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB8], 5, 2)));
$RGB9 = explode(",",hexdec(lmb_substr($farbschema[WEB9], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB9], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB9], 5, 2)));
$RGB10 = explode(",",hexdec(lmb_substr($farbschema[WEB10], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB10], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB10], 5, 2)));
$RGB12 = explode(",",hexdec(lmb_substr($farbschema[WEB12], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB12], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB12], 5, 2)));
$RGB14 = explode(",",hexdec(lmb_substr($farbschema[WEB14], 1, 2)).",".hexdec(lmb_substr($farbschema[WEB14], 3, 2)).",".hexdec(lmb_substr($farbschema[WEB14], 5, 2)));

function setcolor($img){
	global $RGB1;
	global $RGB2;
	global $RGB3;
	global $RGB4;
	global $RGB5;
	global $RGB6;
	global $RGB7;
	global $RGB8;
	global $RGB9;
	global $RGB10;
	global $RGB14;

	/* --- Farbwerte umwandeln --------------------------------------------- */
    $rgb[1] = imagecolorallocate($img, $RGB1[0], $RGB1[1], $RGB1[2]);
    $rgb[2] = imagecolorallocate($img, $RGB2[0], $RGB2[1], $RGB2[2]);
    $rgb[3] = imagecolorallocate($img, $RGB3[0], $RGB3[1], $RGB3[2]);
    $rgb[4] = imagecolorallocate($img, $RGB4[0], $RGB4[1], $RGB4[2]);
    $rgb[5] = imagecolorallocate($img, $RGB5[0], $RGB5[1], $RGB5[2]);
    $rgb[6] = imagecolorallocate($img, $RGB6[0], $RGB6[1], $RGB6[2]);
    $rgb[7] = imagecolorallocate($img, $RGB7[0], $RGB7[1], $RGB7[2]);
    $rgb[8] = imagecolorallocate($img, $RGB8[0], $RGB8[1], $RGB8[2]);
    $rgb[9] = imagecolorallocate($img, $RGB9[0], $RGB9[1], $RGB9[2]);
    $rgb[10] = imagecolorallocate($img, $RGB10[0], $RGB10[1], $RGB10[2]);
    $rgb[12] = imagecolorallocate($img, $RGB12[0], $RGB12[1], $RGB12[2]);
    $rgb[14] = imagecolorallocate($img, $RGB14[0], $RGB14[1], $RGB14[2]);

	return $rgb;
}


function create_gradient($w,$h,$r1,$g1,$b1,$r2,$g2,$b2){

	$w  = max(min(33,$w),1);
	$h  = max(min(300,$h),1);
	$r1 = max(min(255,$r1),0);
	$g1 = max(min(255,$g1),0);
	$b1 = max(min(255,$b1),0);
	$r2 = max(min(255,$r2),0);
	$g2 = max(min(255,$g2),0);
	$b2 = max(min(255,$b2),0);

	$s  = array($r1,$g1,$b1);
	$e  = array($r2,$g2,$b2);

	$image  = imagecreate($w,$h);

	for ($i = 0; $i<$h; $i++)
	{
	    $l = ImageColorAllocate
	              (
	                $image,
	                max(0,$s[0]-((($e[0]-$s[0])/-$h)*$i)),
	                max(0,$s[1]-((($e[1]-$s[1])/-$h)*$i)),
	                max(0,$s[2]-((($e[2]-$s[2])/-$h)*$i))
	              );
	    imageline($image, 0, $i, $w, $i, $l);
	}

	return $image;
}


function replace_color_blur($img,$RGB4,$RGBB=null){

	$x = imagesx($img);
	$y = imagesy($img);

	if($RGBB){
		$trans = imagecolorallocate($img,$RGBB[0],$RGBB[1],$RGBB[2]);
	}else{
		$trans = imagecolorallocate($img,255,255,255);
		imagecolortransparent($img,$trans);
	}

	for($i = 0; $i < $x; $i++){
		for($e = 0; $e < $y; $e++){
			$oc = imagecolorat($img,$i,$e);
			$oci = imagecolorsforindex($img,$oc);

			$ncr = intval($oci["red"] * (256-$RGB4[0])/256 + $RGB4[0]);
			$ncg = intval($oci["green"] * (256-$RGB4[1])/256 + $RGB4[1]);
			$ncb = intval($oci["blue"] * (256-$RGB4[2])/256 + $RGB4[2]);
			$c = $ncr.$ncg.$ncb;

			if($oci["blue"]==255 AND $oci["green"]==255 AND $ncr==$oci["red"]){
				imagesetpixel($img,$i,$e,$trans);
			}else{
				if(!$ca[$c]){
					$ca[$c] = imagecolorallocate($img, $ncr, $ncg, $ncb);
				}
				imagesetpixel($img,$i,$e,$ca[$c]);
			}
		}
	}
}

// no usage found!
/* # context left border
if(!file_exists("USER/".$userid."/menuicons/div".$i."_contextRow.png")){
	$img = imagecreate(20,1);
	$rgb = setcolor($img);
	imagefill($img, 0, 0, $rgb[10]);
	$ok = imagepng($img, "USER/".$userid."/menuicons/contextRow.png");
	imagedestroy($img);
} */

# COLOR 3 --------------------
// done or not?   depr ,  y   ,   y  , -squared,   ~       , -caret    ,  ~         ,   y             ,   y
$piclist = array("topC","topX","topY","arrowUp","arrowDown","arrowLeft","arrowRight","smallArrowRight","smallArrowDown");

foreach ($piclist as $key => $value){
	if(!file_exists("USER/".$userid."/menuicons/".$value."3.png") AND file_exists("layout/".$session["layout"]."/pic/$value.png")){
		$img = imagecreatefrompng("layout/".$session["layout"]."/pic/$value.png");
		$ok = replace_color_blur($img,$RGB12);
		$ok = imagepng($img, "USER/".$userid."/menuicons/".$value."3.png");
		imagedestroy($img);
	}
}

# COLOR 4 --------------------
// done or not?   depr ,  y   ,   y  , -squared,   ~       , -caret    ,  ~         ,   y             ,   y             , depr
$piclist = array("topC","topX","topY","arrowUp","arrowDown","arrowLeft","arrowRight","smallArrowRight","smallArrowDown","ArrowDown");

foreach ($piclist as $key => $value){
	if(!file_exists("USER/".$userid."/menuicons/".$value."4.png") AND file_exists("layout/".$session["layout"]."/pic/$value.png")){
		$img = imagecreatefrompng("layout/".$session["layout"]."/pic/$value.png");
		$ok = replace_color_blur($img,$RGB12);
		$ok = imagepng($img, "USER/".$userid."/menuicons/".$value."4.png");
		imagedestroy($img);
	}
}


// no usage found!
/* # COLOR 10 --------------------
$piclist = array("bckTop");

foreach ($piclist as $key => $value){
	if(!file_exists("USER/".$userid."/menuicons/".$value."10.png") AND file_exists("layout/".$session["layout"]."/pic/$value.png")){
		$img = imagecreatefrompng("layout/".$session["layout"]."/pic/$value.png");
		$ok = replace_color_blur($img,$RGB10);
		$ok = imagepng($img, "USER/".$userid."/menuicons/".$value."10.png");
		imagedestroy($img);
	}
}
*/

# smallicons --------------------

/*
$xsize = 12;
$_8pixicons = array("fileicons/xml.gif","fileicons/pdf.gif","fileicons/odt.gif","silk_icons/camera.gif");

foreach ($_8pixicons as $key => $value){
	$filepath = $umgvar["pfad"]."/pic/".$value;
	$filename = explode("/",$value);
	$file = $filename[count($filename)-1];
	if(file_exists($filepath)){
		#$cmd = $umgvar["imagemagick"]."/convert -resize ".$xsize."x $filepath ".$umgvar["pfad"]."/pic/8pxicons/".$file;
		$cmd = $umgvar["imagemagick"]."/convert $filepath -channel RGBA -separate -resize ".$xsize."x -combine ".$umgvar["pfad"]."/pic/8pxicons/".$file; # alpha chanel works
		system($cmd,$out);
	}
}

foreach ($LINK["name"] as $key => $value){
	$filepath = $umgvar["pfad"]."/".$LINK["icon_url"][$key];
	$file = explode("/",$filepath);
	$file = $file[count($file)-1];

	if(file_exists($filepath)){
		#$cmd = $umgvar["imagemagick"]."/convert -resize ".$xsize."x -alpha Off $filepath ".$umgvar["pfad"]."/pic/8pxicons/".$file;
		$cmd = $umgvar["imagemagick"]."/convert $filepath -channel RGBA -separate -resize ".$xsize."x -combine ".$umgvar["pfad"]."/pic/8pxicons/".$file; # alpha chanel works
		system($cmd,$out);
	}
}
*/

?>