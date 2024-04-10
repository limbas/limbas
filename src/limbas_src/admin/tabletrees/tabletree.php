<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



//TODO: new_layout Context menu not working
?>

<Script language="JavaScript">

// Ajax edit field
function ajaxEditTree(evt,treeid,treetab,itemtab,relationid,changetyp,changeval){
	if(changetyp==null)changetyp = '';
	if(changeval==null)changeval = '';
	mainfunc = function(result){ajaxEditTreePost(result,evt);};
	ajaxGet(null,"main_dyns_admin.php","editTableTree&treeid="+treeid+"&treetab="+treetab+"&itemtab="+itemtab+"&relationid="+relationid+"&changetyp="+changetyp+"&changeval="+changeval,null,"mainfunc");
}

function ajaxEditTreePost(result,evt){
	document.getElementById("lmbAjaxContainer").innerHTML = result;

	if(document.getElementById("lmbAjaxContainer").style.display == 'none'){
		if(evt){limbasDivShow('',evt,"lmbAjaxContainer");}
		document.getElementById("lmbAjaxContainer").style.left = (parseInt(document.getElementById("lmbAjaxContainer").style.left)+30);
	}
	
	
}

function divclose(){
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

var activ_menu = null;

</Script>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>

<?php

if($new_tabletree AND $new_treename AND $new_treetable){
	add_tabletree($new_tabletree,$new_treename,$new_treetable);
}

if(is_numeric($delid)){
	delete_tabletree($delid);
}

$tabletree = get_tabletree();


$sqlquery = "SELECT * FROM LMB_TABLETREE WHERE TREEID = ".parse_db_int($treeid);
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)){
	if($md5tab = lmbdb_result($rs,"RELATIONID")){
		$tree['tform'][$md5tab] = lmbdb_result($rs,"TARGET_FORMID");
		#$tree['tsnap'][$md5tab] = lmbdb_result($rs,"TARGET_SNAP");
		$tree['display'][$md5tab] = lmbdb_result($rs,"DISPLAY");
		$tree['tfield'][$md5tab] = lmbdb_result($rs,"DISPLAY_FIELD");
		$tree['ttitle'][$md5tab] = lmbdb_result($rs,"DISPLAY_TITLE");
		$tree['tsort'][$md5tab] = lmbdb_result($rs,"DISPLAY_SORT");
		$tree['ticon'][$md5tab] = lmbdb_result($rs,"DISPLAY_ICON");
		$tree['trule'][$md5tab] = lmbdb_result($rs,"DISPLAY_RULE");
	}
}


function lmb_subtree($treetab,$gtabid,$treeid,$sub=0){
	global $gverkn;
	global $gfield;
	global $gtab;
	global $tree;
	static $alldone;

	if($gverkn[$gtabid]["id"]){
		
		$count = 0;
		foreach($gverkn[$gtabid]["id"] as $fieldid => $tabid){
			if($gfield[$gtabid]["verkntabletype"][$fieldid] == 2){continue;}
			$count++;
		}
		
		if($alldone[$gtabid] >= 1){return;}
		$alldone[$gtabid]++;

		if($count){
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";

			$bzm = 1;
			foreach($gverkn[$gtabid]["id"] as $fieldid => $tabid){

				if($gfield[$gtabid]["verkntabletype"][$fieldid] == 2){continue;}

				if($count == $bzm){
					$imgpref = "joinbottom";
				}else{
					$imgpref = "join";
				}

				$md5tab = $gfield[$gtabid]["md5tab"][$fieldid];
				$desc = "";
				if($tree['display'][$md5tab]){$color = "red";}else{$color = "";}
				if($tree['tfield'][$md5tab]){$desc .= "<b style=\"color:blue\" title=\"field\"> fi </b>";}
				if($tree['ttitle'][$md5tab]){$desc .= "<b style=\"color:purple\" title=\"title\"> ti </b>";}
				if($tree['tform'][$md5tab]){$desc .= "<b style=\"color:green\" title=\"form\"> fo </b>";}
				if($tree['tsort'][$md5tab]){$desc .= "<b style=\"color:red\" title=\"sort\"> so </b>";}
				if($tree['ticon'][$md5tab]){$desc .= "<b style=\"color:orange\" title=\"icon\"> ic </b>";}
				if($tree['trule'][$md5tab]){$desc .= "<b style=\"color:grey\" title=\"rule\"> ru </b>";}
				if($desc){$desc = "($desc)";}

				echo "
			<tr><td style=\"width:18px\" valign=\"top\"><img src=\"assets/images/legacy/outliner/".$imgpref.".gif\" align=\"top\" border=\"0\"></td>
			<td align=\"left\" nowrap>&nbsp;<a style=\"color:$color;\" onclick=\"activ_menu=1;ajaxEditTree(event,$treeid,$treetab,".$gfield[$gtabid]["verkntabid"][$fieldid].",'".$gfield[$gtabid]["md5tab"][$fieldid]."')\">".$gtab["desc"][$tabid]."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$desc</td></tr>
			";

				if($gverkn[$tabid]["id"]){
					$stb = "";

					if($count != $bzm){
						$stb = "style=\"width:18px;background-image:url(assets/images/legacy/outliner/line.gif);background-repeat:repeat-y;\"";
					}

					echo "
				<tr>
				<td valign=\"top\" $stb></td>
				<td align=\"left\" nowrap>
				";
					lmb_subtree($treetab,$tabid,$treeid,1);
					echo "
				</td>
				</tr>
				";
				}
				$bzm++;
			}
			echo "</table>\n";
		}
	}
}

?>



<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_tabletree">

        <?php if($treeid AND $tabid): ?>
        
        <div class="card">
            <div class="card-body">
                <?php lmb_subtree($tabid,$tabid,$treeid); ?>
            </div>
        </div>
            
        
        <?php else: ?>
        
        <table class="table table-sm table-striped mb-0 border bg-contrast">

            <?php                
                
            foreach ($gtab["tab_id"] as $key => $tabid):
                if($tabletree[$tabid]["treeid"]):
                    
                ?>
                    <tr class="table-section"><th colspan="4"><?=$gtab["desc"][$tabid]?></th></tr>
                    <tr><th></th><th colspan="3"><?=$lang[4]?></th></tr>
                
                <?php foreach ($tabletree[$tabid]["treeid"] as $key1 => $value1):
                    if($tabletree[$tabid]["display_icon"][$key1]){$icon = "<img src=\"".$tabletree[$tabid]["display_icon"][$key1]."\">";}else{$icon = "";}
                    ?>
                
                
                    <tr>
                        <td><a href="main_admin.php?action=setup_tabletree&deltabid=<?=$tabid?>&delid=<?=$tabletree[$tabid]["treeid"][$key1]?>&poolid=<?=$tabletree[$tabid]["poolid"][$key1]?>"><i class="lmb-icon lmb-trash cursor-pointer"></i></a></td>
                        <td colspan="3"><a href="main_admin.php?action=setup_tabletree&treeid=<?=$tabletree[$tabid]["treeid"][$key1]?>&tabid=<?=$tabid?>"><?=$tabletree[$tabid]["poolname"][$key1]?></td>
                    </tr>


            <?php
                    endforeach;
                endif;
            endforeach;
            


            ?>

            <tfoot>

                <tr>
                    <th></th>
                    <th><?=$lang[4]?></th>
                    <th><?=$lang[164]?></th>
                    <th></th>
                </tr>
    
                <tr>
                    <td></td>
                    <td><input type="text" name="new_treename" class="form-control form-control-sm"></td>
                    <td>
                        <select name="new_treetable" class="form-select form-select-sm">
                            <option></option>
                            <?php
                            foreach($gtab["table"] as $tabkey => $tabval){
                                echo "<option value=\"$tabkey\">".$tabval.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><button type="submit" name="new_tabletree" class="btn btn-primary btn-sm" value="1"><?=$lang[2543]?></button></td>
                </tr>
            </tfoot>

        </table>
        
        
            <?php

            
            
            ?>
        
        <?php endif; ?>
    </FORM>

</div>
