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

/*
 * Generate legacy layout.css for main content
 * This file will be deprecated an removed in future versions
 * 
 */

global $umgvar;
global $session;


/*
WEB5 : Contextmenu hover
WEB6 : unused
WEB9 : Formuluar tabframe backround
WEB7 : Table dataset hover
 */


# frameset
$topFrameSize = 30;
$topMenuSize = 55;
$LeftMenuSize = 190;
$rightMenuSize = 230;
$topFrameDivSize = ($topFrameSize-1);
if($intro){return;}

$cssfile = fopen("{$umgvar['pfad']}/USER/{$session['user_id']}/layout.css", "w+");
if($umgvar['waitsymbol']){$waitsymbol = $umgvar['waitsymbol'];}else{$waitsymbol = "pic/wait1.gif";}

$fontc3 = lmbSuggestColor($farbschema['WEB14'],"222222","7D7D7D");
$fontc7 = lmbSuggestColor($farbschema['WEB7'],"5a5a5a","333333");
$fontc14 = lmbSuggestColor($farbschema['WEB14']);

$fontsize07 = "0.7em";
$fontsize09 = "0.9em";
$fontsize11 = "1.1em";
$fontsize12 = "1.2em";
$fontsize13 = "1.3em";
$fontsize14 = "1.4em";
$fontsize15 = "1.5em";

$bgcolor1 = $farbschema['WEB10']; # tablerow

$umgvar['fontsize'];

$buf .= file_get_contents($umgvar['pfad'] . "/layout/comet/icons.css");

$buf .= "

/* to replace frames with iframes */
.frame-container {
    display: flex;
    height: 100%;
}
.frame-container-vertical {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.frame-fill {
    flex-grow: 1;
}

/*global*/
body{
	font-size: {$umgvar['fontsize']}px;
	font-family: {$umgvar['font']};
	background-color: {$farbschema['WEB14']};
	color: {$farbschema['WEB2']};
	margin: 0;
	padding: 0;
	height: 95%;
}

/*global*/
table{
	font-size: {$umgvar['fontsize']}px;
	font-family: {$umgvar['font']};
	color: {$farbschema['WEB2']};
}

/*global*/
th{
	font-weight: normal;
}

/*global*/
iframe {
    border: none;
}

/*navigation menu frame*/
body.nav{
	padding-left: 5px;
	padding-bottom: 5px;
}

/*tools menu frame*/
body.multiframe{
    padding-right: 5px;
	padding-bottom: 5px;
}

.lmbFrameShow {
    background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    border-top: none;
    height: 28px;
    padding-left: 20%;
    padding-top: 12px;
}


/* Frame Top */

.lmbfringeFrameTop{
	background-color:{$farbschema['WEB13']};
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
	right:6px;
	top:8px;
}

.lmbItemLogoTopLeft{
	background-color:transparent;
	position:absolute;
        left:5px;
}

.lmbItemLogoTopRight{
	background-color:transparent;
	right:20px;
	top:0px;
	position:absolute;
}
        
#big_image {
        text-align:center;
}
      
        
/* Frame Top 2 */

.lmbfringeMenuTop2{
	position:absolute;
	vertical-align:middle;
	margin-left:5px;
	margin-right:5px;
	border-collapse:collapse;
        height: 100%;
        border:1px solid {$farbschema['WEB3']};
}


.lmbMenuItemTop2{
	color:{$farbschema['WEB12']};
	vertical-align:middle;
	padding:10px;
	padding-top:5px;
	padding-bottom:5px;
	margin:0px;
	font-size: {$fontsize15};
	border:none;
	text-decoration: none;
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
        
.lmbMenuItemTop2:hover {
        text-decoration:none;
}
        
.lmbMenuItemInactiveTop2:hover {
        text-decoration:none;
        background-color:{$farbschema['WEB11']};
}
        
.lmbMenuItemspaceTop2{
	color:{$farbschema['WEB12']};
	padding:10px;
	padding-top:5px;
	padding-bottom:5px;
	margin:0px;
	font-size: {$fontsize12};
	overflow:hidden;
	background:{$farbschema['WEB10']};
}

.lmbMenuItemActiveTop2{
        background-color:{$farbschema['WEB11']};
        cursor: pointer;
}

.lmbMenuItemInactiveTop2{
	background-color:{$farbschema['WEB10']};
        cursor: pointer;
}  
        
.lmbMenuItemTop2Icon {
    text-align: center;
}
    
.lmbMenuItemTop2Text {
    text-align: center;
    font-size: {$fontsize07};
}

.lmbMenuSpaceBottom{
        display:none;
}
  
.lmbUpdateAvailable .lmbMenuItemTop2Icon {
    color: #f9d421;
}






/* Frame nav */

.lmbfringeFrameNav {
    cursor: w-resize;
    height: 100%;
    width: 100%;
}

.lmbfringeFrameMultiframe{
	height:100%;
	width:100%;
	cursor:w-resize;
}


.lmbfringeMenuNav {
    background-color: {$farbschema['WEB13']};
    border: 1px solid  {$farbschema['WEB4']};
    border-top:none;
    color: {$farbschema['WEB12']};
    cursor: default;
    width: 100%;
    
    /* prevent selection of text */
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.lmbMenuHeaderNav {
    background-color: {$farbschema['WEB14']};
    
    color: {$farbschema['WEB12']};
    cursor: pointer;
    font-size: {$fontsize12};
    height: 40px;
    padding: 11px 0;
    vertical-align: top;
    position:relative;
}
    
.lmbMenuHeaderNavContent {
    border-top: 1px solid {$farbschema['WEB1']};
}
    
.lmbMenuHeaderImage {
    border-right: 1px solid {$farbschema['WEB1']};
    cursor: pointer;
    max-height: 30px;
    max-width: 30px;
    height:30px;
    padding: 5px;
    position: absolute;
    top: 0;
    font-size: 25px;
    width:30px;
    text-align:center;
    line-height: 30px;
}

.lmbMenuItemHeaderNav{
	text-align:left;
	padding-left:47px;
	white-space: nowrap;
	overflow: hidden;
}

.lmbMenuBodyNav {
    overflow: hidden;
    padding-left: 17px;
    padding-top: 8px;
    padding-right: 3px;
    padding-bottom: 8px;
    text-align: left;
    box-sizing: border-box;
    width:100%;
    table-layout:fixed;
}
.lmbMenuBodyNav td {
    padding: 0;
}

.nav #multiframe .lmbMenuBodyNav tr td:first-child {
    width: 20px;
    text-align: center;
}
.nav #multiframe .lmbMenuBodyNav tr td:nth-child(3) {
    width:20px;
    text-align: right;
    cursor:pointer;
}

.lmbMenuSubBodyNav {
    overflow: hidden;
    width:100%;
    table-layout:fixed;
    border-spacing: 0;
    border-collapse: collapse;
    white-space: nowrap;
}
.lmbMenuSubBodyNav td {
    padding: 0;
}

.lmbMenuHide {
    cursor:pointer;
    height: 10px;
    padding: 5px;
    text-align: center;
    border: 1px solid {$farbschema['WEB3']};
    border-top:none;
    border-radius: 0 0 3px 3px;
}
    
.lmbMenuItemBodyNav{
	text-align:left;
	overflow:hidden;
    line-height: 20px;
}
a.lmbMenuItemBodyNav {
    display: block;
}
.lmbMenuItemBodyNav:hover{
	text-decoration: none;
}
.lmbMenuItemBodyNav:not(.lmbMenuItemHeader):not(:nth-child(3)):hover{
	background-color: {$farbschema['WEB5']};
}
    
.lmbMenuItemImage{
    margin-right:4px;
}
    
.lmbMenuItemCaret {
    color:{$farbschema['WEB7']};
    font-size: 26px;
    position: absolute;
    right: 3px;
}
    
.lmbMenuItemActive {
    color:{$farbschema['WEB7']};
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



    
.lmbItemHeaderMain {
    background-color: {$farbschema['WEB13']};
    border-bottom: 1px solid {$farbschema['WEB3']};
    box-sizing: border-box;
    color: {$farbschema['WEB12']};
    cursor: default;
    font-size: {$fontsize14};
    height: 41px;
    padding: 11px 20px;
}
    
.lmbItemHeaderMain td {
    color: {$farbschema['WEB12']};
    font-size: {$fontsize14};
}
    
.helplink {
    position: absolute;
    right: 20px;
}


/* Frame main */

.lmbfringeFrameMain{
	height:100%;
	width:100%;
}
  
.lmbPositionContainerMain {
	background-color: {$farbschema['WEB13']};
        margin: 20px;
        padding:20px;
        overflow: auto;
        position: relative;
        border:1px solid {$farbschema['WEB3']};
        display:block;
}
         
.lmbPositionContainerMainTabPool {
        
}
        
.lmbPositionContainerMain.small {
        width:600px;
}
       
  .lmbPositionContainerMainTabPool.small {
        width:600px;
}    

/* 100% width/height of tabpool minus padding */
.lmbPositionContainerMainTabPool.lmbFullSize {
    height: calc(100% - 20px); /* padding-bottom of 20px */
}
.lmbPositionContainerMainTabPool.lmbFullSize .tabpool {
    height: calc(100% - 20px);
    width: calc(100% - 40px);
}
.lmbPositionContainerMainTabPool.lmbFullSize .tabpool tr:last-child {
    height: 100%;
}  

    
/* Information Frame */
    

.lmbfringeFrameMain .lmbinfo {
    border: 1px solid {$farbschema['WEB3']};
    margin: 20px;
    width:800px;
}
        
.lmbfringeFrameMain .lmbinfo h2 {
    background-color: {$farbschema['WEB10']};
    border-bottom: 1px solid {$farbschema['WEB3']};
    margin: 0 0;
    padding: 10px;
}
    
.lmbfringeFrameMain .lmbinfo .infonav {
    margin: 0 0 10px;
    padding: 10px;
}
    
.lmbfringeFrameMain .lmbinfo table {
    margin:10px;
    width:95%;
}
    
.lmbfringeFrameMain .lmbinfo .footer {
    border-top:1px solid {$farbschema['WEB3']};
    background-color:{$farbschema['WEB14']};
    padding:10px;
    text-align:justify;
}



/* gtab Tabelle außen */

.lmbfringeGtab {
	background-color: {$farbschema['WEB13']};
        margin: 20px;
        overflow: auto;
        position: relative;
        border:1px solid {$farbschema['WEB3']};
        padding:20px;
        display:block;
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

.lmbTableHeader {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    border-bottom: 1px solid {$farbschema['WEB3']};
}
.lmbTableHeader .lmbGtabTabmenuInactive,
.lmbTableHeader .lmbGtabTabmenuActive {
    clear: none;
    float: none;
    width: initial;
    display: inline-block;
    border: 0;
}

.lmbGtabTabmenuTable{
	border-collapse:collapse;
	border-spacing:1px;
}

.lmbGtabTabmenuActive{
    display:none;
	color:{$farbschema['WEB2']};
	font-size:{$fontsize13};
	white-space:nowrap;
	overflow:visible;
	padding: 3px 20px 3px 0;
	border:1px solid {$farbschema['WEB1']};
	border-bottom:none;
	cursor:pointer;
}
        
.lmbGtabTabmenuInactive, .lmbGtabTabmenuTable .lmbGtabTabmenuActive{
	background-color:{$farbschema['WEB13']};
	//color:$fontc3;
	//font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding: 3px 20px 5px 0;
	border: none;
    border-bottom:1px solid {$farbschema['WEB3']};
	cursor:pointer;
    float:left;
    margin: 3px 0;
}
        
.lmbGtabTabmenuTable.multiTab .lmbGtabTabmenuActive{
    color:{$farbschema['WEB2']};
    clear: none;
    float: left;
    margin: 1px 0;
    padding: 3px 20px 3px 0;
    width: auto;
}        
        
table .lmbGtabTab {
    clear: none;
    margin: 1px 0;
    padding: 3px 20px 3px 0;
    width: auto;
    border-spacing: 0;
}
    
td .lmbGtabTabActive {
	color: $fontc7;
	font-size: {$fontsize13};
}
        
.lmbGtabTabInactive, .lmbGtabTabActive {
	color: $fontc3;
	font-size: {$fontsize11};
	white-space: nowrap;
	overflow: visible;
	padding: 3px 10px 3px 10px;
	border: none;
    border-bottom: 1px solid {$farbschema['WEB3']};
	cursor: pointer;
    margin: 3px 0;
    vertical-align: baseline;
}

td .lmbGtabTabSpace {
    width:100%;
}

.lmbGtabTabBody {
	font-weight: normal;
    border: 1px solid {$farbschema['WEB3']};
	width: calc(95% - 2px); /* 2x border */
}

.lmbGtabTabmenuSpace{
    display:none;
	width:100%;
	background-color:{$farbschema['WEB14']};
	border:1px solid {$farbschema['WEB14']};
	border-bottom:1px solid {$farbschema['WEB3']};
	border-left:1px solid {$farbschema['WEB3']};
	font-size:{$fontsize11};
	padding:3px;
	padding-top:4px;
}
        
        
.GtabTableFringeHeader .lmbGtabTabmenuActive {
    border: none;
    /* maybe for 100% header width */
    box-sizing: border-box;
    width: 100%;
    border-bottom:1px solid {$farbschema['WEB1']};
    display: block;
    margin-bottom: 5px;
    clear: both;
}
        

.lmbGtabTabmenuTable .lmbGtabTabmenuActive {
    /* maybe for 100% header width */
    box-sizing: border-box;
    width: 100%;
    margin-top: 0px;
    display: table-cell;
    font-size: 1.3em;
    clear: both;
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
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom:none;
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
	background-color:{$farbschema['WEB9']};
	border-collapse:collapse;
	border: 1px solid {$farbschema['WEB3']};
	border-left:none;
}

.lmbGtabTabulatorLeftActive{
	background-color:{$farbschema['WEB9']};
	color:$fontc7;
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
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:5px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
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
        padding: 2px 0;
}

.gtabHeaderMenuTD{
	cursor:pointer;
	color:color:{$farbschema['WEB2']};
	padding-left:5px;
	padding-right:5px;
	height:18px;
}

.gtabHeaderMenuTD.hoverable:hover{
	text-decoration:underline;
}
                
.gtabHeaderSymbolTR{
	background-color: {$farbschema['WEB13']};
}                

.gtabHeaderSymbolTD{
	margin-top:0px;
	height:23px;
}

.gtabHeaderInputTD{
	background-color: {$farbschema['WEB13']};
	overflow:hidden;
	padding:2px;
	border:1px solid {$farbschema['WEB1']};
}
   
.gtabHeaderInputINP{
        background-image: url(\"../../pic/find.png\");
        padding-left:14px;
        background-repeat: no-repeat;
        background-position: 2px 3px;
        margin: 3px 0;
        border:1px solid {$farbschema['WEB1']};
}

.gtabHeaderInputINPajax{
        background-image: url(\"../../pic/find_green.png\");
}

.gtabHeaderTitleTD{
	background-color: {$farbschema['WEB8']};
	vertical-align: middle;
    overflow: hidden;
	cursor:col-resize;
	padding-left: 4px;
    border-right: 1px solid {$farbschema['WEB1']};
    border-left: 1px solid {$farbschema['WEB1']};
    line-height: 20px;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.gtabHeaderTitleTDOver {
	background-color: {$farbschema['WEB8']};
    background-image: url(\"../../pic/arrow_bullet_left.gif\");
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
	background-color: {$farbschema['WEB8']};
	border: 1px solid grey;
	padding-left: 5;
	padding-right: 5;
	font-weight: bold;
	cursor: pointer;
}




/* gtab Tabelle Liste Body */

.lmbfringeGtabSearch{
	background-color: {$farbschema['WEB8']};
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
	background-color: {$farbschema['WEB8']};
	border-collapse:collapse;
	border-spacing:1px;
	opacity: .9;
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
    border:1px solid {$farbschema['WEB1']};
}


.gtabBodyTDCL{
    vertical-align: top;
    overflow:hidden;
    padding:2px;
    border:1px solid {$farbschema['WEB1']};
}
.gtabBodyTDCL .gtabBodyINP{
    background-color:transparent;
}

.gtabBodyINP{
	background-color: rgba(255, 255, 255, 0.3);
}



/* gtab Tabelle Liste Fuß */

.lmbGtabBottom{
	display:none;
}

.gtabFooterTAB {
    height: 23px;
    vertical-align: middle;
    width: 100%;
}


/* gtab Tabelle Details */

.gtabBodyDetailFringe{
	background-color:{$farbschema['WEB8']};
	border:1px solid {$farbschema['WEB3']};
}

.gtabBodyDetailTopBorder{
	height:1px;
	background-color:{$farbschema['WEB3']};
}

.gtabBodyDetailTopSpace{
	height:5px;
}

.gtabBodyDetailBody{
	
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
    padding: 2px;
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

input, select, textarea {
    color: inherit; /* to prevent the default black of some browsers */
}

.fgtabchange{
        background-color: {$farbschema['WEB8']};
        border: 1px solid {$farbschema['WEB3']};
        padding:0px;
        margin:0px;
        font-size: {$umgvar['fontsize']}px;
        opacity:0.8;
        filter:Alpha(opacity=70);
        box-sizing: border-box;
        overflow: auto; /* fixes IE-Scrollbar-Bug */
}

INPUT.fgtabchange, TEXTAREA.fgtabchange, SELECT.fgtabchange, TABLE.fgtabchange, TD.fgtabchange{
	background-color: {$farbschema['WEB13']};
        border: 1px solid {$farbschema['WEB3']};
	opacity:1;
	filter:Alpha(opacity=100);
	font-size: {$umgvar['fontsize']}px;
	padding:2px;
	color:{$farbschema['WEB2']};
}

DIV.fgtabchange{
    min-height: 18px;
	overflow-y:auto;
	padding:2px;
	box-sizing: border-box;
}

INPUT.gmultilang {
    background-image: url(\"../../pic/fa-language_16.png\");
    background-repeat: no-repeat;
    background-position: right center;
}


/* gtab Tabelle Relation */

.gtabHeaderRelationTAB{
	border: 1px solid {$farbschema['WEB3']};
}

.gtabFringeRelationBody{
	background-color:{$farbschema['WEB8']};
	border: 1px solid {$farbschema['WEB3']};
        width: 94%;
}

.gtabHeaderRelationTAB i {
        padding:3px;
}
   
 .gtabHeaderRelationTAB input {
        margin: 3px;
}
 



/*INPUT[readonly][id^=g_]*/



/* Tabelle Kacheldarstellung */


.tabTileSubform:hover .tabTileResize{
    display:block;
}

.tabTileResize{
    position:absolute;
    display:none;
    right:0px;
    bottom:2px;
    opacity:0.7;
    cursor:pointer;
    z-index:99999;
}



/* Tabelle allgemein */

.tabfringe{
	background-color:{$farbschema['WEB13']};
	padding:0px;
}

table.hoverable tr.tabBody:hover {
    background-color:{$farbschema['WEB5']}
}

.tabHeader{
	background-position:top;
	background-repeat:repeat-x;
	background-color:{$farbschema['WEB13']};
}

.tabHeaderItem {
    font-weight: bold;
    padding: 5px 5px 5px 0;
    vertical-align: top;
}

.tabSubHeader{
	background-color:{$farbschema['WEB7']};
}

.tabSubHeaderItem{
        padding: 2px;
}

.tabItem2{
	background-color:{$farbschema['WEB3']};
}

.tabBody{
	
}

.tabSortableHandle {
    cursor: move;
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
	border-collapse:collapse;
	border-spacing:1px;
	padding:5px;
}

.tabHpoolItemTR{
	border:none;
}

.tabHpoolItemActive{
	color:$fontc14;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:10px;
	padding-left:20px;
	padding-right:20px;
	background-color:{$farbschema['WEB13']};
	border:1px solid {$farbschema['WEB3']};
	border-right:none;
	border-bottom:none;
	cursor:pointer;
}

.tabHpoolItemTR:nth-last-child(2) .tabHpoolItemActive {
    border-bottom: 1px solid {$farbschema['WEB3']};
}

.tabHpoolItemInactive{
	color:{$farbschema['WEB12']};
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:10px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
}

.tabHpoolItemSpaceGtab{
	height:100%;
}


/* Reiter horizontal Tabelle */
       
        
.tabpool {
        margin:20px;
}

.tabpoolfringe{
	border:1px solid {$farbschema['WEB3']};
	background-color:{$farbschema['WEB13']};
	border-top:none;
	border-collapse:collapse;
	border-spacing:1px;
	padding:5px;
    margin:20px;
}

.tabpoolItemTR{
	
}

.tabpoolItemActive{
    background-color:{$farbschema['WEB13']};
	color:$fontc14;
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
	border-bottom-color:{$farbschema['WEB13']};
	cursor:pointer;
}

.tabpoolItemInactive{
	color:{$farbschema['WEB12']};
	text-align:center;
	font-size:{$fontsize11};
	white-space:nowrap;
	overflow:visible;
	padding:3px;
	cursor:pointer;
	padding-left:20px;
	padding-right:20px;
	border:1px solid {$farbschema['WEB3']};
}


.tabpoolItemSpace{
	width:100%;
	border-bottom:1px solid {$farbschema['WEB3']};
	background-color:transparent;
}

.tabpoolItemSpaceGtab{
	width:100%;
	border-bottom:1px solid {$farbschema['WEB3']};
}

.tabpoolItemInactive:hover{
	background-color:{$farbschema['WEB3']};
}

.lmb-tabpoolItemnohover:hover{
	background-color:{$farbschema['WEB13']};
	cursor:default;
}

/* Kontextmenus */

.lmbContextMenu {
    background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    border-left: 3px solid {$farbschema['WEB7']};
    min-width: 150px;
    padding: 5px;
    position: absolute;
    box-shadow: 7px 8px 9px -4px rgba(0,0,0,0.2);
}
.lmbContextMenu.lmbContextMenuMove {
    padding: 0px 5px 5px 5px;
}
    
.lmbContextLink {
    color: {$farbschema['WEB2']};
    cursor: pointer;
    display: block;
    padding: 5px 2px;
    text-decoration: none;
}

.lmbContextLink:hover {
    background-color: {$farbschema['WEB5']};
    color: {$farbschema['WEB2']};
    text-decoration: none;
}

.lmbContextLink:visited{
	color:{$farbschema['WEB2']};
}

.lmbContextRow{
	text-decoration: none;
	display: block;
	padding: 3px 2px;
}
.lmbContextRow.lmbContextRowMove{
	padding-top:5px;
	cursor: move;
}
        
.lmbContextRowSeparatorLine{
	background-color: {$farbschema['WEB3']};
        height: 1px;
        left: 4px;
        margin: 5px 0;
        overflow: hidden;
        width: 100%;
}

.lmbContextLeft {
	vertical-align:middle;
        margin-right: 3px;
}

.lmbContextRight {
	vertical-align:middle;
	margin-left:6px;
	right:4px;
	position:absolute;
}

.lmbContextItem {
	padding: 0 16px; /* earlier 12 */
}

.lmbContextItemIcon {
	padding-right:12px; /* earlier pad-left: 6 */
}

.lmbContextItem.inactive {
    opacity: 0.5;
    font-style: oblique;
}

.lmbContextItemIcon.inactive {
    opacity: 0.5;
    font-style: oblique;
}

.lmbContextTop {
	cursor:pointer;
	padding-bottom:8px;
}

.lmbContextHeader {
	height:18px;
	font-weight: bold;
	background-color: {$farbschema['WEB10']};
    padding-top: 6px;
    border-bottom: 1px solid {$farbschema['WEB3']};
    border-top: 1px solid {$farbschema['WEB3']};
    margin: 0 -5px;
   	text-align:center;
}
.lmbContextHeader:first-child {
    margin-top: -5px;
    border-top: none;
}
.lmbContextHeader[onclick] {
    cursor: pointer;
}
.lmbContextHeader[onclick]:hover {
    background-color: {$farbschema['WEB7']};
}

/* explorer */

.lmbFileTreeItem{
	cursor:pointer;
}
    
.lmbFileTreeItem:hover{
	cursor:pointer;
	background-color: {$farbschema['WEB10']};
	text-decoration:none;
	/* color:{$fontc14};*/
}



/* relation tree ------------------------------------------------------------------------*/
        
.rtree1 {
    background: {$farbschema["WEB7"]};
    color: white;
    font-weight:bold;
	border:1px solid {$farbschema['WEB4']};
	border-radius: 3px 3px 3px 3px;
	padding: 3px;
}

.rtree2 {
    background: {$farbschema["WEB10"]};

	border:1px solid {$farbschema['WEB12']};
	border-radius: 3px 3px 3px 3px;
	padding: 3px;
	
}
       
        
.lmb-green-hover-red {
    color: green;
}
.lmb-black-hover-red {
    color: {$farbschema["WEB2"]};
}
.lmb-green-hover-red:hover, .lmb-black-hover-red:hover {
    color: red;
}
       
.lmb-table-search-hide {
    display: none;
}

label {
    cursor: pointer;
}

.lmbfringeMenuSearch {
    position: sticky;
    top: 0;
    z-index: 50;
    transition: box-shadow 0.3s ease-in-out;
}
.lmbfringeMenuSearch .lmbMenuHeaderNav {
    padding-top: 7px;
    padding-bottom: unset;
}
.lmbTableSearch {
    width: calc(100% - 7px);
    padding:5px;
    transition: box-shadow 0.3s ease-in-out;
}
.lmbTableSearch:not(:placeholder-shown) {
    box-shadow: 0 0 2px 1px {$farbschema["WEB7"]};
    border: 1px solid {$farbschema["WEB7"]};
}



/* Sonstiges */

/*ajax container*/        
DIV.ajax_container {
    z-index:99999;
    background-color: {$farbschema['WEB13']};
    border: 1px solid {$farbschema['WEB3']};
    border-left: 3px solid {$farbschema['WEB7']};
    min-width: 150px;
    padding: 5px;
    position: absolute;
}
 
/*container allgemein*/
.lmb_container {
	padding:5px;
	background-color:{$farbschema['WEB8']};
	border:1px solid {$farbschema['WEB3']};
	border-radius: 3px 3px 3px 3px;
}

.lmbFileVersionDiff {
	border-width:1px;
	border-color:{$farbschema['WEB1']};
	background-color:#FFFF9C; /* TODO: custom color - this is an old yellow*/
	border-style:solid;
	position:absolute;z-index:999999;
	overflow:visible;
	padding:10px;
}

.lmbUploadProgress {
	height: 14px;
	width:100%;
	border:1px solid {$farbschema['WEB3']};
	background-color:{$farbschema['WEB8']};
	font-style:italic;
	float:left;
	position:relative;
	display:none;
}

.lmbUploadProgressBar {
	background-color:{$farbschema['WEB7']};
	width:0%;
	height:100%;
	overflow:hidden;
	white-space:nowrap;
	position:absolute;
	left:0;
	top:0;
	bottom:0;
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
    /* takes too long for quick progress updates -> progress bar half full on 100% */
    /* -webkit-transition:width .6s ease; */
    /* -o-transition:width .6s ease;transition:width .6s ease; */
}

.lmbUploadDragenter {
	background: {$farbschema['WEB14']};
	opacity: .2;
	filter: alpha(opacity=20);
}

/* Drop-Area border */
.lmbUploadDroparea {
    border: 2px dashed transparent;
    margin: -2px;

    /* because of dropover/dropleave events firing too fast */
    transition: border-color 0.2s ease-in-out;
}
body.lmbBodyDropareaActive * {
    pointer-events: none;
}
body.lmbBodyDropareaActive .lmbUploadDroparea {
    pointer-events: stroke;
    border-color: {$farbschema['WEB7']};
    transition-duration: 0.01s;
}
.lmbUploadDroparea.lmbUploadDropareaHover {
    border-color: green;
}
.lmbUploadDroparea.lmbUploadDropareaHover * {
    pointer-events: none;
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


/*openwysiwyg*/
.thumpPreview{
	position:absolute;
	top:50%;
	left:50%;
	cursor:pointer;
	padding:10px;
	background-color:{$farbschema['WEB1']};
	-moz-box-shadow:4px 4px #333333; 
	-webkit-box-shadow:4px 4px #333333; 
	box-shadow:4px 4px #666666;
        z-index:99999;
}
	
	
/*Global*/


A:link {
	text-decoration: none;
	color: {$farbschema['WEB2']};
}

A:visited {
	text-decoration: none;
	color: {$farbschema['WEB2']};
}

A:hover {
	text-decoration: underline;
	color: {$farbschema['WEB12']};
	cursor:pointer
}


.link {
	text-decoration: none;
	color: {$farbschema['WEB2']};
	cursor: pointer;
}

SPAN.HEADER {
	FONT-STYLE: oblique;
	font-size: {$fontsize11};
	color: {$farbschema['WEB2']};
}

DEL {
	background-color: #fcc;
}

INS {
	background-color: #cfc;
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
	color: {$farbschema['WEB2']};
    overflow: hidden;
    background-color: {$farbschema['WEB3']};
}

TD {
	font-weight: normal;
	/* color: {$farbschema['WEB2']}; */
}

SELECT {
	font-weight: normal;
	background-color: {$farbschema['WEB13']};
        border: 1px solid {$farbschema['WEB3']};
        font-size: {$umgvar['fontsize']}px;
}

SELECT.contextmenu {
	font-weight: normal;
	background-color: {$farbschema['WEB8']};
}

INPUT {
	font-weight: normal;
	background-color: {$farbschema['WEB13']};
        border: 1px solid {$farbschema['WEB3']};
        font-size: {$umgvar['fontsize']}px;
}
        
INPUT[type=button] {
        cursor: pointer;
}

INPUT.checkbox {
	font-weight: normal;
	background-color: transparent;
        border: none;
        height:12px;
        width:12px;
}

INPUT.submit {
	font-size: {$fontsize11};
	background-color: {$farbschema['WEB8']};
    border: 1px solid {$farbschema['WEB7']};
    padding:3px;
    padding-left:10px;
    padding-right:10px;
}
    
INPUT.contextmenu {
    vertical-align: middle;
    background-color: {$farbschema['WEB8']};
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

i.lmb-icon[onclick] {
    cursor: pointer;
}
    
/* for the translation form:
    Textarea is 2 line high, the height can be increased by the user */
.language-table TEXTAREA {
    resize: vertical;
    height: 2.5em;
}

FORM {
	font-weight: normal;
	color: {$farbschema['WEB2']};
	margin:0px;
	padding:0px;
}

INS {
    color: #222222;
    padding:2px;
}

DEL {
    color: #222222;
    padding:2px;
}


/* fullcalendar ------------------------------------------------------------------------*/


.fc-widget-header,    /* <th>, usually */
.fc-widget-content {  /* <td>, usually */
	border: 1px solid #ccc;
	/* background: linear-gradient(to bottom, {$farbschema['WEB14']}, {$farbschema['WEB13']} 20px); */
}

.fc-widget-header {
    vertical-align: bottom !important; 
}

.fc-state-highlight { /* <td> today cell */ /* TODO: add .fc-today to <th> */
	background: {$farbschema['WEB5']};
}

.fc-cell-overlay { /* semi-transparent rectangle while dragging */
	background: {$farbschema['WEB7']};
	opacity: .3;
	filter: alpha(opacity=30); /* for IE */
}
	
.fc-resourceName{
	border: 1px solid #ccc;
	background: linear-gradient(to bottom, {$farbschema['WEB13']} 70%, {$farbschema['WEB11']} );
	font-weight:bold;
	font-style:italic;
}
        
 .fc-day-header, .fc-week-number, .fc-widget-header {
        background:  {$farbschema['WEB10']};
}
	 
.fc-event {
	border: 1px solid #3a87ad; /* default BORDER color */
	background-color: #3a87ad; /* default BACKGROUND color */
	color: #fff;               /* default TEXT color  */
	font-size: .85em;
	cursor: default;
    border-radius: 3px;
}

a.fc-event {
	text-decoration: none;
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

.fc-view-resourceWeek tr:nth-child(even), .fc-view-resourceMonth tr:nth-child(even), .fc-view-resourceDay tr:nth-child(even) {
    background: {$farbschema["WEB10"]};
}
 
.fc .fc-week-number {
	width: 25px;
	text-align: center;
}

/* fullcalendar holidays extension
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

/* fullcalendar Resource month, Resource week, Resource day
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








/* Form Editor  /  Report - Editor
------------------------------------------------------------------------*/

.formeditorPanel {
     background-color:{$farbschema["WEB8"]};
     color: {$fontc14};
     border:1px solid {$farbschema["WEB3"]};
     width:240px;

}
     
.formeditorPanel td {
     padding:2px;
     overflow:hidden;
 }
     
.formeditorPanel.no-padding td {
     padding:0;
 }
     
.formeditorPanelHead {
    height:15px;
    padding:3px;
    color:{$fontc7};
    background-color:{$farbschema["WEB7"]};
    text-align:left;
    margin-top:10px;
    cursor:s-resize;
}
    
    
.formeditorPanel .btn {
    border:1px outset;
    cursor: pointer;
    padding:1px;
}
    
#itemlist_area td {
    border-bottom:1px solid {$farbschema["WEB3"]};
    padding:1px;
}
     
    
    
/* ######### user-icon replacement helper classes ####### */

.u-color-3 {
    color: {$farbschema["WEB12"]};
}

.u-color-4 {
    color: {$farbschema["WEB12"]};
}



   
    

/* jquery UI ------------------------------------------------------------------------*/


.ui-widget{
    color: {$farbschema["WEB10"]};
    background-color: {$farbschema["WEB10"]};
}

.ui-selecting {

}

.ui-selectable-helper {
    border-color: {$farbschema["WEB2"]};
}

.ui-selected{
	//background:orange !important;
	-moz-box-shadow: 3px 3px 3px #619A00;
	-webkit-box-shadow: 3px 3px 3px #619A00;
	box-shadow: 3px 3px 3px #619A00;
}

.ui-state-disabled{
	opacity: 0.60;
}

.ui-dialog .ui-dialog-content{
    background: {$farbschema["WEB13"]};
}

.ui-dialog { 
    z-index: 9999 !important ;
}

.ui-widget .ui-widget-header {
    background: {$farbschema["WEB10"]};
    border-color: {$farbschema["WEB7"]};
    color: {$farbschema['WEB3']};
}

.ui-widget-content {
    color:{$farbschema["WEB2"]};
    background-image: none;
}
    
.ui-widget.ui-widget-content .ui-state-default {
    color: {$farbschema["WEB4"]};
}
      
.ui-widget.ui-widget-content .ui-state-hover, .ui-widget.ui-widget-content .ui-state-active {
    border-color: {$farbschema["WEB7"]};
    color: {$farbschema['WEB2']};
}
    
.ui-widget.ui-widget-content .ui-state-highlight,
.ui-widget.ui-widget-content .ui-widget-header .ui-state-hover {
    background: {$farbschema["WEB7"]};
    border-color: {$farbschema["WEB12"]};
    color: {$farbschema["WEB2"]}; 
}
     
.ui-widget .ui-widget-header .ui-icon {
    text-indent: 0;
    background-image: none;
    color: {$farbschema["WEB12"]};
}
    
.ui-widget .ui-widget-header .ui-state-hover .ui-icon {
    background-image: none;
    color: {$farbschema["WEB2"]};
}

.ui-widget .ui-widget-header .ui-icon.ui-icon-circle-triangle-e:before {
    content: \"\\f138\"; 
}
    
.ui-widget .ui-widget-header .ui-icon.ui-icon-circle-triangle-w:before {
    content: \"\\f137\"; 
}
    
.ui-widget .ui-widget-header .ui-icon.ui-icon-closethick {
    left: 0px;
    margin-left: 0;
    width: 100%;
    height: 100%;
}
.ui-widget .ui-widget-header .ui-icon.ui-icon-closethick:before {
    content: \"\\f00d\"; 
}


div.calendar table {
    background: {$farbschema["WEB10"]}; 
}
    
div.calendar tbody td.weekend, div.calendar thead .weekend {
    color: {$farbschema['WEB12']};
}
    
div.calendar tbody td.today {
    background: {$farbschema["WEB10"]};
    color: {$farbschema['WEB7']};
}
    
div.calendar tbody .rowhilite td.wn {
    background: {$farbschema["WEB10"]}
}
     
.ui-datepicker {
    background: linear-gradient({$farbschema["WEB11"]}, {$farbschema["WEB10"]});
    width:inherit;
}
     
.ui-widget-content .ui-datepicker-calendar a.ui-state-default{
    background-color: {$farbschema["WEB8"]};
    color:{$farbschema["WEB12"]};
    background-image: none;
}

.ui-widget-content .ui-datepicker-calendar a.ui-state-active{
    background-color: {$farbschema["WEB12"]};
    color:{$farbschema["WEB8"]};
}

.ui-widget-overlay{
    background-image:none;
    opacity: .6;
    filter: Alpha(Opacity=60);
}



/* canban ------------------------------------------------------------------------*/


.kanban-wrapper {
    display: flex;
    height: 95%;
    max-width: 100%;
    overflow: auto;
    width: 100%;
}

.kanban {
    /*table-layout: fixed;
    border-collapse: separate;
    border-spacing: 10px 0;*/
}

.kanban-header {
    display: flex;
    flex-direction: row;
    overflow: hidden;
    width: 100%;
}

.kanban-body {
    display: flex;
    flex-direction: row;
    height: calc(100% - 35px);
    height: -moz-calc(100% - 35px);
    height: -webkit-calc(100% - 35px);
    overflow: hidden;
    width: 100%;
}


.kanban-column {
    -moz-box-flex: 0;
    -webkit-box-flex: 0;
    flex: 0 0 280px;
    margin: 0 5px 0 0;
    max-width: 280px;
    overflow-y: auto;
    overflow-x: hidden;
    width: 280px;
    background-color: {$farbschema["WEB14"]};
    box-sizing: border-box;
}

.kanban-column-header{
    background-color: {$farbschema["WEB10"]};
}

.kanban-header .kanban-column {
    border-top: 3px solid;
    clear: both;
    color: {$farbschema["WEB2"]};
    font-weight: bold;
    height: 30px;
    padding: 7px 10px;
    text-transform: uppercase;
    overflow: hidden;
}

.kanban-body .kanban-column {
    padding:5px 10px;
}

.kanban-header .kanban-filter {
    border-color: {$farbschema["WEB12"]};
}

.kanban-body .kanban-filter {
    background-color: {$farbschema["WEB8"]};
    border: 1px solid {$farbschema["WEB12"]};
}

.kanban .col-title {
    float:left;
}
.kanban .col-add, .kanban .col-filterico {
    float:right;
    cursor:pointer;
}

.kanban .card {
    background-color: {$farbschema["WEB8"]};
    box-shadow: 1px 1px 3px #545454;
    border-radius: 3px 3px 3px 3px;
    margin-bottom: 10px;
    cursor:pointer;
}

.kanban .card-new:hover {
    background-color: #DDDDDD;
    color: #666666;
}


.kanban .kanban-drop {
    background-color: {$farbschema["WEB11"]};
    border:1px dashed {$farbschema["WEB7"]};
    padding:15px;
    margin-bottom: 10px;
}

.kanban .card .card-tags {
    display: flex;
}

.kanban .card .card-tags .card-tag {
    -moz-box-flex: 1;
    -webkit-box-flex: 1;
    display: block;
    -webkit-flex: 1 1 0;
    -moz-flex: 1 1 0;
    -ms-flex: 1 1 0;
    flex: 1 1 0;
    height: 0.5rem;
}

.kanban .card .card-title {
    padding: 10px;
}

.kanban .card .card-meta {
    display:none;
}

.card-assigned {
    text-align: right;
//    background-color: {$farbschema["WEB11"]};
}

card-assigned-value{
    background-color: {$farbschema["WEB11"]};
}

.card-assigned div {
    display: inline-block;
    margin: 0 5px 5px 0;
    background-color: {$farbschema["WEB11"]};
    padding: 5px;
}

/*
 * Filter
 ***************************************
 */

#kanban-filter ul {
    list-style: outside none none;
    margin: 0;
    padding: 0;
}

#kanban-filter li {
    border-bottom: 1px solid #666666;
    clear: both;
}

#kanban-filter li a {
    display: block;
    padding: 0.5rem 0.5rem 0.5rem 1.5rem;
}

#kanban-filter li a:hover {
    text-decoration:none;
}

#kanban-filter .filter-title {
    text-transform: uppercase;
}

#kanban-filter .filter-item {
    background-color: {$farbschema["WEB11"]};
    border: 1px solid #afafaf;
    cursor:pointer;
    margin: 15px;
    padding: 10px 40px 10px 10px;
    position: relative;
}

#kanban-filter .filter-item .name {
    display: block;
    max-width: 100%;
    overflow: hidden;
    overflow-wrap: normal;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
}

#kanban-filter .filter-item .counter {
    background-color: #afafaf;
    box-sizing: border-box;
    color: #ffffff;
    height: 100%;
    padding: 10px;
    position: absolute;
    right: 0;
    top: 0;
}

#kanban-filter .filter-item .lmb-close-alt {
    position: absolute;
    right: 10px;
    top: 12px;
}

#kanban-filter .filter-item[data-factive='1'] {
    border:2px solid green;
}

#kanban-filter .filter-list {
    display: none;
    max-height: 300px;
    overflow: auto;
}

/*
 * Default-Formular
 ***************************************
 */
.kanban-default-form input[type=text], .kanban-default-form select {
    height:20px;
    width:100%;
}

.kanban-default-form > tbody > tr > td {
    padding:10px;
    vertical-align: top;
}

.kanban-default-form p {
    margin-bottom: 5px;
}

/* Smaller version of .lmbContextLink for use in attribute fieldtype */
.lmbSelectLink {
    color: {$farbschema['WEB2']};
    cursor: pointer;
    padding: 3px 1px 3px 0;
    text-decoration: none;
}
.lmbSelectLink:hover {
    background-color: {$farbschema['WEB5']};
}

/*special icon colors*/
.lmb-query::before {
    color: {$farbschema['WEB7']};
}




/*
 * Attribute Tag-Mode
 ***************************************
 */
.tagattr {

    /* default .fgtabchange */
    min-height: 18px;
    padding: 2px;
    box-sizing: border-box;
    margin: 0;
    font-size: 11px;
    overflow: hidden;

    /* flex container */
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-flex-direction: row-reverse;
    -ms-flex-direction: row-reverse;
    flex-direction: row-reverse;
    -webkit-flex-wrap: nowrap;
    -ms-flex-wrap: nowrap;
    flex-wrap: nowrap;
    -webkit-justify-content: flex-end;
    -ms-flex-pack: end;
    justify-content: flex-end;
    -webkit-align-content: flex-end;
    -ms-flex-line-pack: flex-end;
    align-content: flex-end;
    -webkit-align-items: flex-start;
    -ms-flex-align: start;
    align-items: flex-start;

    border: none;
    background: transparent;
}

.tagattr input {
    margin-left: 0.1rem;
    padding: 0;
}


.tagattr table {
    display:block;
}


.tagattr tbody {
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -webkit-justify-content: flex-start;
    -ms-flex-pack: start;
    justify-content: flex-start;
    -webkit-align-content: flex-start;
    -ms-flex-line-pack: start;
    align-content: flex-start;
    -webkit-align-items: flex-start;
    -ms-flex-align: start;
    align-items: flex-start;
}


.tagattr tr {
    /* tr as flex item */
    -webkit-order: 0;
    -ms-flex-order: 0;
    order: 0;
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    -webkit-align-self: auto;
    -ms-flex-item-align: auto;
    align-self: auto;

    /* tr as flex container */
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-align-items: stretch;
    -ms-flex-align: stretch;
    align-items: stretch;

    /* other styles */
    background: transparent;
    margin-right: 0.4rem;
    margin-bottom: 0.3rem;
    border-radius: 5px;
    padding: 0;
    min-height:1.3rem;
    height:1.3rem;
}
.tagattr tr td:nth-child(2),
.tagattr tr td:nth-child(3) {
    padding-top:0.1rem;
}
.tagattr tr td:nth-child(2) {
    padding-right:0.2rem;
}

/* hide tag with empty input */
.tagattr tr:not(.empty) td[data-hidetag=\"1\"] {
    display: none;
}


.tagattr td {
    width: auto !important;
    padding:0;
    display: flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
}


/* search field */
.tagattr > input:first-child {
    width: 1.3rem !important;
    height: 1.3rem;
    margin: 0;
    border-radius: 3px;
    background-color: {$farbschema['WEB13']};
    text-align: center;
    cursor: pointer;
}

/*.tagattr > input:first-child,*/
.tagattr.transition > input:first-child {
    -webkit-transition: width 0.3s ease-in-out;
    -moz-transition: width 0.3s ease-in-out;
    -ms-transition: width 0.3s ease-in-out;
    -o-transition: width 0.3s ease-in-out;
    transition: width 0.3s ease-in-out;
}

/* input attribute */
.tagattr tr input[type='text'] {
    background: transparent;
    border: none;
    cursor: pointer;
    font-family: Verdana, Arial, Helvetica, sans-serif;
    margin-right: 0.1rem;
    -webkit-appearance: none;
}
.tagattr.transition tr input[type='text'] {
    -webkit-transition: width 0.3s ease;
    -moz-transition: width 0.3s ease;
    -ms-transition: width 0.3s ease;
    -o-transition: width 0.3s ease;
    transition: width 0.3s ease;
}

.tagattr tr input[type='text']:focus {
    background-color: {$farbschema['WEB13']};
    color:{$farbschema['WEB2']};
    border: 1px solid {$farbschema['WEB3']};
    border-radius: 3px;
}
.tagattr.transition tr input[type='text']:focus {
    -webkit-transition: width 0.3s ease;
    -moz-transition: width 0.3s ease;
    -ms-transition: width 0.3s ease;
    -o-transition: width 0.3s ease;
    transition: width 0.3s ease;
}


/* select attribute */
.tagattr select {
    -moz-appearance: none;
    -webkit-appearance: none;
    appearance: none;
    border: none;
    cursor: pointer;
    background: transparent;
    padding:0;
    height:1rem;
    font-family: Verdana, Arial, Helvetica, sans-serif;
}

.tagattr.transition select {
    -webkit-transition: width 0.3s ease-in-out;
    -moz-transition: width 0.3s ease-in-out;
    -ms-transition: width 0.3s ease-in-out;
    -o-transition: width 0.3s ease-in-out;
    transition: width 0.3s ease-in-out;
}

.tagattr tr.empty input[type='text'],
.tagattr tr.empty select {
    border-bottom: 1px solid;
}

/* checkbox attribute */
.tagattr input[type=checkbox] {
    display:none;
}

.tagattr input[type=checkbox] ~ label {
    margin-left:0.2rem;
}
.tagattr input[type=checkbox] ~ label:before {
    font-family: FontAwesome;
    display: inline-block;
    font-size: 1rem;
    letter-spacing: 10px;
    content: '\\f096'
}
.tagattr input[type=checkbox]:checked + label:before {
    content: '\\f00c';
    letter-spacing: 5px;
}


/*
 * Multiselect Tag-Mode
 ***************************************
 */
.tagattr .tagmulti {
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -webkit-justify-content: flex-start;
    -ms-flex-pack: start;
    justify-content: flex-start;
    -webkit-align-content: flex-start;
    -ms-flex-line-pack: start;
    align-content: flex-start;
    -webkit-align-items: flex-start;
    -ms-flex-align: start;
    align-items: flex-start;
}

.tagattr .tagmulti > span {
    margin-right: 0.3rem;
    margin-bottom: 0.3rem;
    width: auto !important;
    display: flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    border-radius: 5px;
    padding: 0.2rem 0.3rem;
    border: 1px solid {$farbschema['WEB3']};
}

.tagattr .tagmulti br {
    display: none;
}

.lmb-progress {
    height: 2em;
    max-width: 250px;
    background-color: {$farbschema['WEB10']};
    position: relative;
    margin-top: 0.3em;
    margin-bottom: 1.2em;
}
.lmb-progress .lmb-progress-valtext {
    position: absolute;
    text-align: center;
    top: 5px;
    left: 0;
    right: 0;
}
.lmb-progress .lmb-progress-val {
    background-color: {$farbschema['WEB7']};
    display: inline-block;
    height: 100%;
}

.first-letter-red {
    display: inline-block; /* for ::first-letter to work */
}
.first-letter-red::first-letter {
    color: red;
}

label input {
    vertical-align: middle;
}

";

# EXTENSIONS
if($GLOBALS["gLmbExt"]["ext_css.inc"]){
    foreach ($GLOBALS["gLmbExt"]["ext_css.inc"] as $key => $extfile){
        require_once($extfile);
    }
}

fwrite($cssfile, $buf);
fclose($cssfile);
