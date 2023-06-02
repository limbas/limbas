<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if(!$snap_view){$snap_view = 1;}

if($newsnap AND $new_snapname){

    $new_snapname = preg_replace("/[^[:alnum:]_-äöüÄÖÜ]/",'',$new_snapname);
    $new_snapgroup = preg_replace("/[^[:alnum:]äöüÄÖÜ]/",'',$new_snapgroup);

    if(!$gtabid && !$new_snapgroup){
        $new_snapgroup = 'defaultgroup';
    }

    $snap_view = 2; // show all filter

    $NEXTID = next_db_id("LMB_SNAP");
    $sqlquery = "INSERT INTO LMB_SNAP (ID,USER_ID,TABID,NAME,SNAPGROUP) VALUES ($NEXTID,".$session["user_id"] . ",".parse_db_int($gtabid).",'".parse_db_string($new_snapname,30)."','".parse_db_string($new_snapgroup,30)."')";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
}


if($del){
	$sqlquery = "DELETE FROM LMB_SNAP_SHARED WHERE ID = $del";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	
	$sqlquery = "DELETE FROM LMB_SNAP WHERE ID = $del";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}else{
		unset($gsnap[$gtabid]['id'][$del]);
		unset($gsnap[$gtabid]['name'][$del]);
		unset($gsnap[$gtabid]['filter'][$del]);
		if(lmb_count($gsnap[$gtabid]['id']) == 0){unset($gsnap[$gtabid]);}
	}
}

if($snap_edit AND $snapid){
	if($snap_name = trim($snap_name)){
		$update[] = "NAME = '".parse_db_string(str_replace(";",",",$snap_name),50)."'";
		$gsnap[$gtabid]['name'][$snapid] = lmb_substr(str_replace(";",",",$snap_name),0,50);
	}
	if($snap_group){
		$update[] = "SNAPGROUP = '".parse_db_string(str_replace(";",",",$snap_group),50)."'";
		$gsnap[$gtabid]['group'][$snapid] = lmb_substr(str_replace(";",",",$snap_name),0,50);
	}
	
	if($update){
		$update = implode(",",$update);
		$sqlquery = "UPDATE LMB_SNAP SET $update WHERE ID = $snapid";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	}
}

if($snap_extension){
	$prepare_string = "UPDATE LMB_SNAP SET EXT = ? WHERE ID = ".$snap_extension;
	if(!lmb_PrepareSQL($prepare_string,array($snap_extensionValue),__FILE__,__LINE__)){$commit = 1;}
}


?>


<SCRIPT LANGUAGE="JavaScript">

function edit_snap(gtabid,snapid,val,typ) {
	document.form1.gtabid.value = gtabid;
	document.form1.snapid.value = snapid;
	if(typ == 1){
		document.form1.snap_name.value = val;
	}else if(typ == 2){
		document.form1.snap_group.value = val;
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

function limbasSnapshotShare(el,snap_id,destUser,del,edit,drop){
	if(typeof(del) == "undefined"){del = 0;}
	if(typeof(edit) == "undefined"){edit = 0;}
	if(typeof(drop) == "undefined"){drop = 0;}

	ajaxGet('','main_dyns.php','showUserGroups&gtabid='+snap_id+'&usefunction=lmbSnapShareSelect&destUser='+destUser+'&del='+del+'&edit='+edit+'&drop='+drop,'', function(result) {
        $('#lmbAjaxContainer').html(result).show();
		if(el){
            $('#modal_publicShare').modal('show');
            //limbasDivShow(el,null,'lmbAjaxContainer');
        }
	});

}
function lmbSnapShareSelect(ugval,snapname,gtabid){
	limbasSnapshotShare(null,gtabid,ugval);
}

function lmbSnapExtension(el,snapid){
	document.form1.snap_extension.value = snapid;
    $('#modal_snapExtension').modal('show');
	document.form1.snap_extensionValue.value = document.form1.elements['snap_extvalue['+snapid+']'].value;
}
	
var activ_menu = null;
function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

</SCRIPT>




<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_snap">
<input type="hidden" name="snap_view" value="<?=$snap_view?>">
<input type="hidden" name="gtabid">
<input type="hidden" name="snapid">
<input type="hidden" name="snap_id">
<input type="hidden" name="snap_name">
<input type="hidden" name="snap_group">
<input type="hidden" name="snap_edit">
<input type="hidden" name="snap_extension">
<input type="hidden" name="del">


<!-- Modal Extensions -->
<div class="modal" id="modal_snapExtension">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Extension</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        <div class="modal-body">
            <textarea id="snap_extensionValue" name="snap_extensionValue" style="width:400px;height:250px;"></textarea>
        </div>

      <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="document.form1.submit();">Save changes</button>
        <button type="button" class="btn btn-danger" onclick="document.form1.snap_extension.value = '';" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal public share -->
<div class="modal" id="modal_publicShare">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Public share</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        <div class="modal-body" id="lmbAjaxContainer">
        </div>

    </div>
  </div>
</div>














    <div class="container-fluid p-3">


        <ul class="nav nav-tabs">

            <?php if($snap_view == 1) : ?>
                <li class="nav-item">
                    <a class="nav-link active bg-white" href="#"><?=$lang[2498]?> <?=$lang[102]?> <?=$lang[164]?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="main_admin.php?action=setup_snap&snap_view=2&show_public_filter=<?=$show_public_filter?>"><?=$lang[2498]?> <?=$lang[102]?> <?=$lang[2785]?></a>
                </li>

            <?php elseif($snap_view == 2): ?>
                <li class="nav-item">
                    <a class="nav-link" href="main_admin.php?action=setup_snap&snap_view=1&show_public_filter=<?=$show_public_filter?>"><?=$lang[2498]?> <?=$lang[102]?> <?=$lang[164]?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active bg-white" href="#"><?=$lang[2498]?> <?=$lang[102]?> <?=$lang[2785]?></a>
                </li>
            <?php endif; ?>
        </ul>



        <div class="tab-content border border-top-0 bg-white">
            <div class="tab-pane active">



    <?php

     /**
    <select name="snap_view" onchange="document.form1.submit()" class="form-select form-select-sm mb-3">
<option value="1" <?php if($snap_view == 1){echo "SELECTED";}?>><?=$lang[2784]?>
<option value="2" <?php if($snap_view == 2){echo "SELECTED";}?>><?=$lang[2785]?>
<option value="3" <?php if($snap_view == 3){echo "SELECTED";}?>>Gruppiert nach Tabelle
<option value="4" <?php if($snap_view == 4){echo "SELECTED";}?>>Gruppiert nach Filter
</select>
    **/


function show_snapDetail(&$gsnap_,$key1,$key2,$group){
    global $userdat;
    global $LINK;
    global $gtab;

    if($key1){$tablename = $gtab['table'][$key1];}else{$tablename = 'extension only';}


    echo "<TR>
    <TD><INPUT TYPE=\"TEXT\" class=\"form-control form-control-sm\" VALUE=\"".$gsnap_[$key1]["name"][$key2]."\" OnChange=\"edit_snap('$key1','$key2',this.value,1)\" STYLE=\"width:200px;\"></TD>
    <td>{$tablename}</td>
    <td><input class=\"form-control form-control-sm\" type=\"text\"  value=\"$group\" OnChange=\"edit_snap('$key1','$key2',this.value,2)\" STYLE=\"width:200px;\"></td>
    <TD style=\"padding-left:5px;\">";
    if($key1){echo "<a href=\"main.php?action=gtab_erg&gtabid=$key1&snapid=$key2&snap_id=$key2\" target=\"_new\"><i class=\"lmb-icon lmb-list-ul-alt\" border=\"0\"></i></a>";}
    echo "</TD>
    <TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-pencil\"  style=\"cursor:pointer\" border=\"0\" onclick=\"lmbSnapExtension(this,$key2)\"></i></TD>
    <input type=\"hidden\" name=\"snap_extvalue[$key2]\" value=\"".htmlentities($gsnap_[$key1]["ext"][$key2],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">
    ";

    if($LINK[225]){
        echo "<TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-groups\" OnCLick=\"limbasSnapshotShare(this,$key2,'');\" STYLE=\"cursor:pointer;\"></i></TD>";
    }
    echo "<TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-trash\" OnCLick=\"document.form1.del.value=$key2;document.form1.gtabid.value=$key1;document.form1.submit();\" STYLE=\"cursor:pointer;\"></i></TD>
    <TD> &nbsp; (ID $key2) &nbsp; <i>".$userdat["bezeichnung"][$gsnap_[$key1]["user_id"][$key2]]."</i></TD>
    </TR>";
}


if($show_public_filter){$show_public_filter = 1;}else{$show_public_filter=2;}
$gsnap_ = SNAP_loadInSession(null,$show_public_filter);


echo "<ul class=\"list-group\">";



if($snap_view == 1) {

    if($tabgroup["name"] AND $gtab["tab_id"]){
        foreach($tabgroup["name"] as $key => $value){
            $viewgroup = 0;
            foreach($gtab["tab_id"] as $key1 => $value1){
                if($gsnap_[$key1] and $gtab["tab_group"][$key1] == $tabgroup["id"][$key]){
                    if(array_sum($gsnap_[$key1]["user_id"])>0){
                        if(!$viewgroup){echo "<LI class=\"list-group-item border-0\">".$value."<UL class=\"list-group\">";$viewgroup=1;}
                        echo "<LI class=\"list-group-item list-group-item-primary\">{$lang[4]}: ".$gtab["desc"][$key1]."<UL class=\"list-group list-group-item-primary\"><TABLE>";
                        foreach($gsnap_[$key1]["id"] as $key2 => $snid){
                                show_snapDetail($gsnap_,$key1,$key2,$gsnap_[$key1]["group"][$key2]);
                        }
                        echo "</TABLE></UL></LI>";
                    }
                }
            }
            if($viewgroup){echo "</UL></LI>";}
        }
    }



}elseif($snap_view == 2) {

    foreach ($gsnap_[-1] as $key => $value) {
        echo "<LI class=\"list-group-item border-0\">{$lang[2785]}: " . $key;
        echo "<UL class=\"list-group\"><LI class=\"list-group-item list-group-item-primary\"><TABLE WIDTH=\"100%\">";
        foreach ($gsnap_[-1][$key] as $key2 => $snid) {
            $key1 = $gsnap_["argresult_id"][$snid];
            show_snapDetail($gsnap_, $key1, $key2, $key);
        }
        echo "</TABLE></LI></UL></LI>";
    }


}

    ?>
</ul>

<ul class="list-group"><li class="list-group-item border-0">
<table>
    <tr>
        <td><?=$lang[4]?></td>
        <td><?=$lang[164]?></td>
        <td><?=$lang[2785]?></td>
    </tr>

    <tr>
        <td><input class="form-control form-control-sm" type="text" name="new_snapname"></td>
                    <td><SELECT NAME="gtabid" class="form-select form-select-sm">
                        <option></option>
                            <?php
                            foreach ($tabgroup["id"] as $key0 => $value0) {
                                echo '<optgroup label="' . $tabgroup["name"][$key0] . '">';
                                foreach ($gtab["tab_id"] as $key => $value) {
                                    if($gtab["tab_group"][$key] == $value0){
                                        echo "<option value=\"".$value."\">".$gtab["desc"][$key].'</option>';
                                    }
                                }
                                echo '</optgroup>';
                            }
                            ?>
                    </SELECT></td>
        <td><input class="form-control form-control-sm" type="text" name="new_snapgroup"></td>
        <td><button class="btn btn-primary btn-sm" TYPE="SUBMIT" NAME="newsnap" value="1"><?=$lang[2009]?></button></td>
        <td>&nbsp;<?=$lang[2784]?>&nbsp;<input type="checkbox" name="show_public_filter" onchange="document.form1.submit();" <?php if($show_public_filter==1){echo " checked";}?>></td>


    </tr></table>
    </li></ul>

            </div>
        </div>
    </div>
</FORM>
