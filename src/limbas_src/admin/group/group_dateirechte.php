<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $dspl_;

?>
<script>

var dspl = new Array();
<?php
$dspl_ = array();
if($dspl){
$dspl_ = explode(";",$dspl);
foreach ($dspl_ as $key => $value) {
	if($value){
		echo "dspl[$value] = $value;";
	}
}}
?>

img3=new Image();img3.src="assets/images/legacy/outliner/plusonly.gif";
img4=new Image();img4.src="assets/images/legacy/outliner/minusonly.gif";


$(function(){

    $('.collapse').on('show.bs.collapse', function () {
        dspl[parseInt($(this).data('id'))] = parseInt($(this).data('id'));
        document.form1.dspl.value = dspl.join(";");
    }).on('hide.bs.collapse', function () {
        dspl.splice(parseInt($(this).data('id')), 1);
        document.form1.dspl.value = dspl.join(";");
    });
    
    $('#openall').click(function(){
        $('.collapse').collapse('show');
    });
    $('#closeall').click(function(){
        $('.collapse').collapse('hide');
    });

});


function uncheckChildFiles(id,typ){
	var child_elements = document.getElementsByName("hh"+typ+id);
	for(var e=0;e<child_elements.length;e++){
		var elid = child_elements[e].id.substr(2,100);
		document.getElementById("f"+typ+elid).checked = 0;
		uncheckChildFiles(elid,typ);
	}
}

function checkChildFiles(id,typ){
	var child_elements = document.getElementsByName("hh"+typ+id);
	for(var e=0;e<child_elements.length;e++){
		var elid = child_elements[e].id.substr(2,100);
		document.getElementById("f"+typ+elid).checked = 1;
		checkChildFiles(elid,typ);
	}
}

function checkParentFiles(id,level,typ,status){
	parent_el = "f"+typ+level;
	parent_hide = "h"+typ+level;
	if(document.getElementById(parent_el)){
		var parent_level = document.getElementById(parent_hide).value;
		document.getElementById(parent_el).checked = 1;
		checkParentFiles(0,parent_level,typ,1);
	}
}

function checkFiles(id,level,typ,status) {
    if (status) {
        if (typ == "v") {
            checkParentFiles(id, level, typ, status);
        }
        if (document.getElementById("inclsub").checked) {
            checkChildFiles(id, typ);
        }
    } else {
        if (typ == "v") {
            uncheckChildFiles(id, typ);
        } else {
            if (document.getElementById("inclsub").checked) {
                uncheckChildFiles(id, typ);
            }
        }
    }
}


// Ajax Gruppenrechte
function limbasShowGroups(el,level){
	url = "main_dyns_admin.php";
	actid = "fileGroupRules&level=" + level;
	ajaxGet(null,url,actid,null,"limbasShowGroupsPost");
}

function limbasShowGroupsPost(result){
    $('#filepermission-modal-body').html(result);
    $('#filepermission-modal').modal('show');
}

</script>


<div class="modal" id="filepermission-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filepermission-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="filepermission-modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">close</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="action" VALUE="setup_group_dateirechte">
        <input type="hidden" name="ID" VALUE="<?=$ID?>">
        <input type="hidden" name="fileid">
        <input type="hidden" name="val">
        <input type="hidden" name="typ">
        <input type="hidden" name="dspl" VALUE="<?=$dspl?>">

        <div class="row">
            <?php
            $activeTabLinkId = 192;
            require(__DIR__.'/group_tabs.php') ?>

            <div class="tab-content col-9 border border-start-0 bg-contrast">
                <div class="tab-pane active p-3">

                    <h5><i class="lmb-icon lmb-group"></i> <?=$groupdat["name"][$ID]?></h5>

                    <hr>

                    
                    <div class="row">
                        <div class="col-4">
                            <i class="lmb-icon lmb-expand-all" id="openall" title="<?=$lang[2288]?>"></i>
                            <i class="lmb-icon lmb-collapse-all" id="closeall" title="<?=$lang[2289]?>"></i>
                            <i class="lmb-icon lmb-expand-some" id="opclall3" title="<?=$lang[2290]?>"></i>
                        </div>
                        <div class="col-4 text-end">
                            <?=$lang[2081]?> <INPUT TYPE="CHECKBOX" ID="inclsub" CLASS="checkb">
                        </div>
                        <div class="col-4">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col w-100"></div>
                        <div class="col-auto me-3">
                            <table class="table table-sm p-0 m-0 table-borderless">
                                <TR>
                                    <TD><i class="lmb-icon lmb-eye px-0 mx-2" TITLE="<?=$lang[2295]?>"></i></TD>
                                    <TD><i class="lmb-icon lmb-create-file px-0 mx-2" TITLE="<?=$lang[2296]?>"></i></TD>
                                    <TD><i class="lmb-icon lmb-create-folder px-0 mx-2" TITLE="<?=$lang[2297]?>"></i></TD>
                                    <TD><i class="lmb-icon lmb-pencil px-0 mx-2" TITLE="<?=$lang[2299]?>"></i></TD>
                                    <TD><i class="lmb-icon lmb-trash px-0 mx-2" TITLE="<?=$lang[2298]?>"></i></TD>
                                    <TD><i class="lmb-icon lmb-eye-slash px-0 mx-2" TITLE="<?=$lang[3162]?>"></i></TD>
                                    <TD><i class="lmb-icon lmb-lock-file px-0 mx-2" TITLE="<?=$lang[2300]?>"></i></TD>
                                </TR>
                            </table>
                        </div>
                    </div>



                    <?php
                    function files1($LEVEL,$sub_view,$sub_add,$sub_addf,$sub_edit,$sub_del,$id='',$colid=''){
                        global $file_struct;
                        global $ffilter;
                        global $filerules;
                        global $farbschema;
                        global $lang;
                        global $dspl_;


                        if($LEVEL){
                            echo '<div class="collapse multi-collapse ps-2'.((in_array($id,$dspl_))?'show':'').'" '.(!empty($colid)?'id="'.$colid.'"':'').' data-id="'.$id.'" data-level="'.$LEVEL.'">';
                        }

                        foreach ($file_struct["id"] as $bzm => $value) {
                            if($file_struct["level"][$bzm] == $LEVEL){
                                $id = $file_struct["id"][$bzm];
                                $colid = 'f_'.$LEVEL.'_'.$id;
                                
                                if(in_array($file_struct["id"][$bzm],$file_struct["level"])){
                                    if(in_array($file_struct["id"][$bzm],$dspl_)){$pis = "plusonly.gif";}else{$pis = "minusonly.gif";}
                                    $next = 1;
                                    $pic = '<img class=\'lmb-image-as-icon\' src="assets/images/legacy/outliner/'.$pis.'" data-bs-toggle="collapse" data-bs-target="#'.$colid.'" class="cursor-pointer">';
                                }else{
                                    $next = 0;
                                    $pic = '<IMG class="lmb-image-as-icon" SRC="assets/images/legacy/outliner/blank.gif">';
                                }
                                    
                                
                                
                                if (true) {

                                    echo '<div class="row w-100" title="'.htmlentities($file_struct["name"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]).'">';
                                    echo '<div class="col w-100">';
                                    echo '<span>';
                                        echo $pic;
                                        echo '<i class="lmb-icon lmb-folder-closed align-middle"></i>';
                                        echo $file_struct['name'][$bzm];
                                        echo '</span>';
                                    echo '</div>';

                                    echo '<div class="col-auto float-end">';
                                    
                                        if (true) {
                                        echo '<table class="w-100 table-borderless mx-5"><tr>';

                                        # --- view ---
                                        echo "<TD><INPUT ID=\"fv".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2295]\" NAME=\"frule[v][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','v',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["view"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"hv".$file_struct["id"][$bzm]."\" NAME=\"hhv".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- add ---
                                        echo "<TD><INPUT ID=\"fa".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2296]\" NAME=\"frule[a][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','a',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["add"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"ha".$file_struct["id"][$bzm]."\" NAME=\"hha".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- addf ---
                                        echo "<TD><INPUT ID=\"fc".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2297]\" NAME=\"frule[c][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','c',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["addf"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"hc".$file_struct["id"][$bzm]."\" NAME=\"hhc".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- edit ---
                                        echo "<TD><INPUT ID=\"fe".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2299]\" NAME=\"frule[e][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','e',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["edit"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"he".$file_struct["id"][$bzm]."\" NAME=\"hhe".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- del ---
                                        echo "<TD><INPUT ID=\"fd".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2298]\" NAME=\"frule[d][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','d',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["del"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"hd".$file_struct["id"][$bzm]."\" NAME=\"hhd".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- hide in tree ---
                                        echo "<TD><INPUT ID=\"fh".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2298]\" NAME=\"frule[h][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','h',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["hide"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"hh".$file_struct["id"][$bzm]."\" NAME=\"hhh".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- lock ---
                                        echo "<TD><INPUT ID=\"fl".$file_struct["id"][$bzm]."\" TITLE=\"$lang[2300]\" NAME=\"frule[l][".$file_struct["id"][$bzm]."]\" TYPE=\"CHECKBOX\" CLASS=\"form-check-input mx-2\" OnClick=\"checkFiles('".$file_struct["id"][$bzm]."','".$file_struct["level"][$bzm]."','l',this.checked)\"";
                                        if($filerules[$file_struct["id"][$bzm]]["lock"]){echo " CHECKED";}
                                        echo "><INPUT TYPE=\"hidden\" ID=\"hl".$file_struct["id"][$bzm]."\" NAME=\"hhl".$file_struct["level"][$bzm]."\" VALUE=\"".$file_struct["level"][$bzm]."\"></TD>";

                                        # --- lock ---
                                        echo "<TD class=''><i class=\"lmb-icon lmb-info-circle\" style=\"cursor:pointer\" OnClick=\"limbasShowGroups(this,'$value')\" title=\"$lang[2301]\"></i></TD>";


                                        echo '</tr></table>';
                                    }
                                    
                                    echo '</div>';
                                    echo '</div>';
                                }
                                
                                if($next){
                                    files1($file_struct["id"][$bzm],$sub_view,$sub_add,$sub_addf,$sub_edit,$sub_del,$id,$colid);
                                }
                            }
                        }

                        if($LEVEL){
                            echo '</div>';
                        }
                    }

                    files1(0,0,0,0,0,0);
                    ?>

                    <div class="pt-3">
                        <?php require __DIR__ . '/submit-footer.php'; ?>
                    </div>

                </div>
            </div>
        </div>


    </FORM>
</div>






