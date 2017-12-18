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


# frameset
$topFrameSize = 50;
$topMenuSize = 36;
$LeftMenuSize = 190;
$rightMenuSize = 230;
$topFrameDivSize = ($topFrameSize-1);
if($intro){return;}

$cssfile = fopen("{$umgvar['pfad']}/USER/{$session['user_id']}/layout.css", "w+");
if($umgvar['waitsymbol']){$waitsymbol = $umgvar['waitsymbol'];}else{$waitsymbol = "pic/wait1.gif";}

$fontc3 = lmbSuggestColor($farbschema['WEB13'],"CCCCCC","909090");
$fontc6 = lmbSuggestColor($farbschema['WEB6'],"CCCCCC","333333");
$fontc7 = lmbSuggestColor($farbschema['WEB7'],"CCCCCC","333333");

$fontsize09 = "0.9em";
$fontsize11 = "1.1em";
$fontsize12 = "1.2em";
$fontsize13 = "1.3em";

$bgcolor1 = 'FEF9E1'; # tablerow

$umgvar['fontsize'];


# EXTENSIONS
if($GLOBALS["gLmbExt"]["ext_css.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_css.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

$buf .= file_get_contents($umgvar['pfad'] . "/layout/skalar/icons.css");

$buf .= <<<EOD


/*global*/
body{
	font-size: {$umgvar['fontsize']}px;
	font-family: {$umgvar['font']};
	background-color:{$farbschema['WEB14']};
	height:96%;
}

/*global*/
table{
	font-size: {$umgvar['fontsize']}px;
	font-family: {$umgvar['font']};
}

/*global*/
th{
	font-weight:normal;
}

/*middle frame*/
body.main{
	margin:0px;
	padding:0px;
}

/*middle frame header*/
body.main_top{
	margin:0px;
	padding:0px;
}

/*top frame*/
body.top{
	
}

/*top menu frame*/
body.top2{
	margin:0px;
	padding:0px;
}

/*navigation menu frame*/
body.nav{
	margin:0px;
	padding:0px;
	padding-left:5px;
	padding-bottom:5px;
}

/*tools menu frame*/
body.multiframe{
	margin:0px;
	padding:0px;
	padding-top:6px;
	padding-right:5px;
	padding-bottom:5px;
}



/* Frame Top */

.lmbfringeFrameTop{
	background-color:FFFFFF;
	border:1px solid {$farbschema['WEB3']};
	margin:5px;
	margin-top:0px;
	margin-bottom:0px;
	height:{$topFrameDivSize};
}

.lmbItemInfoTop{
	background-color:transparent;
	color:{$farbschema['WEB12']};
	font-size: {$fontsize12};
	position:absolute;
	left:10px;
	top:5px;
}

.lmbItemUsernameTop{
	background-color:transparent;
	color:{$farbschema['WEB12']};
	font-size: {$fontsize09};
	position:absolute;
	right:30px;
	top:8px;
}

.lmbItemLogoTopLeft{
	background-color:transparent;
	left:10px;
	top:5px;
	position:absolute;
}

.lmbItemLogoTopRight{
	background-color:transparent;
	right:20px;
	top:0px;
	position:absolute;
}



/* Frame Top 2 */

.lmbfringeMenuTop2{
	position:absolute;
	vertical-align:middle;
	margin-left:5px;
	margin-right:5px;
	border-collapse:collapse;
}


.lmbMenuItemTop2{
	color:{$farbschema['WEB12']};
	vertical-align:middle;
	padding:10px;
	padding-top:5px;
	padding-bottom:5px;
	margin:0px;
	font-size: {$fontsize12};
	border:none;
	text-decoration: none !important; 
	display:block;
	cursor:pointer;
    
    /* prevent selection of text */
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.lmbMenuItemspaceTop2{
	color:{$farbschema['WEB12']};
	padding:10px;
	padding-top:5px;
	padding-bottom:5px;
	margin:0px;
	font-size: {$fontsize12};
	border:1px solid {$farbschema['WEB3']};
	overflow:hidden;
	background-color:{$farbschema['WEB10']};
}

.lmbMenuItemActiveTop2{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyTopA.png);
	background-repeat:repeat-x;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
}

.lmbMenuItemInactiveTop2{
	background-color: {$farbschema['WEB10']};
        border:1px solid {$farbschema['WEB3']};
}

.lmbMenuItemInactiveTop2:hover{
	background-color:{$farbschema['WEB13']};
	color:{$farbschema['WEB12']};
	vertical-align:middle;
	margin:0px;
	text-decoration: none;
}

.lmbMenuSpaceBottom{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyTopB.png);
	background-repeat:repeat-x;
	height:10px;
	border:1px solid {$farbschema['WEB3']};
	border-top:none;
	border-bottom:none;
}


.formeditorPanelHead{
        border:1px grey;
        height:15px;
        color:white;
        text-align: center;
        background-color: {$farbschema[WEB6]};

}
        
.formeditorPanel{
        border:1px solid grey;
        width:210px;
        background-color: {$farbschema[WEB7]};
}
    
i.btn {
        border:2px outset;
        cursor: pointer;
        float:left;
        padding-bottom: 22px;
        padding-right: 22px;
}

.formeditorPanel.no-padding td {
        padding:0;
}

/* Frame nav */

.lmbfringeFrameNav{
	padding:6px;
	padding-top:0px;
	height:100%;
	width:100%;
	border:1px solid {$farbschema['WEB3']};
	border-right:none;
	border-top:none;
	cursor:w-resize;
}

.lmbfringeFrameMultiframe{
	padding:6px;
	padding-top:0px;
	height:100%;
	width:100%;
	border:1px solid {$farbschema['WEB3']};
	border-right:none;
	cursor:w-resize;
}

.lmbfringeMenuNav{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:right;
	color:{$farbschema['WEB12']};
	width:100%;
	border: 1px solid {$farbschema['WEB4']};
	margin-bottom:8px;
	cursor:default;
	/*box-shadow: -5px -5px 5px #FFFFFF inset;*/
    
    /* prevent selection of text */
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.lmbMenuHeaderNav{
	color:{$farbschema['WEB12']};
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGrey.png);
	background-position:center;
	background-repeat:repeat-x;
	font-size: {$fontsize12};
	height:1.6em;
	vertical-align:top;
	padding-top:4px;
	padding-bottom:4px;
	cursor:default;
	border-bottom: 1px solid {$farbschema['WEB1']};
}

.lmbMenuItemHeaderNav{
	text-align:left;
	padding-left:45px;
}

.lmbMenuBodyNav{
	text-align:left;
	overflow:hidden;
	margin-top:8px;
}

.lmbMenuItemBodyNav{
	text-align:left;
	overflow:hidden;
}

.lmbMenuItemBodyNav:hover{
	text-align:left;
	overflow:hidden;
	cursor:pointer;
	text-decoration:underline;
}

.lmbNaviconLeftNav{
	position:relative;
	float:left;
	left:-5px;
	top:0px;
	cursor:pointer;
}

.lmbNaviconRightNav{
	position:relative;
	float:right;
	right:-5px;
	top:0px;
	cursor:pointer;
}




/* Frame main header */

.lmbfringeHeaderMain{
	padding:6px;
	padding-left:20px;
}

.lmbItemHeaderMain{
	border:1px solid {$farbschema['WEB3']};
	color:{$farbschema['WEB12']};
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGrey.png);
	background-position:center;
	background-repeat:repeat-x;
	font-size: {$fontsize12};
	height:1.6em;
	vertical-align:top;
	padding:2px;
	padding-left:10px;
	cursor:default;
}


/* Frame main */

.lmbfringeFrameMain{
	height:100%;
	width:100%;
}
        
.lmbPositionContainerMainTabPool,
.lmbPositionContainerMain{
	padding:20px;
	padding-top:10px;
}



/* gtab Tabelle außen */

.lmbfringeGtab{
	margin-left:20px;
	margin-top:10px;
	position:relative;
	overflow:auto;
	background-image:url(../../layout/{$session['layout']}/pic/bckGreyAll2.png);
	background-repeat:repeat-x;
}


/* gtab Popup-Tabelle außen */
.lmbPopupDiv{
	padding-left:8px;
	padding-bottom:4px;
	width:100%;
	background-color:{$farbschema['WEB14']};
}
.lmbPopupGtab{
	background-color:{$farbschema['WEB14']};
	width:100%;
	overflow:visible;
}


/* gtab Tabelle Relation Tabmenu */

.lmbGtabTabmenuTable{
	border-collapse:collapse;
	border-spacing:1px;
}

.lmbGtabTabmenuActive{
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
	cursor:pointer;
}

.lmbGtabTabmenuInactive{
	background-color:{$farbschema['WEB13']};
	color:$fontc3;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	cursor:pointer;
}

.lmbGtabTabmenuSpace{
	width:100%;
	background-color:{$farbschema['WEB14']};
	border:1px solid {$farbschema['WEB14']};
	border-bottom:1px solid {$farbschema['WEB3']};
	border-left:1px solid {$farbschema['WEB3']};
	font-size:{$fontsize11};
	padding:3px;
	padding-top:4px;
}



/* gtab Tabelle Subheader Tabmenu */

.lmbGtabSubheaderTable{
	width:100%;
	border-collapse:collapse;
	border-spacing:1px;
}

.lmbGtabSubheaderActive{
	background-color:{$farbschema['WEB8']};
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
	cursor:pointer;
}

.lmbGtabSubheaderInactive{
	background-color:{$farbschema['WEB13']};
	color:$fontc3;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	cursor:pointer;
	border:1px solid {$farbschema['WEB3']};
}


/* Tabulator Tabelle Formular oben positioniert*/

.lmbGtabTabulatorFrame{
	background-color:{$farbschema['WEB14']};
	border: 1px solid {$farbschema['WEB3']};
	z-index:1;
}

.lmbGtabTabulatorActive{
	background-color:{$farbschema['WEB14']};
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
	cursor:pointer;
	float:left;
	position:relative;
	bottom:-1px;
	z-index:2;
}

.lmbGtabTabulatorInactive{
	background-color:{$farbschema['WEB8']};
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
	cursor:pointer;
	float:left;
}

.lmbGtabTabulatorSpace{
	font-size:{$fontsize11};
	padding:3px;
	padding-top:4px;
	border-bottom:1px solid {$farbschema['WEB3']};
	border-bottom:none;
}


/* Tabulator Tabelle Formular links positioniert */

.lmbGtabTabulatorLeftFrame{
	background-color:{$farbschema['WEB14']};
	border-collapse:collapse;
	border: 1px solid {$farbschema['WEB3']};
	border-left:none;
}

.lmbGtabTabulatorLeftActive{
	background-color:{$farbschema['WEB14']};
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:5px;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-right:1px solid transparent;
	cursor:pointer;
}

.lmbGtabTabulatorLeftInactive{
	background-color:{$farbschema['WEB13']};
	color:$fontc3;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:5px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	cursor:pointer;
}

.lmbGtabTabulatorLeftSpace{
	font-size:{$fontsize11};
	opacity:0.4;
	height:100%;
	overflow:hidden;
	border-right:1px solid {$farbschema['WEB3']};
}





/* gtab Tabelle Liste Kopf */

.lmbGtabTop{
	padding:5px;
	padding-left:10px;
	padding-right:10px;
	height:1px;
}

.gtabHeaderTop{
	border-top:1px solid {$farbschema['WEB3']};
	height:8px;
}

.gtabHeaderMenuTR{
	overflow:hidden;
	border-left:1px solid {$farbschema['WEB3']};
	border-right:1px solid {$farbschema['WEB3']};
}

.gtabHeaderMenuTD{
	cursor:pointer;
	color:$fontc6;
	padding-left:5px;
	padding-right:5px;
	height:18px;
}

.gtabHeaderMenuTDhover{
	text-decoration:underline;
	cursor:pointer;
	padding-left:5px;
	padding-right:5px;
	height:18px;
}

.gtabHeaderSymbolTR{
	border-left:1px solid {$farbschema['WEB3']};
	border-right:1px solid {$farbschema['WEB3']};
}

.gtabHeaderSymbolTD{
	margin-top:0px;
	height:23px;
}

.gtabHeaderInputTR{
	border-left:1px solid {$farbschema['WEB3']};
	border-right:1px solid {$farbschema['WEB3']};
}

.gtabHeaderInputTD{
	background-color: {$farbschema['WEB11']};
	overflow:hidden;
	padding:2px;
	border:1px solid {$farbschema['WEB13']};
}

.gtabHeaderInputINP{
	background-color:{$farbschema['WEB8']};
	border: 1px solid {$farbschema['WEB2']};
    background-image: url("../../pic/find_2.png");
    padding-left:14px;
    background-repeat: no-repeat;
}

.gtabHeaderInputINPajax{
    background-image: url("../../pic/find_green_2.png");
}

.gtabHeaderTitleTR{
	border-left:1px solid {$farbschema['WEB3']};
	border-right:1px solid {$farbschema['WEB3']};
}

.gtabHeaderTitleTD{
	background-color: {$farbschema['WEB11']};
	vertical-align: top;
	overflow:hidden;
	cursor:col-resize;
	padding:2px;
	border:1px solid {$farbschema['WEB13']};
}


.gtabHeaderTitleTDOver {
	background-color: {$farbschema['WEB11']};
    background-image: url("../../pic/silk_icons/arrow_bullet_left.gif");
    background-repeat: no-repeat;
    padding-left: 20px;
}

.gtabHeaderTitleTDItem{
	font-weight: bold;
	cursor: pointer;
	float: left;
	overflow:hidden;
	white-space:nowrap;
}

.gtabHeaderTitleTDItemOver{
	background-color: {$farbschema['WEB11']};
	border: 1px solid grey;
	padding-left: 5;
	padding-right: 5;
	font-weight: bold;
	cursor: pointer;
}

.gtabHeaderBodyTR{
	border-left:1px solid {$farbschema['WEB3']};
	border-right:1px solid {$farbschema['WEB3']};
}


/* gtab Tabelle Liste Body */

.lmbfringeGtabSearch{
	background-color: {$farbschema['WEB11']};
	border-collapse:collapse;
	border-spacing:1px;
	table-layout:fixed;
}

.lmbfringeGtabTable{
	background-color: {$farbschema['WEB4']};
	border-collapse:collapse;
	border-spacing:1px;
	table-layout:fixed;
}

.lmbfringeGtabBody{
	background-color: {$farbschema['WEB11']};
	border-collapse:collapse;
	border-spacing:1px;
	opacity: .8;
}

.gtabBodyTR{
	background-color:{$farbschema['WEB13']};
	overflow:hidden;
}

.gtabBodyTRColorA{
	background-color:{$bgcolor1};
}

.gtabBodyTRColorB{
	background-color:none;
}


.gtabBodyTD{
    vertical-align: top;
    overflow:hidden;
    padding:2px;
    border:1px solid {$farbschema['WEB11']};
}


.gtabBodyTDCL{
    vertical-align: top;
    overflow:hidden;
    padding:2px;
    border:1px solid {$farbschema['WEB11']};
}

.gtabBodyINP{
	background-color: rgba(255, 255, 255, 0.5);
}



/* gtab Tabelle Liste Fuß */

.lmbGtabBottom{
	height:5px;
	width:100%;
}

.gtabFooterTAB{
	border-left:1px solid {$farbschema['WEB3']};
	border-right:1px solid {$farbschema['WEB3']};
	border-bottom:1px solid {$farbschema['WEB3']};
	background-image:url(../../layout/{$session['layout']}/pic/bckGreyAll2.png);
	background-repeat:repeat-x;
	background-position:bottom;
	height:23px;
	vertical-align:middle;
	width:100%;
	border-collapse:collapse;
}

.gtabFooterTR{

}

.gtabFooterTD{

}




/* gtab Tabelle Details */

.gtabBodyDetailFringe{
	background-color:{$farbschema['WEB8']};
	border:1px solid {$farbschema['WEB3']};
}

.gtabBodyDetailFringeItem{
	color:$fontc6;
	padding-left:5px;
	padding-right:5px;
}

.gtabBodyDetailTopBorder{
	height:1px;
	background-color:{$farbschema['WEB3']};
}

.gtabBodyDetailTopSpace{
	height:5px;
}

.gtabBodyDetailSubheader{

}

.gtabBodyDetailBody{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:right;
}


.gtabBodyDetailTab{

}

.gtabBodyDetailTR{

}

.gtabBodyDetailTD{
	border:none;
	padding:5px;
	padding-right:30px;
	width:30%;
}

.gtabBodyDetailNeedTitle{
	color:red;
}

.gtabBodyDetailNeedVal{
	//background-color:#FBC7C8 !important;
	border: 2px solid red !important;
}

.gtabBodyDetailValTD{
	border:none;
	padding-top:4px;
	padding-bottom:4px;
	padding-right:20px;
	overflow:hidden;
	width:70%;
}

.gtabBodyDetailValLI{
	list-style-type:disc;
}

.gtabGrouping{
	background-color:{$farbschema['WEB7']};
	border-bottom:1px solid {$farbschema['WEB12']};
	border-top:1px solid {$farbschema['WEB12']};
	font-weight:bold;
	padding-right:20px;
}

.gtabchange{
	font-weight: normal;
    width: 95%;
}

DIV.gtabchange{
    border: 1px solid {$farbschema['WEB3']};
    min-height: 18px;
    box-sizing: border-box;
    padding:2px;
}

INPUT.gtabchange, TEXTAREA.gtabchange, SELECT.gtabchange{
	font-weight: normal;
	background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    font-size: {$umgvar['fontsize']}px;
}

.fgtabchange{
	background-color: {$farbschema['WEB8']};
    border: 1px solid {$farbschema['WEB3']};
    padding:0px;
    margin:0px;
    font-size: {$umgvar['fontsize']}px;
	opacity:0.7;
	filter:Alpha(opacity=70);
	box-sizing: border-box;
}

INPUT.fgtabchange, TEXTAREA.fgtabchange, SELECT.fgtabchange, TABLE.fgtabchange, TD.fgtabchange{
	background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
	opacity:1;
	filter:Alpha(opacity=100);
	font-size: {$umgvar['fontsize']}px;
	padding:2px;
}

DIV.fgtabchange{
    min-height: 18px;
	overflow-y:auto;
	padding:2px;
	box-sizing: border-box;
}

.gtabchangeIframe{
	background-color:{$farbschema['WEB9']};
	border: 1px solid {$farbschema['WEB3']};
	padding:5px;
	padding-top:20px;
	padding-bottom:20px;
	cursor:move;
 	width:700px;
 	height:50%;
 	position:absolute;
}

.gtabchangeInnerFrame{
	background-color: {$farbschema['WEB8']};
    border: 1px solid {$farbschema['WEB3']};
}

INPUT.gmultilang {
    background-image: url("../../pic/fa-language_16.png");
    background-repeat: no-repeat;
    background-position: right center;
}

/* gtab Tabelle Group */

.gtabringeGroupBody {
	font-weight: normal;
    border: 1px solid {$farbschema['WEB3']};
    border-top: none;
	width: 94%;
}




/* gtab Tabelle Relation */

.gtabHeaderRelationTAB{
	border: 1px solid {$farbschema['WEB3']};
}

.gtabFringeRelationBody{
	background-color:{$farbschema['WEB9']};
	border: 1px solid {$farbschema['WEB3']};
    width: 94%;
}




/*INPUT[readonly][id^=g_]*/



/* Tabelle allgemein */

.tabfringe{
	border: 1px solid {$farbschema['WEB3']};
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:left;
	background-color:{$farbschema['WEB13']};
	padding:0px;
}
        
.tabfringe.lmbinfo {
        margin: 20px;
}

.tabHeader{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGrey.png);
	background-position:top;
	background-repeat:repeat-x;
	background-color:{$farbschema['WEB13']};
}

.tabHeaderItem{
	font-weight:bold;
	padding:5px;
        vertical-align: top;
}

.tabSubHeader{
	background-color:{$farbschema['WEB7']};
}

.tabSubHeaderItem{

}

.tabItem2{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGrey.png);
	background-position:top;
	background-repeat:repeat-x;
	background-color:{$farbschema['WEB5']};
}

.tabBody{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:left;
}

.tabFooter{

}

.vAlignMiddle{
        vertical-align: middle;
}
        
.vAlignTop{
     vertical-align: top;
}
        
.txtAlignCenter{
        text-align: center;
}
.txtAlignLeft{
        text-align: left;
}

/* Reiter vertikal Tabelle  */

.tabHpoolfringe{
	border:1px solid {$farbschema['WEB3']};
	background-color:{$farbschema['WEB13']};
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:left;
	border-collapse:collapse;
	border-spacing:1px;
	padding:5px;
}

.tabHpoolItemTR{
	border:none;
}

.tabHpoolItemActive{
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	padding-left:20px;
	padding-right:20px;
	background-color:{$farbschema['WEB9']};
	border:1px solid {$farbschema['WEB3']};
	border-right:none;
	border-bottom:none;
	cursor:pointer;
}

.tabHpoolItemInactive{
	background-color:{$farbschema['WEB13']};
	color:$fontc3;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	cursor:pointer;
}

.tabHpoolItemSpaceGtab{
	height:100%;
	background-color:{$farbschema['WEB13']};
	border:1px solid {$farbschema['WEB3']};
}




/* Reiter horizontal Tabelle */

.tabpoolfringe{
	border:1px solid {$farbschema['WEB3']};
	background-color:{$farbschema['WEB13']};
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:left;
	border-top:none;
	border-collapse:collapse;
	border-spacing:1px;
	padding:5px;
}

.tabpoolItemTR{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:left;
}

.tabpoolItemActive{
	color:$fontc7;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
	cursor:pointer;
}

.tabpoolItemInactive{
	background-color:{$farbschema['WEB13']};
	color:$fontc3;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	cursor:pointer;
}


.tabpoolItemSpace{
	width:100%;
	border-bottom:1px solid {$farbschema['WEB3']};
	background-color:{$farbschema['WEB14']};
}

.tabpoolItemSpaceGtab{
	width:100%;
	border-bottom:1px solid {$farbschema['WEB3']};
}

.tabpoolItemInactive:hover{
	background-color:{$farbschema['WEB3']};
}

.tabIconInactive:hover{
	background-color:{$farbschema['WEB3']};
}


/* Kontextmenus */

.lmbContextMenu{
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:right;
	background-color:{$farbschema['WEB9']};
	border:1px solid gray;
	color:black;
	position:absolute;
	min-width:150px;

	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;

	-moz-box-shadow:2px 2px #BBBBBB;
	-webkit-box-shadow:2px 2px #BBBBBB;
	box-shadow:2px 2px #BBBBBB;
}

.lmbContextLink{
	text-decoration:none;
	color:black;
	cursor:pointer;
	display:block;
	padding:2px;
        background: linear-gradient(90deg, {$farbschema['WEB7']} 20px, transparent 0px);
}

.lmbContextLink:hover{
	text-decoration:underline;
	color:blue;
}

.lmbContextLink:visited{
	color:black;
}

.lmbContextRow{
	text-decoration:none;
	display:block;
	padding:2px;
        background: linear-gradient(90deg, {$farbschema['WEB7']} 20px, transparent 0px);
}

.lmbContextRowSeparator{
        background: linear-gradient(90deg, {$farbschema['WEB7']} 20px, transparent 0px);
	text-align:center;
	padding:4px;
}

.lmbContextRowSeparatorLine{
	height:1px;
	background-color:gray;
	position:absolute;
	width:95%;
	left:4px;
	overflow:hidden;
}

.lmbContextLeft {
	vertical-align:middle;
}

.lmbContextRight {
	vertical-align:middle;
	margin-left:6px;
	right:4px;
	position:absolute;
}

.lmbContextItem {
	padding-left:24px;
	padding-right:12px;
}

.lmbContextItemIcon {
	padding-left:6px;
	padding-right:12px;
}

.lmbContextTop {
	cursor:pointer;
	padding-bottom:8px;
}

.lmbContextHeader {
	text-align:center;
	height:20px;
	font-weight: bold;
	background-image: url("../../layout/skalar/pic/bckLightGrey.png");
    background-position: center center;
    background-repeat: repeat-x;
    padding-top: 4px;
}



/* Sonstiges */

/*ajax container*/
DIV.ajax_container {
	z-index:99999;
	background-image:url(../../layout/{$session['layout']}/pic/bckLightGreyHLine.png);
	background-repeat:repeat-y;
	background-position:right;
	background-color:{$farbschema['WEB9']};
	border:1px solid gray;
	color:black;
}


/*container allgemein*/
.lmb_container {
	padding:5px;
	background-color:{$farbschema['WEB9']};
	border:1px solid {$farbschema['WEB3']};
}

.lmbIconInactive {
	opacity:0.3;
	filter:Alpha(opacity=30);
}

.lmbFileVersionDiff {
	border-width:1px;
	border-color:#CCCCCC;
	background-color:#FFFF9C;
	border-style:solid;
	position:absolute;z-index:999999;
	overflow:visible;
	padding:10px;
}

.lmbUploadProgress {
	height: 14px;
	width:100%;
	border-radius: 4px;
	border:1px solid {$farbschema['WEB5']};
	background-color:{$farbschema['WEB11']};
	background-image:url(../../pic/animated-overlay.gif);
	background-repeat:repeat-x;
	font-style:italic;
	float:left;
	display:none;
}

.lmbUploadProgressBar {
	background-color:{$farbschema['WEB7']};
	width:0%;
	height:100%;
	overflow:hidden;
	border-radius: 4px;
	border-right:1px solid {$farbschema['WEB5']};
}

.lmbUploadProgressInfo {
	font-style:italic;
	position:absolute;
	color:{$farbschema['WEB12']};
}

.lmbUploadForm{

}

.progress {
    height:20px;
    margin-bottom:20px;
    overflow:hidden;
    background-color:#f5f5f5;
    border-radius:4px;
    -webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);
}

.progress-bar {
    float:left;
    width:0;
    height:100%;
    font-size:12px;
    line-height:20px;
    color:#fff;
    text-align:center;
    background-color:#337ab7;
    -webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);
    box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);
    -webkit-transition:width .6s ease;
    -o-transition:width .6s ease;transition:width .6s ease;
}

.lmbUploadDragenter {
	background: {$farbschema['WEB14']};
	opacity: .2;
	filter: alpha(opacity=20);
}

.modalOverlay {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    background-color: rgba(0,0,0,0.3); /* black semi-transparent */
    z-index: 99990;
}

.markAsActive{
	background-image:url(../../pic/ok.gif);
	background-position:right;
	background-repeat:no-repeat;
	padding-right: 20px;
}

.markAsDisabled{
	opacity: 0.60;
}

.markForDelete{
	color:red;
	text-decoration:line-through;
}

/*waitsymbol*/
.lmbWaitSymbol{
	background-color:transparent;
	background-image:url(../../{$waitsymbol});
	background-repeat:no-repeat;
	z-index:99999;
	height:150px;
	width:150px;
}
.lmbInputSelectIcon{
    background-image: url("../../pic/scrolldown.gif");
    background-repeat: no-repeat;
}


/*openwysiwyg*/
/* Table Textarea */
.tableTextareaEditor { 
	background-color: {$farbschema['WEB13']};
}

.iframeText { 
	background-color: {$farbschema['WEB9']};
}


	
.thumpPreview{
	position:absolute;
	top:50%;
	left:50%;
	cursor:pointer;
	padding:10px;
	background-color:#CCCCCC;
	-moz-box-shadow:4px 4px #333333;
	-webkit-box-shadow:4px 4px #333333;
	box-shadow:4px 4px #666666;
        z-index:99990;
}
	
	
/*Global*/


A:link {
	text-decoration: none;
	color: black;
}

A:visited {
	text-decoration: none;
	color: black;
}

A:hover {
	text-decoration: underline;
	color: {$farbschema['WEB12']};
	cursor:pointer
}


.link {
	text-decoration: none;
	color: black;
	cursor: pointer;
}

SPAN.HEADER {
	FONT-STYLE: oblique;
	font-size: {$fontsize11};
	color: #000000;
}

DEL {
	background-color: #fcc;
}

INS {
	background-color: #cfc;
}

DIV.nav_menu {
	font-size: {$fontsize11};
	font-weight: normal;
	line-height: 17px;
	color: {$farbschema['WEB12']};
	cursor: pointer;
	vertical-align: middle;
	height: 17px;
}

TR.nav_menu {
	font-size: {$fontsize11};
	font-weight: normal;
	line-height: 17px;
	color: {$farbschema['WEB12']};
	cursor: pointer;
	vertical-align: middle;
	height: 17px;
}
TD.nav_menu {
	font-size: {$fontsize11};
	font-weight: normal;
	line-height: 17px;
	color: {$farbschema['WEB12']};
	cursor: pointer;
	vertical-align: middle;
	height: 17px;
}

TABLE.popmenu {
    border-collapse: collapse;
    border-spacing: 0;
    EMPTY-CELLS: show;
    table-layout: fixed;
    padding: 0;
    border: none;
    background-color: {$farbschema['WEB3']};
}

TR.popmenu {
	font-weight: normal;
	color: #000000;
    overflow: hidden;
    background-color: {$farbschema['WEB3']};
}

TD {
	font-weight: normal;
	color: #000000;
}

TR.head {
    background-color:{$farbschema['WEB6']};
}

TR.headsub {
    background-color:{$farbschema['WEB7']};
}

TD.head {
    cursor: e-resize;
    vertical-align: top;
    height: 20px;
    border: 1px solid {$farbschema['WEB7']};
}

TD.data {
    vertical-align: top;
    border: 1px solid {$farbschema['WEB7']};
}

TD.datachange {
    vertical-align: top;
    border: 1px solid #FFFFFF;
}

TD.datal {
    vertical-align: top;
    border: 1px solid {$farbschema['WEB7']};
    color: #999999;
}

TD.datachangel {
    vertical-align: top;
    border: 1px solid #FFFFFF;
    color: #999999;
}

TD.main_info {
	font-size: {$fontsize12};
	font-weight: normal;
	color: {$farbschema['WEB12']};
	cursor: pointer;
}

TD.active_tag {
	font-weight: normal;
	color: {$farbschema['WEB12']};
	cursor: pointer;
	padding: 2px;
	padding-left: 4px;
	padding-right: 4px;
	border-left: 1px solid {$farbschema['WEB3']};
	border-right: 1px solid {$farbschema['WEB3']};
	border-top: 1px solid {$farbschema['WEB3']};
	background-color: {$farbschema['WEB6']};
}

TD.inactive_tag {
	font-weight: normal;
	color: {$farbschema['WEB12']};
	cursor: pointer;
	padding: 2px;
	padding-left: 4px;
	padding-right: 4px;
	border-right: 1px solid {$farbschema['WEB3']};
	border-BOTTOM: 1px solid {$farbschema['WEB3']};
	background-color: {$farbschema['WEB1']};
}

SELECT {
	font-weight: normal;
	background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    font-size: {$umgvar['fontsize']}px;
}

SELECT.searchfield {
	font-weight: normal;
	background-color: {$farbschema['WEB9']};
    border: 2px inset grey;
    width: 480px;
	border: 1px solid {$farbschema['WEB4']};
}

SELECT.gtaberg {
	font-weight: normal;
	background-color: {$farbschema['WEB9']};
    border: none;
}

SELECT.contextmenu {
	font-weight: normal;
	background-color: {$farbschema['WEB9']};
}


INPUT.searchfield {
	font-weight: normal;
	background-color: {$farbschema['WEB9']};
    border: 1px solid {$farbschema['WEB4']};
}

INPUT {
	font-weight: normal;
	background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    font-size: {$umgvar['fontsize']}px;
}

INPUT.checkbox {
	font-weight: normal;
	background-color: transparent;
    border: none;
    height:12px;
    width:12px;
}

INPUT.gtaberg {
	font-weight: normal;
	background-color: transparent;
    border: none;
    width: 480px;
}

INPUT.upload {
	font-weight: normal;
	background-color: {$farbschema['WEB9']};
    border: 1px solid {$farbschema['WEB3']};
    width: 320px;
}

INPUT.submit {
	font-size: {$fontsize11};
	background-color: {$farbschema['WEB9']};
    border: 1px solid {$farbschema['WEB10']};
    padding:3px;
    padding-left:10px;
    padding-right:10px;
}

TEXTAREA {
	font-family: {$umgvar['font']};
	resize: none;
	font-weight: normal;
	background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    font-size: {$umgvar['fontsize']}px;
    resize:none;
}

FORM {
	font-weight: normal;
	color: #000000;
	margin:0px;
	padding:0px;
}

/* jquery UI ------------------------------------------------------------------------*/


.ui-selected{
	//background-color:orange !important;
	-moz-box-shadow: 3px 3px 3px #619A00;
	-webkit-box-shadow: 3px 3px 3px #619A00;
	box-shadow: 3px 3px 3px #619A00;
}

.ui-state-disabled{
	opacity: 0.60;
}








/*fullcalendar*/
/*!
 * FullCalendar v1.6.4 Stylesheet
 * Docs & License: http://arshaw.com/fullcalendar/
 * (c) 2013 Adam Shaw
 */


.fc {
	direction: ltr;
	text-align: left;
	}
	
.fc table {
	border-collapse: collapse;
	border-spacing: 0;
	}
	
html .fc,
.fc table {
	font-size: 1em;
	}
	
.fc td,
.fc th {
	padding: 0;
	vertical-align: top;
	}



/* Header
------------------------------------------------------------------------*/

.fc-header td {
	white-space: nowrap;
	}

.fc-header-left {
	width: 25%;
	text-align: left;
	}
	
.fc-header-center {
	text-align: center;
	}
	
.fc-header-right {
	width: 25%;
	text-align: right;
	}
	
.fc-header-title {
	display: inline-block;
	vertical-align: top;
	}
	
.fc-header-title h2 {
	margin-top: 0;
	white-space: nowrap;
	}
	
.fc .fc-header-space {
	padding-left: 10px;
	}
	
.fc-header .fc-button {
	margin-bottom: 1em;
	vertical-align: top;
	}
	
/* buttons edges butting together */

.fc-header .fc-button {
	margin-right: -1px;
	}
	
.fc-header .fc-corner-right,  /* non-theme */
.fc-header .ui-corner-right { /* theme */
	margin-right: 0; /* back to normal */
	}
	
/* button layering (for border precedence) */
	
.fc-header .fc-state-hover,
.fc-header .ui-state-hover {
	z-index: 2;
	}
	
.fc-header .fc-state-down {
	z-index: 3;
	}

.fc-header .fc-state-active,
.fc-header .ui-state-active {
	z-index: 4;
	}
	
	
	
/* Content
------------------------------------------------------------------------*/
	
.fc-content {
	clear: both;
	zoom: 1; /* for IE7, gives accurate coordinates for [un]freezeContentHeight */
	}
	
.fc-view {
	width: 100%;
	overflow: hidden;
	}
	
	

/* Cell Styles
------------------------------------------------------------------------*/

.fc-widget-header,    /* <th>, usually */
.fc-widget-content {  /* <td>, usually */
	border: 1px solid #ccc;
	background: linear-gradient(to bottom, {$farbschema['WEB13']} 20%, {$farbschema['WEB11']} );
	}
	
.fc-state-highlight { /* <td> today cell */ /* TODO: add .fc-today to <th> */
	background: #fcf8e3;
	}
	
.fc-cell-overlay { /* semi-transparent rectangle while dragging */
	background: {$farbschema['WEB7']};
	opacity: .3;
	filter: alpha(opacity=30); /* for IE */
	}
	
.fc-resourceName{
	border: 1px solid #ccc;
	background: linear-gradient(to bottom, {$farbschema['WEB13']} 20%, {$farbschema['WEB11']} );
	font-weight:bold;
	font-style:italic;
	}

/* Buttons
------------------------------------------------------------------------*/

.fc-button {
	position: relative;
	display: inline-block;
	padding: 0 .6em;
	overflow: hidden;
	height: 1.9em;
	line-height: 1.9em;
	white-space: nowrap;
	cursor: pointer;
	}
	
.fc-state-default { /* non-theme */
	border: 1px solid;
	}

.fc-state-default.fc-corner-left { /* non-theme */
	border-top-left-radius: 4px;
	border-bottom-left-radius: 4px;
	}

.fc-state-default.fc-corner-right { /* non-theme */
	border-top-right-radius: 4px;
	border-bottom-right-radius: 4px;
	}

/*
	Our default prev/next buttons use HTML entities like &lsaquo; &rsaquo; &laquo; &raquo;
	and we'll try to make them look good cross-browser.
*/

.fc-text-arrow {
	margin: 0 .1em;
	font-size: 2em;
	font-family: "Courier New", Courier, monospace;
	vertical-align: baseline; /* for IE7 */
	}

.fc-button-prev .fc-text-arrow,
.fc-button-next .fc-text-arrow { /* for &lsaquo; &rsaquo; */
	font-weight: bold;
	}
	
/* icon (for jquery ui) */
	
.fc-button .fc-icon-wrap {
	position: relative;
	float: left;
	top: 50%;
	}
	
.fc-button .ui-icon {
	position: relative;
	float: left;
	margin-top: -50%;
	*margin-top: 0;
	*top: -50%;
	}
	
/*
  button states
  borrowed from twitter bootstrap (http://twitter.github.com/bootstrap/)
*/

.fc-state-default {
	background-color: #f5f5f5;
	background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
	background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
	background-repeat: repeat-x;
	border-color: #e6e6e6 #e6e6e6 #bfbfbf;
	border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
	color: #333;
	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	}

.fc-state-hover,
.fc-state-down,
.fc-state-active,
.fc-state-disabled {
	color: #333333;
	background-color: #e6e6e6;
	}

.fc-state-hover {
	color: #333333;
	text-decoration: none;
	background-position: 0 -15px;
	-webkit-transition: background-position 0.1s linear;
	   -moz-transition: background-position 0.1s linear;
	     -o-transition: background-position 0.1s linear;
	        transition: background-position 0.1s linear;
	}

.fc-state-down,
.fc-state-active {
	background-color: #cccccc;
	background-image: none;
	outline: 0;
	box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
	}

.fc-state-disabled {
	cursor: default;
	background-image: none;
	opacity: 0.65;
	filter: alpha(opacity=65);
	box-shadow: none;
	}

	

/* Global Event Styles
------------------------------------------------------------------------*/

.fc-event-container > * {
	z-index: 8;
	}

.fc-event-container > .ui-draggable-dragging,
.fc-event-container > .ui-resizable-resizing {
	z-index: 9;
	}
	 
.fc-event {
	border: 1px solid #3a87ad; /* default BORDER color */
	background-color: #3a87ad; /* default BACKGROUND color */
	color: #fff;               /* default TEXT color */
	font-size: .85em;
	cursor: default;
	}

a.fc-event {
	text-decoration: none;
	}
	
a.fc-event,
.fc-event-draggable {
	cursor: pointer;
	}
	
.fc-rtl .fc-event {
	text-align: right;
	}

.fc-event-inner {
	width: 100%;
	height: 100%;
	overflow: hidden;
	}
	
.fc-event-time,
.fc-event-title {
	padding: 0 1px;
	}
	
.fc .ui-resizable-handle {
	display: block;
	position: absolute;
	z-index: 99999;
	overflow: hidden; /* hacky spaces (IE6/7) */
	font-size: 300%;  /* */
	line-height: 50%; /* */
	}
	
	
	
/* Horizontal Events
------------------------------------------------------------------------*/

.fc-event-hori {
	border-width: 1px 0;
	margin-bottom: 1px;
	}

.fc-ltr .fc-event-hori.fc-event-start,
.fc-rtl .fc-event-hori.fc-event-end {
	border-left-width: 1px;
	border-top-left-radius: 3px;
	border-bottom-left-radius: 3px;
	}

.fc-ltr .fc-event-hori.fc-event-end,
.fc-rtl .fc-event-hori.fc-event-start {
	border-right-width: 1px;
	border-top-right-radius: 3px;
	border-bottom-right-radius: 3px;
	}
	
/* resizable */
	
.fc-event-hori .ui-resizable-e {
	top: 0           !important; /* importants override pre jquery ui 1.7 styles */
	right: -3px      !important;
	width: 7px       !important;
	height: 100%     !important;
	cursor: e-resize;
	}
	
.fc-event-hori .ui-resizable-w {
	top: 0           !important;
	left: -3px       !important;
	width: 7px       !important;
	height: 100%     !important;
	cursor: w-resize;
	}
	
.fc-event-hori .ui-resizable-handle {
	_padding-bottom: 14px; /* IE6 had 0 height */
	}
	
	
	
/* Reusable Separate-border Table
------------------------------------------------------------*/

table.fc-border-separate {
	border-collapse: separate;
	}
	
.fc-border-separate th,
.fc-border-separate td {
	border-width: 1px 0 0 1px;
	}
	
.fc-border-separate th.fc-last,
.fc-border-separate td.fc-last {
	border-right-width: 1px;
	}
	
.fc-border-separate tr.fc-last th,
.fc-border-separate tr.fc-last td {
	border-bottom-width: 1px;
	}
	
.fc-border-separate tbody tr.fc-first td,
.fc-border-separate tbody tr.fc-first th {
	border-top-width: 0;
	}
	
.fc-view-resourceWeek tr:nth-child(even), .fc-view-resourceMonth tr:nth-child(even), .fc-view-resourceDay tr:nth-child(even) {
    background: #fef9e1;
    }
    
.fc-view-resourceWeek td, .fc-view-resourceMonth td, .fc-view-resourceDay td {
    background: none;
    }
    
    
    

/* Month View, Basic Week View, Basic Day View
------------------------------------------------------------------------*/

.fc-grid th {
	text-align: center;
	}

.fc .fc-week-number {
	width: 22px;
	text-align: center;
	}

.fc .fc-week-number div {
	padding: 0 2px;
	}
	
.fc-grid .fc-day-number {
	float: right;
	padding: 0 2px;
	}
	
.fc-grid .fc-other-month .fc-day-number {
	opacity: 0.3;
	filter: alpha(opacity=30); /* for IE */
	/* opacity with small font can sometimes look too faded
	   might want to set the 'color' property instead
	   making day-numbers bold also fixes the problem */
	}
	
.fc-grid .fc-day-content {
	clear: both;
	padding: 2px 2px 1px; /* distance between events and day edges */
	}
	
/* event styles */
	
.fc-grid .fc-event-time {
	font-weight: bold;
	}
	
/* right-to-left */
	
.fc-rtl .fc-grid .fc-day-number {
	float: left;
	}
	
.fc-rtl .fc-grid .fc-event-time {
	float: right;
	}
	
	

/* Agenda Week View, Agenda Day View
------------------------------------------------------------------------*/

.fc-agenda table {
	border-collapse: separate;
	}
	
.fc-agenda-days th {
	text-align: center;
	}
	
.fc-agenda .fc-agenda-axis {
	width: 50px;
	padding: 0 4px;
	vertical-align: middle;
	text-align: right;
	white-space: nowrap;
	font-weight: normal;
	}

.fc-agenda .fc-week-number {
	font-weight: bold;
	}
	
.fc-agenda .fc-day-content {
	padding: 2px 2px 1px;
	}
	
/* make axis border take precedence */
	
.fc-agenda-days .fc-agenda-axis {
	border-right-width: 1px;
	}
	
.fc-agenda-days .fc-col0 {
	border-left-width: 0;
	}
	
/* all-day area */
	
.fc-agenda-allday th {
	border-width: 0 1px;
	}
	
.fc-agenda-allday .fc-day-content {
	min-height: 34px; /* TODO: doesnt work well in quirksmode */
	_height: 34px;
	}
	
/* divider (between all-day and slots) */
	
.fc-agenda-divider-inner {
	height: 2px;
	overflow: hidden;
	}
	
.fc-widget-header .fc-agenda-divider-inner {
	background: #eee;
	}
	
/* slot rows */
	
.fc-agenda-slots th {
	border-width: 1px 1px 0;
	}
	
.fc-agenda-slots td {
	border-width: 1px 0 0;
	background: none;
	}
	
.fc-agenda-slots td div {
	height: 20px;
	}
	
.fc-agenda-slots tr.fc-slot0 th,
.fc-agenda-slots tr.fc-slot0 td {
	border-top-width: 0;
	}

.fc-agenda-slots tr.fc-minor th,
.fc-agenda-slots tr.fc-minor td {
	border-top-style: dotted;
	}
	
.fc-agenda-slots tr.fc-minor th.ui-widget-header {
	*border-top-style: solid; /* doesn't work with background in IE6/7 */
	}
	


/* Vertical Events
------------------------------------------------------------------------*/

.fc-event-vert {
	border-width: 0 1px;
	}

.fc-event-vert.fc-event-start {
	border-top-width: 1px;
	border-top-left-radius: 3px;
	border-top-right-radius: 3px;
	}

.fc-event-vert.fc-event-end {
	border-bottom-width: 1px;
	border-bottom-left-radius: 3px;
	border-bottom-right-radius: 3px;
	}
	
.fc-event-vert .fc-event-time {
	white-space: nowrap;
	font-size: 10px;
	}

.fc-event-vert .fc-event-inner {
	position: relative;
	z-index: 2;
	}
	
.fc-event-vert .fc-event-bg { /* makes the event lighter w/ a semi-transparent overlay  */
	position: absolute;
	z-index: 1;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: #fff;
	opacity: .25;
	filter: alpha(opacity=25);
	}
	
.fc .ui-draggable-dragging .fc-event-bg, /* TODO: something nicer like .fc-opacity */
.fc-select-helper .fc-event-bg {
	display: none\9; /* for IE6/7/8. nested opacity filters while dragging don't work */
	}
	
/* resizable */
	
.fc-event-vert .ui-resizable-s {
	bottom: 0        !important; /* importants override pre jquery ui 1.7 styles */
	width: 100%      !important;
	height: 8px      !important;
	overflow: hidden !important;
	line-height: 8px !important;
	font-size: 11px  !important;
	font-family: monospace;
	text-align: center;
	cursor: s-resize;
	}
	
.fc-agenda .ui-resizable-resizing { /* TODO: better selector */
	_overflow: hidden;
	}
	

/* holidays
------------------------------------------------------------------------*/

.fcr-holiday-title-M {
    top: 60px;
}
.fcr-holiday-title-M, .fcr-holiday-title-aW, .fcr-holiday-title-bW {
    color: #aaaaaa;
    font-weight: bold;
    height: 32px;
    left: 2px;
    overflow: hidden;
    position: absolute;
    width: 98%;
}


/* Resource month, Resource week, Resource day
------------------------------------------------------------------------*/

td.fc-resourceName {
	border-left: 1px solid #ddd;
	border-top: 1px solid #ddd;
	padding: 0px 5px 0px 5px;
	white-space: nowrap;
	font-weight:bold;
}
tr.fc-last td.fc-resourceName {
	border-bottom: 1px solid #ddd;	
}
th.fc-resourceName {
	border-bottom: 1px solid #ddd;
}










/* Exif ------------------------------------------------------------------------*/
.EXIF_Level1A {
	background-color:{$farbschema['WEB2']};
	padding: 2px;
}
.EXIF_Level1B{
	background-color:{$farbschema['WEB11']};
	padding: 2px;
}
.EXIF_Level2A {
	background-color:{$farbschema['WEB8']};
	padding: 2px;
}
.EXIF_Level2B{
	background-color:{$farbschema['WEB14']};
	padding: 2px;
}
.EXIF_Content{
	color: black;
	padding: 2px;
}






EOD;

fwrite($cssfile, $buf);
fclose($cssfile);

?>