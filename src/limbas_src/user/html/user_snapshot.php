<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


require_once(COREPATH.'extra/snapshot/snapshot.lib');


if($del AND $gtabid){
    SNAP_delete($del,$gtabid);
}

if($snap_edit AND $gtabid AND $snapid){
	if($snap_name = trim($snap_name)){
		$update[] = "NAME = '".parse_db_string(str_replace(";",",",$snap_name),50)."'";
		$gsnap[$gtabid]['name'][$snapid] = lmb_substr(str_replace(";",",",$snap_name),0,50);
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

global $tabgroup;
global $gtab;
global $LINK;
global $lang;
global $gsnap;
global $session;

if(!is_numeric($gtabid)){$gtabid = null;}

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
    if($gtabfilter AND $gtabfilter != $tabid){continue;}
    $tabgroupID = $gtab['tab_group'][$tabid];
    $snapshotsTabgroups[$tabgroupID] = true;
}

?>


<script>

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

</script>

<div class="p-3">
<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="user_snapshot">
<input type="hidden" name="gtabid">
<input type="hidden" name="gtabfilter" value="<?=$gtabfilter?>">
<input type="hidden" name="snapid">
<input type="hidden" name="snap_id">
<input type="hidden" name="snap_name">
<input type="hidden" name="snap_global">
<input type="hidden" name="snap_edit">


        <div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999;" onclick="activ_menu=1;"></div>

        


            <?php if(!lmb_count($snapshotsTabgroups)): ?>
                <div class="card card-body">
                    <?=$lang[98]?>
                </div>
            <?php  endif; ?>


            <?php
            foreach ($tabgroup['name'] as $tabgroupID => $tabgroupName):
                if (!array_key_exists($tabgroupID, $snapshotsTabgroups)) {
                    continue;
                } ?>

            <div class="card card-body mb-3">
                <h5><?=$tabgroupName?></h5>

                <?php
                # every table (in order)
                foreach ($gtab['desc'] as $tabid => $tabName):
                    if($gtabfilter AND $gtabfilter != $tabid){continue;}    
                    
                    # is table of tabgroup and has snapshots
                    if ($gtab['tab_group'][$tabid] != $tabgroupID || !array_key_exists($tabid, $gsnap)) {
                        continue;
                    } ?>


                    <div class="row">
                        <div class="col-sm-3 ms-4 mt-2 mb-1 bg-secondary"><?=$tabName?></div>
                    </div>



                    <?php
                    # every snapshot of that table
                    foreach ($gsnap[$tabid]['name'] as $snapID => $snapName): ?>

                        <!--snap name-->


                        <div class="row p-1">

                            <div class="col-sm-2 ms-5">

                                <?php if($LINK[225] AND ($gsnap[$tabid]["owner"][$snapID] OR $session['group_id'] == 1)): ?>
                                    <input type="text" class='form-control' value="<?=$snapName?>" onchange="edit_snap('$tabid','$snapID',this.value,1);">
                                <?php else: ?>
                                    <input type="text" class='form-control' value="<?=$snapName?>" readonly disabled>
                                <?php endif; ?>
                            </div>




                            <!-- show snap -->
                            <div class="col-sm-1 mt-2">
                                <i class="lmb-icon lmb-list-ul-alt cursor-pointer" onclick="viewSnap(<?=$tabid?>,<?=$snapID?>);" title="<?=$lang[301]?>"></i>

                                <?php
                                # share snap
                                if($LINK[225] AND ($gsnap[$tabid]["owner"][$snapID] OR $session['group_id'] == 1)){
                                    $style = 'opacity: 0.4;';
                                    $text = '';
                                    if ($sharedCount[$snapID]) {
                                        $style = '';
                                        $text = '&nbsp;(' . $sharedCount[$snapID] . ')';
                                    } ?>
                                <i class="lmb-icon lmb-group cursor-pointer" onclick="limbasSnapshotShare(event, <?=$snapID?>, '');" style="<?=$style?>" title="<?=$lang[1966]?>"></i><?=$text?>
                                <?php }

                                # delete snap
                                if(($gsnap[$tabid]["owner"][$snapID] OR $session['group_id'] == 1) OR $gsnap[$tabid]["del"][$snapID]): ?>
                                    <i class="lmb-icon lmb-trash cursor-pointer" onclick="deleteSnap(<?=$tabid?>, <?=$snapID?>);"  title="<?=$lang[2010]?>"></i>
                                <?php endif; ?>

                            </div>

                        </div>

                        <?php

                    endforeach;


                endforeach;

?>
            </div>
                <?php

            endforeach;?>


    </FORM>
</div>

