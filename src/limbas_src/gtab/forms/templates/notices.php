<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $farbschema;
global $lang;
global $gtab;
global $LINK;
global $filter;
global $session;

$hasAnyNotices = false;

$noticeOutput = '';


// notice validity
if ($gtab['validity'][$gtabid] AND $filter['validity'][$gtabid]) {
    $dateformat = setDateFormat(1, 2);
    $validity = $filter['validity'][$gtabid];
    if($filter['validity'][$gtabid] == 'all') {$validity = $GLOBALS['lang'][994];}
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice1\">{$lang[3008]}:&nbsp;
         <input type=\"text\" id=\"lmbValidityNotice\" style=\"height:19px;width:80px;background-color:inherit\" value=\"{$validity}\" 
         onchange=\"document.form1.filter_validity.value=this.value+' ';send_form(1);\"
         ondblclick=\"lmb_datepicker(event,this,'lmbValidityNotice',document.getElementById('lmbValidityNotice').value,'{$dateformat}',10);\">&nbsp;";
    if($filter['validity'][$gtabid] != 'all') {
        $noticeOutput .= "<i class=\"lmb-icon lmb-close-alt\" onclick=\"document.form1.filter_validity.value='all';send_form(1);\"></i>";
    }
    #echo "</div>";

    #echo "<div id=\"notice_validity_line\" class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice1\" style=\"width:100%;\">";
    // validity versioning
    if($gtab['validity'][$gtabid] == 2) {
        $noticeOutput .= show_validity_line($gtabid, $ID, $gresult[$gtabid]['VPID'][0]);
    }
    #echo "</div>";
    $noticeOutput .= "</div>";
}

// notice archive
if ($gresult[$gtabid]['LMB_STATUS'][0] == 1) {
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice2\" style=\"padding-top:3px;\">{$lang[1312]}:
         <i class=\"lmb-icon lmb-close-alt\" onclick=\"userecord('restore');\"></i>
         </div>";
}
// notice trash
elseif ($gresult[$gtabid]['LMB_STATUS'][0] == 2) {
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice2\" style=\"padding-top:3px;\">{$lang[3098]}:
         <i class=\"lmb-icon lmb-close-alt\" onclick=\"userecord('restore');\"></i>
         </div>";
}

// notice versioned
if ($filter['viewversion'][$gtabid]) {
    $msg = '<b>'.$lang[2].' '.array_search($ID,$gresult[$gtabid]['V_ID']).'</b> - ';
    if(trim($gresult[$gtabid]["VDESC"][0])){$msg .= ' (<i>'.$gresult[$gtabid]["VDESC"][0].'</i>) - ';}
    if($gresult[$gtabid]['VACT'][0]){$msg .= $lang[2928];}
    elseif($gtab['editver'][$gtabid]) {$msg .= $lang[3047]."&nbsp;<i class=\"lmb-icon lmb-exclamation-triangle\"></i>";}
    else{$msg .= $lang[2928];}
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice3\">{$msg}</div>";
}

// notice locked
# manuelles lock - lock freigeben
if($gtab['lock'][$gtabid] AND !$gresult[$gtabid]['LOCK']['USER'][$ID] AND $gresult[$gtabid]['LOCK']['STATIC'][$ID] AND $LINK['271']){
    $msg = $lang[$LINK['desc'][271]]." - ".$gresult[$gtabid]['LOCK']['TIME'][$ID];
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice4\">{$msg}:
         <i class=\"lmb-icon lmb-undo\" onclick=\"".$LINK['link_url'][271]."\"></i>
         </div>";
}elseif ($gtab['lockable'][$gtabid] AND $gresult[$gtabid]['LOCK']['USER'][$ID]) {
    $msg = $lang[763]." (<b>User: ".$gresult[$gtabid]['LOCK']['USER'][$ID]."</b> - ".$lang[1722].$gresult[$gtabid]['LOCK']['TIME'][$ID];
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice4\">{$msg}:
         <i class=\"lmb-icon lmb-undo\" onclick=\"document.form1.action.value='gtab_change';send_form(1)\"></i>
         </div>";
}

// notice mandatory
if ($gtab["multitenant"][$gtabid]) {
    global $lmmultitenants;

    $msg = null;
    if(!$gresult[$gtabid]['MID'][0]) {
        $msg = $lang[3012];
    }elseif($gresult[$gtabid]["MID"][0] != $lmmultitenants['mid'][$session['mid']]){
        $msg = $lang[3011] . ' (<b>' . $lmmultitenants['name'][$lmmultitenants['translate'][$gresult[$gtabid]['MID'][0]]] . '</b>)';
    }
    if($msg) {
        $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice5\">{$msg}</div>";
    }
}

// notice data permission
if($gtab['has_userrules'][$gtabid] AND $filter['userrules'][$gtabid]){
    $noticeOutput .= "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice6\">{$lang[3043 ]}:&nbsp;";
    $noticeOutput .= show_userRules($gtabid,$ID);
    $noticeOutput .= "</div>";
}


/*
// notice validity line
if ($gtab['validity'][$gtabid] AND $filter['validity'][$gtabid]) {
    #$arr = show_validity_line($gtabid,$gresult[$gtabid]['VPID'][0]);
    echo "<div id=\"notice_validity_line\" class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice7\" style=\"width:100%;\">";

    show_validity_line($gtabid,$gresult[$gtabid]['VPID'][0]);

    echo "</div>";

    #echo '<Script language="JavaScript">';
    #echo '$(function() {';
    #echo 'lmb_show_validity_line($(\'#notice_validity_line\'),\''.json_encode($arr).'\')';
    #echo '});';
    #echo '</Script>';

}
*/

if (!empty($noticeOutput)) {
    echo '<div class="mb-2">' . $noticeOutput . '</div>';
}




#echo "<div class=\"gtabHeaderBodyTR lmbGlistBodyNotice notice7\" style=\"padding:none\">";
#show_validity_line($gtabid,$gresult[$gtabid]['VPID'][0]);
#echo "</div>";
