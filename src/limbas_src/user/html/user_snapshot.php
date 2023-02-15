<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if($del AND $gtabid){
	$sqlquery = "DELETE FROM LMB_SNAP_SHARED where ID = $del";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	
	$sqlquery = "DELETE FROM LMB_SNAP WHERE ID = $del AND USER_ID = ".$session["user_id"]." AND TABID = $gtabid";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}else{
		unset($gsnap[$gtabid]["id"][$del]);
		unset($gsnap[$gtabid]['name'][$del]);
		unset($gsnap[$gtabid]['filter'][$del]);
		if(lmb_count($gsnap[$gtabid]["id"]) == 0){unset($gsnap[$gtabid]);}
	}
}

if($snap_edit AND $gtabid AND $snapid){
	if($snap_name = trim($snap_name)){
		$update[] = "NAME = '".parse_db_string(str_replace(";",",",$snap_name),30)."'";
		$gsnap[$gtabid]['name'][$snapid] = lmb_substr(str_replace(";",",",$snap_name),0,30);
	}
	if($snap_global){
		if($snap_global == 1){$v = LMB_DBDEF_TRUE;$vs = 1;}elseif($snap_global == 2){$v = LMB_DBDEF_FALSE;$vs = 0;}
		$update[] = "GLOBAL = $v";
		$gsnap[$gtabid]['glob'][$snapid] = $vs;
	}
	
	if($update){
		$update = implode(",",$update);
		$sqlquery = "UPDATE LMB_SNAP SET $update WHERE ID = $snapid AND USER_ID = {$session['user_id']} AND TABID = $gtabid";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);	
	}
}
?>


<SCRIPT LANGUAGE="JavaScript">

function edit_snap(gtabid,snapid,val,typ) {
	document.form1.gtabid.value = gtabid;
	document.form1.snapid.value = snapid;
	if(typ == 1){
		document.form1.snap_name.value = val;
	}else if(typ == 2){
		if(val){val = 1;}else{val = 2;}
		document.form1.snap_global.value = val;
	}
	
	document.form1.snap_edit.value = 1;
	document.form1.submit();
}

function nav_refresh(gtabid,snapid,val) {
	if(parent.nav){
		parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&refresh=no';
	}
	if(parent.parent.nav){
		parent.parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&refresh=no';
	}
}

function viewSnap(tabid,snapid){
	document.form1.action.value = "gtab_erg";
	document.form1.snapid.value = snapid;
	document.form1.snap_id.value = snapid;
	document.form1.gtabid.value = tabid;
	document.form1.submit();
}

function limbasSnapshotShare(evt,snap_id,destUser,del,edit,drop){
	if(typeof(del) == "undefined"){del = 0;}
	if(typeof(edit) == "undefined"){edit = 0;}
	if(typeof(drop) == "undefined"){drop = 0;}

	ajaxGet(evt,'main_dyns.php','showUserGroups&gtabid='+snap_id+'&usefunction=lmbSnapShareSelect&destUser='+destUser+'&del='+del+'&edit='+edit+'&drop='+drop,'','ajaxContainerPost');
}
function lmbSnapShareSelect(ugval,snapname,gtabid){
	limbasSnapshotShare(null,gtabid,ugval);
}

function deleteSnap(tabid, snapid) {
    if (confirm("<?= $lang[2010] ?>?")) {
        document.location.href='main.php?action=user_snapshot&gtabid=' + tabid + '&del=' + snapid;
    }
}

var activ_menu = null;
function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

</SCRIPT>

<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="user_snapshot">
<input type="hidden" name="gtabid">
<input type="hidden" name="snapid">
<input type="hidden" name="snap_id">
<input type="hidden" name="snap_name">
<input type="hidden" name="snap_global">
<input type="hidden" name="snap_edit">


<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999;" onclick="activ_menu=1;"></div>

<div class="lmbPositionContainerMain small">

<?php
global $tabgroup;
global $gtab;
global $LINK;
global $lang;
global $gsnap;
global $session;

# count shared snapshots by snapshot id
$sqlquery = "SELECT SNAPSHOT_ID FROM LMB_SNAP_SHARED,LMB_SNAP WHERE LMB_SNAP.ID = LMB_SNAP_SHARED.SNAPSHOT_ID AND LMB_SNAP.USER_ID = ".$session["user_id"];
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$sharedCount = array();
while(lmbdb_fetch_row($rs)){
	$snid = lmbdb_result($rs,"SNAPSHOT_ID");
    $sharedCount[$snid]++;
}

# group snapshots by tabgroup
$snapshotsTabgroups = array();
foreach ($gsnap as $tabid => $snapshotData) {
    $tabgroupID = $gtab['tab_group'][$tabid];
    $snapshotsTabgroups[$tabgroupID] = true;
}

echo '<table class="tabfringe">';
# no table groups?
if(!lmb_count($snapshotsTabgroups)) {
    echo '<tr class="tabHeader"><td class="tabHeaderItem">' . $lang[98] . '</td></tr>';
}

# every tabgroup (in order)
foreach ($tabgroup['name'] as $tabgroupID => $tabgroupName) {
    if (!array_key_exists($tabgroupID, $snapshotsTabgroups)) {
        continue;
    }

    # tabgroup header
    echo '<tr class="tabHeader">';
    echo '<td class="tabHeaderItem" colspan="6">' . $tabgroupName . '</td>';
    echo '</tr>';

    # every table (in order)
    foreach ($gtab['desc'] as $tabid => $tabName) {
        # is table of tabgroup and has snapshots
        if ($gtab['tab_group'][$tabid] != $tabgroupID || !array_key_exists($tabid, $gsnap)) {
            continue;
        }

        # table header
        echo '<tr class="tabSubHeader">';
        echo '<td class="tabSubHeaderItem" style="width: 20px;"></td>';
        echo '<td class="tabSubHeaderItem" colspan="5">' . $tabName . '</td>';
        echo '</tr>';

        # every snapshot of that table
        foreach ($gsnap[$tabid]['name'] as $snapID => $snapName) {
            echo '<tr class="tabBody">';
            echo '<td></td>';
            echo '<td style="width: 20px;"></td>';

            # snap name
            echo "<td><input type=\"text\" value=\"$snapName\" onchange=\"edit_snap('$tabid','$snapID',this.value,1);\"></td>";

            # show snap
            echo "<td><i class=\"lmb-icon lmb-list-ul-alt\" onclick=\"viewSnap('$tabid','$snapID');\" style=\"cursor: pointer;\" title=\"{$lang[301]}\"></i></td>";

            # share snap
            if($LINK[225]){
                $style = 'opacity: 0.4;';
                $text = '';
                if ($sharedCount[$snapID]) {
                    $style = '';
                    $text = '&nbsp;(' . $sharedCount[$snapID] . ')';
                }
                echo "<td><i class=\"lmb-icon lmb-group\" onclick=\"limbasSnapshotShare(event, $snapID, '');\" style=\"cursor: pointer; $style\" title=\"{$lang[1966]}\"></i>$text</td>";
            }

            # delete snap
            echo "<td><i class=\"lmb-icon lmb-trash\" onclick=\"deleteSnap($tabid, $snapID);\" style=\"cursor: pointer;\" title=\"{$lang[2010]}\"></i></td>";

            echo '</tr>';
        }
    }

    # spacer
    echo '<tr><td colspan="6"></td></tr>';
}
echo '</table>';
?>

</FORM>
