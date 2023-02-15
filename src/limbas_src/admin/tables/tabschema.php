<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<?php
function schema_tab(&$pat,$gtabid,$posx,$posy){
	global $gtab;
	global $gfield;
	global $farbschema;
	
	if($pat["width"][$gtabid]){$width = "width:".$pat["width"][$gtabid]."px;";}else{$width = "";}
	if($pat["height"][$gtabid]){$height = "height:".$pat["height"][$gtabid]."px;";$height2 = "height:".($pat["height"][$gtabid]-50)."px;";}else{$height = "";$height2 = "";}
	
	echo "<TABLE class=\"lmb_container\" ID=\"tabsh_$gtabid\" STYLE=\"position:absolute;".$width.$height."left:".$posx."px;top:".$posy."px;border:1px solid black;background-color:".$farbschema["WEB7"].";overflow:hidden;z-index:$gtabid;\">";
	echo "<TR><TD TITLE=\"".$gtab["desc"][$gtabid]."\" OnMousedown=\"iniDrag(event,'$gtabid');\" STYLE=\"cursor:move;\"><B>".$gtab["table"][$gtabid]."</B> ($gtabid)</TD></TR>";
	
	echo "<TR><TD><DIV ID=\"selsh_$gtabid\" STYLE=\"border:1px solid black;background-color:".$farbschema["WEB4"].";color:".lmbSuggestColor($farbschema["WEB8"]).";overflow:auto;width:99%;$height2\" onmouseup=\"paint_lines();\">";
	if($gfield[$gtabid]["id"]){
	foreach ($gfield[$gtabid]["id"] as $key => $fieldid) {
		if($gfield[$gtabid]["field_type"][$key] == 11){$style = "color:green;";}
		else{$style = "color:black;";}
		echo "<SPAN CLASS=\"lmb-red-hover\" STYLE=\"$style cursor:help;\" ID=\"opt_".$gtabid."_".$fieldid."\" OnMouseOver=\"set_color(this,'red')\" OnMouseOut=\"set_color(this,'black')\" OnClick=\"lmbAjax_fieldinfo(event,'1','$gtabid','$fieldid');\" TITLE=".$gfield[$gtabid]["spelling"][$fieldid].">".$gfield[$gtabid]["field_name"][$fieldid]." (".$fieldid.")</SPAN><BR>";
	}}
	echo "</DIV></TD></TR>";	

	echo "<TR><TD STYLE=\"cursor:se-resize;\" OnMousedown=\"iniResize(event,'$gtabid');\">&nbsp;</TD></TR>";
	echo "</TABLE>\n";
	

}


function schema_link($gtabid){
	global $gfield;
	global $gtab;
	
	if(!$GLOBALS["linknr"]){$GLOBALS["linknr"] = 0;}
	
	$bzm = $GLOBALS["linknr"];
	foreach ($gfield[$gtabid]["id"] as $key => $fieldid) {
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabletype"][$key] == 1){
			if($gfield[$gtabid]["data_type"][$key] == 27){$dtp = "1:n";}
			elseif($gfield[$gtabid]["data_type"][$key] == 24){$dtp = "n:m";}
			$fel = $gtabid."_".$fieldid;
			$tel = $gfield[$gtabid]["verkntabid"][$key]."_".$gfield[$gtabid]["verknfieldid"][$key];
			echo "
			<DIV STYLE=\"position:absolute;\" ID=\"di_".$fel."\">
			<script type=\"text/javascript\">
			var jg_".$fel." = new jsGraphics(\"di_".$fel."\");
			s_link[$bzm]=\"".$gtabid.",".$gfield[$gtabid]["verkntabid"][$key].",".$fieldid.",".$fel.",".$tel.",".$fel.",".$dtp."\";
			</script>
			</DIV>
			\n\n";
			$bzm++;
		}
	}
	$GLOBALS["linknr"] = $bzm;
}
?>

<div class="container-fluid p-3">

    <ul class="nav nav-tabs">
        <?php if ($LINK[108]): ?>
            <li class="nav-item">
                <a class="nav-link <?=(!$typ || $typ==1)?'active bg-white':''?>" href="main_admin.php?action=setup_tabschema&typ=1"><?=$lang[$LINK["desc"][215]]?></a>
            </li>
        <?php endif; ?>
        <?php if ($LINK[258]): ?>
            <li class="nav-item">
                <a class="nav-link <?=($typ==2)?'active bg-white':''?>" href="main_admin.php?action=setup_tabschema&typ=2"><?=$lang[2912]?></a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="tab-content border border-top-0 bg-white">
        <div class="tab-pane active p-3">

            <?php

            //  ################# relation tree ######################

            if($typ == 2){


                ?>

                <script>

                    function lmb_show_relations(name,filter){

                        $(":not(.view_"+name+")").css('color', '');
                        $(".view_"+name).css('color', 'red');

                        if(filter){
                            $("li.roottree").removeAttr('hidetree');
                            $(".view_"+name).closest('li.roottree').attr('hidetree','1');
                            $("li.roottree:not(li[hidetree='1'])").hide();
                        }

                    }

                    function lmb_search_relations(filter){

                        $("li.roottree").show();
                        $(".list-hierarchy div").css('color', '');
                        $("li.roottree").removeAttr('hidetree');

                        if(filter){
                            $('div.list-hierarchy div:contains('+filter+')').css('color', 'red').closest('li.roottree').attr('hidetree','1');
                            $("li.roottree:not(li[hidetree='1'])").hide();
                        }

                    }

                    var claert;
                    function lmb_search_pause(filter){
                        if(claert){clearTimeout(claert);}
                        claert = setTimeout(function(){lmb_search_relations(filter)},500);
                    }

                </script>


                <div class="p-3">
                    <?=$lang[30]?> : <input type="text" onkeyup="lmb_search_pause(this.value)" class="form-control form-control-sm w-auto d-inline-block">
                </div>


                <?php

                $tables = dbf_20(array($DBA['DBSCHEMA'],null,'TABLE'));
                $tables = $tables["table_name"];


                function lmb_make_tree($value, $callTrace=array()){
                    static $outputCache;
                    # first call -> init output cache
                    if (!$outputCache) { $outputCache = array(); }
                    # return from cache if existent
                    if (array_key_exists($value, $outputCache)) { echo $outputCache[$value]; return; }
                    # prevent recursion
                    if (in_array($value, $callTrace)) { echo "<li>Recursive call to $value!</li>"; return; }

                    if($dep = lmb_checkViewDependency($value)) {
                        ob_start();
                        echo '<ul>';
                        foreach ($dep as $k => $v) {
                            echo '<li>';
                            echo "<div class=\"view_$v\" onclick=\"lmb_show_relations('$v')\" ondblclick=\"lmb_show_relations('$v',1)\">$v</div>";
                            $callTrace[] = $value;
                            lmb_make_tree($v, $callTrace);
                            echo '</li>';
                        }
                        echo "</ul>";
                        $output = ob_get_clean();
                        $outputCache[$value] = $output;
                        echo $output;
                        #return $dep;
                    }
                }
                
                ?>

                <div class="list-hierarchy">
                    <ul>
                        <?php
                        foreach($tables as $key => $value):
                            if($dep = lmb_checkViewDependency($value)): ?>

                               <li class="roottree">
                                    <div><?=$value?></div>
                                    <?=lmb_make_tree($value);?>
                               </li>
                            
                            <?php endif;
                        endforeach;
                        ?>
                    

                    </ul>
                </div>
                
                <?php


                




            }
            else{

            //  ################# table tree ######################

            ?>

            <script>
                $(function() {
                    $('#container').height(($( window ).height()) - 100);
                });
            </script>

            <DIV class="ajax_container" ID="fieldinfo" style="width:300px;position:absolute;z-index:99999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?=$farbschema['WEB11']?>"></DIV>

            <div ID="container" STYLE="position: relative; width: 100%; height: 500px; overflow: auto;">
                <?php

                if($setdrag){
                    lmb_SetTabschemaPattern($setdrag);
                }

                $pat = lmb_GetTabschemaPattern();

                $posx = 0;
                foreach ($gtab["tab_id"] as $key => $gtabid) {
                    if($gtab["typ"][$key] != 5){
                        if($pat["posx"][$gtabid]){$posx = $pat["posx"][$gtabid];}
                        if($pat["posy"][$gtabid]){$posy = $pat["posy"][$gtabid];}else{$posy = 60;}
                        schema_tab($pat,$gtabid,$posx,$posy);
                        $posx += 200;
                    }
                }
                if($gtab["tab_id"]){
                    foreach ($gtab["tab_id"] as $key => $gtabid) {
                        if($gfield[$gtabid]["id"]){
                            schema_link($gtabid);
                        }
                    }}

                ?>
            </div>

                <div class="text-end mt-3">
                    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
                        <input type="hidden" name="action" value="<?=$action?>">
                        <input type="hidden" name="setdrag">

                        <button type="submit" class="btn btn-primary" onclick="setDrag();ajaxGet(null,'main_admin.php', '&action=<?=$action?>&setdrag=' +document.form1.setdrag.value,null, 'void');return false;" value="1" name="view_save"><?=$lang[842]?></button>
                    </FORM>
                </div>


            

        <?php }?>
            
        </div>
    </div>



</div>
