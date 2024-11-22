<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<table class="w-100">
    
<?php


if($gtabid):

    #$sqlquery = "SELECT * FROM LMB_TABLETREE WHERE TREEID = ".parse_db_int($treeid)." AND ITEMTAB = ".parse_db_int($gtabid)." AND RELATIONID = '$md5tab'";

    $tfield = $tree['tfield'][$md5tab];
    $ttitle = $tree['ttitle'][$md5tab];
    $tsort = $tree['tsort'][$md5tab];
    $tform = $tree['tform'][$md5tab];
    $trule = $tree['trule'][$md5tab];

    if($gverkn[$gtabid]["id"]){
        foreach ($gverkn[$gtabid]["id"] as $rkey => $rval){$of[] = $rkey;}
        $onlyfield[$gtabid] = $of;
    }
    $fieldid = $gfield[$gtabid]["mainfield"];
    $onlyfield[$gtabid][] = $fieldid;
    if(!$tfield){$tfield = $gfield[$gtabid]["fieldkey_id"][$fieldid];}
    if($tfield){$onlyfield[$gtabid][] = $tfield;}
    if($ttitle){$onlyfield[$gtabid][] = $ttitle;}
    if($tsort){$filter["order"][$gtabid][0] = array($gtabid,$tsort);}
    if($params["verkn_ID"]){$verkn = init_relation($params["verkn_tabid"], $params["verkn_fieldid"], $params["verkn_ID"], null, null, 1);}
    $filter['anzahl'][$gtabid] = 'all';
    if(trim($trule)){eval($trule.";");}
    $gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn,$onlyfield,null,$extension);
    $gsum = lmb_count($gresult[$gtabid]['id']);
    $rand = mt_rand();

    $bzm = 1;
    if(is_array($gresult[$gtabid]['id'])):

        foreach ($gresult[$gtabid]['id'] as $gkey => $gval):
            
            if($gverkn[$gtabid]["id"]){$imgpref = "plus";}else{$imgpref = "join";}
            if($bzm == $gsum){$outliner = "bottom";}
            elseif($bzm == 1){$outliner = "top";}
            else{$outliner = "";}

            if($tfield){
                $fname = "cftyp_".$gfield[$gtabid]["funcid"][$tfield];
                $gvalue = $fname($gkey,$tfield,$gtabid,3,$gresult,0);
                if(!trim($gvalue)){$gvalue = "unknown";}
            }elseif ($fieldid){
                $fname = "cftyp_".$gfield[$gtabid]["funcid"][$fieldid];
                $gvalue = $fname($gkey,$fieldid,$gtabid,3,$gresult,0);
                if(!trim($gvalue)){$gvalue = "unknown";}
            }else{
                $gvalue = "unknown";
            }
            if($ttitle){
                $fname = "cftyp_".$gfield[$gtabid]["funcid"][$ttitle];
                $gtitle = htmlentities($fname($gkey,$ttitle,$gtabid,3,$gresult,0),ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);
            }
?>


    <tr>
        <td style="width:18px"><img src="assets/images/legacy/outliner/<?=$imgpref.$outliner?>.gif" id="lmbTreePlus_<?=$treeid?>_<?=$gtabid?>_<?=$gval?>_<?=$rand?>" align="top" border="0" style="cursor:pointer" onclick="lmb_treeElOpen('<?=$treeid?>','<?=$gtabid?>','<?=$gval?>','<?=$rand?>');event.stopPropagation();"></td>
        <td><a class="lmbFileTreeItem" onclick="event.stopPropagation();lmbTreeOpenData('<?=$gtabid?>','<?=$gval?>','<?=$params["verkn_tabid"]?>','<?=$params["verkn_fieldid"]?>','<?=$params["verkn_ID"]?>','<?=$tform?>')" title="<?=$gtitle?>"><?=$gvalue?></a></td>
    </tr>
    <tr style="display:none" id="lmbTreeEl_<?=$treeid?>_<?=$gtabid?>_<?=$gval?>_<?=$rand?>"><td colspan="2" align="left">
            <table>
            
    
    <?php

            echo "
			";

            $ssum = lmb_count($gverkn[$gtabid]["id"]);
            $sbzm = 1;
            if($gverkn[$gtabid]["id"]):
                foreach ($gverkn[$gtabid]["id"] as $rkey => $rval):
                    if($gfield[$gtabid]["verkntabletype"][$rkey] == 2){continue;}
                    if($tree['display'][$gfield[$gtabid]["md5tab"][$rkey]]){continue;}
                    if($gresult[$gtabid][$rkey][$gkey]){$simgpref = "plusonly";}else{$simgpref = "hline";}
                    if($tree['ticon'][$gfield[$gtabid]["md5tab"][$rkey]]){
                        $icon = $tree['ticon'][$gfield[$gtabid]["md5tab"][$rkey]];
                    }else{
                        $icon = "lmb-folder-closed";
                    }

                    ?>

                    <tr>
                        <td style="width:18px"><img class="lmb-image-as-icon" src="assets/images/legacy/outliner/join.gif"></td>
                        <td style="width:18px"><img class="lmb-image-as-icon" src="assets/images/legacy/outliner/<?=$simgpref?>.gif" style="cursor:pointer" id="lmbTreeSubPlus_<?=$treeid?>_<?=$rval?>_<?=$gval?>_<?=$rand?>" onclick="lmb_treeSubOpen('<?=$treeid?>','<?=$rval?>','<?=$gval?>','<?=$rand?>','<?=$gtabid?>','<?=$rkey?>');event.stopPropagation();"></td>
                        <td style="width:18px"><i class="lmb-icon <?=$icon?>" align="top" border="0" id="lmbTreeSubBox_<?=$treeid?>_<?=$rval?>_<?=$gval?>_<?=$rand?>"></i></td>
                        <td><b><a class="lmbFileTreeItem" onclick="event.stopPropagation();lmbTreeOpenTable('<?=$rval?>','<?=$gtabid?>','<?=$rkey?>','<?=$gval?>');"><?=$gfield[$gtabid]["spelling"][$rkey]?></a></b></td>
                    </tr>
                    <tr style="display:none" id="lmbTreeTR_<?=$treeid?>_<?=$rval?>_<?=$gval?>_<?=$rand?>">
                        <td style="background-image:url(assets/images/legacy/outliner/line.gif);background-repeat:repeat-y;"></td>
                        <td></td>
                        <td colspan="2"><div id="lmbTreeDIV_<?=$treeid?>_<?=$rval?>_<?=$gval?>_<?=$rand?>"></div></td>
                    </tr>
                    
                    <?php

                    $sbzm++;
                endforeach;
                endif;

            echo "";

            $bzm++;
            
            ?>

            </table>
        </td>
    </tr>
                <?php

        endforeach;
        
    endif;

endif;



?>

</table>

