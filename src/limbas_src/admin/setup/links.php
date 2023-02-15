<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<Script>

function set_subgroup(val){
    $("[id^=subgroup_]").addClass('d-none');
    $('#subgroup_'+val).removeClass('d-none');
}
</Script>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_links">
<input type="hidden" name="new_subgroup">
<input type="hidden" name="order" value="<?=$order?>">


    <div class="container-fluid p-3">
        
        


            <?php

            # Extension Files
            $extfiles = read_dir(EXTENSIONSPATH,1);

            /* --- Ergebnisliste --------------------------------------- */
            foreach($link_groupdesc as $key => $value) :
            ?>

        <table class="table table-sm table-striped border bg-white">
            
            <thead>
                <tr><th COLSPAN="12"><?=$link_groupdesc[$key][0];?></th></tr>
            </thead>
            

            <tr>
                <td><A HREF="Javascript:document.form1.order.value='ID';document.form1.submit();">ID</A></td>
                <td><A HREF="Javascript:document.form1.order.value='SORT';document.form1.submit();"><?=$lang[1815]?></A></td>
                <td><A HREF="Javascript:document.form1.order.value='SUBGROUP';document.form1.submit();"><?=$lang[1814]?></A></td>
                <td><?=$lang[4]?></td>
                <td><?=$lang[126]?></td>
                <td><A HREF="Javascript:document.form1.order.value='ACTION';document.form1.submit();"><?=$lang[544]?></A></td>
                <td><?=$lang[1083]?></td>
                <td><?=$lang[1084]?></td>
                <td>help</td>
                <td><?=$lang[1986]?></td>
                <td><?=$lang[1087]?></td>
                <td><?=$lang[160]?></td>
            </tr>

            <?php

            foreach($value as $key2 => $value2) :
            
                foreach($result_links["sort"] as $bzm => $value0) :
                    
                    
                    
            if($result_links["maingroup"][$bzm] == $key AND $result_links["subgroup"][$bzm] == $key2) :
            
            if($result_links["subgroup"][$bzm] != $tmpsubg){
                echo '<TR class="table-section"><TD COLSPAN="14">'.$link_groupdesc[$key][$result_links["subgroup"][$bzm]].'</TD></TR>';
            }
            $tmpsubg = $result_links["subgroup"][$bzm];

            $bg = "";
            $color = "";
            if($result_links["local"][$bzm] == 1){
                $bg = "bg-danger";
                $color = "";
            }
            elseif($result_links["local"][$bzm] == 2){
                $bg = "bg-success";
                $color = "";
            }
            ?>
            <tr>
                <td class="<?=$bg?> <?=$color?>">
                    
                    <?php if($result_links["local"][$bzm] == 1){?>
                        <A onclick="document.getElementById('quickview_<?=$bzm?>').style.visibility='visible'"><?= $result_links['id'][$bzm] ?></A>&nbsp;
                        <DIV ID="quickview_<?=$bzm?>" style="position:absolute;overflow:visible;visibility:hidden;border:1px solid black;padding:3px;cursor:pointer;background-color:<?=$farbschema["WEB3"]?>" OnClick="this.style.visibility='hidden'">
                            <?php
                            $result1 = $lang[1814].": ".$link_groupdesc[$key][$result_links["subgroup"][$bzm]]."\n".
                                $lang[4].": ".$result_links["link_name"][$bzm]."\n".
                                $lang[126].": ".$result_links["beschreibung"][$bzm]."\n".
                                $lang[544].": ".$result_links["action"][$bzm]."\n".
                                $lang[1083].": ".$result_links["link_url"][$bzm]."\n".
                                $lang[1084].": ".$result_links["icon_url"][$bzm]."\n".
                                $lang[1986].": ".$result_links["ext"][$bzm];

                            $result2 = $lang[1814].": ".$link_groupdesc[$key][$result_links["system_subgroup"][$bzm]]."\n".
                                $lang[4].": ".$result_links["system_link_name"][$bzm]."\n".
                                $lang[126].": ".$result_links["system_beschreibung"][$bzm]."\n".
                                $lang[544].": ".$result_links["system_action"][$bzm]."\n".
                                $lang[1083].": ".$result_links["system_link_url"][$bzm]."\n".
                                $lang[1084].": ".$result_links["system_icon_url"][$bzm]."\n".
                                $lang[1986].": ".$result_links["system_ext"][$bzm];

                            echo tableDiff($result1,$result2,"ID:".$result_links["id"][$bzm],"local copy","system",2,1);
                            ?>
                        </DIV>
                    <?php }else{
                        echo $result_links['id'][$bzm];
                    }?>

                </td>
                
                <td><INPUT TYPE="TEXT" SIZE="3" VALUE="<?=$result_links["sort"][$bzm]?>" NAME="link_sort[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td>
                    
                    <?php if ($key != 5) : ?>
                        <select NAME="link_subgroup[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-select form-select-sm">
                            <option></option>
                            <?php
                                foreach($link_groupdesc[$result_links["maingroup"][$bzm]] as $key1 => $value1){
                                    if($key1){
                                        echo "<option value=\"$key1\" ".(($result_links["subgroup"][$bzm] == $key1)?'selected':'').">$value1</option>";
                                    }
                                }
                            ?>
                        </select>
                    <?php else : ?>
                        <input type="text" value="<?=$result_links["ext"][$bzm]?>">
                    <?php endif; ?>
                </td>

                
                
                <td><INPUT TYPE="TEXT" SIZE="15" VALUE="<?= htmlentities($result_links["link_name"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]) ?>" NAME="link_name[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td><INPUT TYPE="TEXT" SIZE="25" VALUE="<?= htmlentities($result_links["beschreibung"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]) ?>" NAME="link_desc[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td><INPUT TYPE="TEXT" SIZE="15" VALUE="<?= htmlentities($result_links["action"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]) ?>" NAME="link_action[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td><INPUT TYPE="TEXT" SIZE="40" VALUE="<?= htmlentities($result_links["link_url"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]) ?>" NAME="link_url[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td><INPUT TYPE="TEXT" SIZE="30" VALUE="<?= htmlentities($result_links["icon_url"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]) ?>" NAME="link_iconurl[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td><INPUT TYPE="TEXT" SIZE="30" VALUE="<?= htmlentities($result_links["help_url"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]) ?>" NAME="link_helpurl[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-control form-control-sm"></td>
                <td>
                    <?php if($result_links["action"][$bzm] AND $extfiles["name"]) : ?>
                    <SELECT NAME="link_ext[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'" class="form-select form-select-sm">
                        <OPTION></OPTION>
                        <?php
                            foreach ($extfiles["name"] as $key1 => $filename){
                                if($extfiles["typ"][$key1] == "file" AND $extfiles["ext"][$key1] == "ext"){
                                    $path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
                                    
                                    echo "<OPTION VALUE=\"".$path.$filename."\" ".(($result_links['ext'][$bzm] == $path.$filename)?'selected':'').">".str_replace("/EXTENSIONS/","",$path).$filename.'</OPTION>';
                                }
                            }
                        ?>
                    </SELECT>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($result_links["icon_url"][$bzm]) : ?>
                        <i class="lmb-icon <?=$result_links["icon_url"][$bzm]?>"></i>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($supervisor OR $result_links["local"][$bzm]) : ?>
                        <A HREF="main_admin.php?action=setup_links&del=1&id=<?=$result_links["id"][$bzm]?>"><i class="lmb-icon lmb-trash"></i></A>
                    <?php endif; ?>
                </td>
                <input type="hidden" name="change_link_<?=$result_links["id"][$bzm]?>">
                
                
                <?php
                
                
                endif;
                
                
                endforeach;
                
                
                
                
                endforeach;
                
                ?>
        </table>
                <?php
            endforeach;
            ?>
            
        <div class="bg-white p-3 border mb-3">
            <button class="btn btn-sm btn-primary" type="submit" name="change" value="1"><?= $lang[522] ?></button>
        </div>
            
            <table class="table table-sm table-striped border mb-0 bg-white">
                <thead>
                <tr>
                    <td><?=$lang[1814]?></td>
                    <td><?=$lang[4]?></td>
                    <td><?=$lang[126]?></td>
                    <td><?=$lang[544]?></td>
                    <td><?=$lang[1083]?></td>
                    <td><?=$lang[1084]?></td>
                    <td></td>
                </tr>

                </thead>
                



                <TR>
                    <TD>
                        <SELECT NAME="new_maingroup" OnChange="set_subgroup(this.value);document.form1.new_subgroup.value='';" class="form-select form-select-sm">
                            <?php
                            foreach($link_groupdesc as $key => $value){
                                if($umgvar["admin_mode"] AND $key == 5){continue;}
                                echo "<OPTION VALUE=\"".$key."\">".$value[0];
                            }
                            ?>
                        </SELECT>
                        <?php
                        $display = "";
                        foreach($link_groupdesc as $key => $value){
                            echo "<SELECT class=\"$display form-select form-select-sm\" NAME=\"new_subgroup_$key\" ID=\"subgroup_$key\" OnChange=\"document.form1.new_subgroup.value=this.value;\"><OPTION>";
                            foreach($link_groupdesc[$key] as $key1 => $value1){
                                if($key1){
                                    echo "<OPTION VALUE=\"$key1\">$value1</OPTION>";
                                }
                            }
                            echo '</SELECT>';
                            $display = "d-none";
                        }
                        ?>
                    </TD>


                    <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="15" NAME="new_linkname"></td>
                    <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="20" NAME="new_desc"></td>
                    <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="15" NAME="new_linkaction"></td>
                    <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="40" NAME="new_linkurl"></td>
                    <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="25" NAME="new_iconurl"></td>
                    <td><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></td>
                </TR>
            
        </table>
        
    </div>


</FORM>
