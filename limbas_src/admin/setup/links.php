<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 162
 */
?>

<Script>

function set_subgroup(val){
	<?foreach($link_groupdesc as $key => $value){
	echo "document.getElementById(\"subgroup_$key\").style.display='none';\n";
	}?>
	document.getElementById('subgroup_'+val).style.display='';
}
</Script>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_links">
<input type="hidden" name="new_subgroup">
<input type="hidden" name="order" value="<?=$order?>">


<DIV class="lmbPositionContainerMain">


<TABLE class="tabfringe" cellspacing="1" cellpadding="0">

	

	<?php

	# Extension Files
	$extfiles = read_dir($umgvar["pfad"]."/EXTENSIONS",1);

	/* --- Ergebnisliste --------------------------------------- */
	foreach($link_groupdesc as $key => $value){
		echo "<TR><TD COLSPAN=\"14\">&nbsp;</TD></TR>";
		echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"14\">".$link_groupdesc[$key][0]."</TD></TR>";
                
                ?>
                                
                <TR class="tabHeader">
                    <TD class="tabHeaderItem" nowrap WIDTH="10">&nbsp;<A HREF="Javascript:document.form1.order.value='ID';document.form1.submit();">ID</A> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<A HREF="Javascript:document.form1.order.value='SORT';document.form1.submit();"><?=$lang[1815]?></A> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<A HREF="Javascript:document.form1.order.value='SUBGROUP';document.form1.submit();"><?=$lang[1814]?></A> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1080]?> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1081]?> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<A HREF="Javascript:document.form1.order.value='ACTION';document.form1.submit();"><?=$lang[1082]?></A> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1083]?> &nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1084]?> &nbsp;</TD>

                    <TD class="tabHeaderItem" nowrap>&nbsp;help&nbsp;</TD>

                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1986]?></TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1087]?>&nbsp;</TD>
                    <TD class="tabHeaderItem" nowrap>&nbsp;<?=$lang[1088]?>&nbsp;</TD>
                </TR>
                                
                <?php

		foreach($value as $key2 => $value2){
		foreach($result_links["sort"] as $bzm => $value0){
			if($result_links["maingroup"][$bzm] == $key AND $result_links["subgroup"][$bzm] == $key2){
			if($result_links["subgroup"][$bzm] != $tmpsubg){
				echo "<TR class=\"tabSubHeader\"><TD class=\"tabSubHeaderItem\" COLSPAN=\"14\">".$link_groupdesc[$key][$result_links["subgroup"][$bzm]]."</TD></TR>";
			}
			$tmpsubg = $result_links["subgroup"][$bzm];
			if($result_links["local"][$bzm] == 1){$bg = "red";$color = "inherit";}elseif($result_links["local"][$bzm] == 2){$bg = "green";$color = "white";}else{$bg = "inherit";$color = "inherit";}
			?>
				<TR class="tabBody">
				<TD class="vAlignMiddle" style="padding-right:20px;color:<?=$color?>;background-color:<?=$bg?>;">
				<?if($result_links["local"][$bzm] == 1){?>
				<A onclick="document.getElementById('quickview_<?=$bzm?>').style.visibility='visible'"><?echo $result_links[id][$bzm];?></A>&nbsp;
				<DIV ID="quickview_<?=$bzm?>" style="position:absolute;overflow:visible;visibility:hidden;border:1px solid black;padding:3px;cursor:pointer;background-color:<?=$farbschema["WEB6"]?>" OnClick="this.style.visibility='hidden'">
					<?php
					$result1 = $lang[1814].": ".$link_groupdesc[$key][$result_links["subgroup"][$bzm]]."\n".
					$lang[1080].": ".$result_links["link_name"][$bzm]."\n".
					$lang[1081].": ".$result_links["beschreibung"][$bzm]."\n".
					$lang[1082].": ".$result_links["action"][$bzm]."\n".
					$lang[1083].": ".$result_links["link_url"][$bzm]."\n".
					$lang[1084].": ".$result_links["icon_url"][$bzm]."\n".
					$lang[1986].": ".$result_links["ext"][$bzm];

					$result2 = $lang[1814].": ".$link_groupdesc[$key][$result_links["system_subgroup"][$bzm]]."\n".
					$lang[1080].": ".$result_links["system_link_name"][$bzm]."\n".
					$lang[1081].": ".$result_links["system_beschreibung"][$bzm]."\n".
					$lang[1082].": ".$result_links["system_action"][$bzm]."\n".
					$lang[1083].": ".$result_links["system_link_url"][$bzm]."\n".
					$lang[1084].": ".$result_links["system_icon_url"][$bzm]."\n".
					$lang[1986].": ".$result_links["system_ext"][$bzm];

					echo tableDiff($result1,$result2,"ID:".$result_links["id"][$bzm],"local copy","system",2,1);
					?>
				</DIV>
				<?}else{
					echo $result_links[id][$bzm];
				}?>

				</TD>
				<TD VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="3" VALUE="<?=$result_links["sort"][$bzm]?>" NAME="link_sort[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
	            <TD VALIGN="TOP">

	            <?
	            if($key != 5){
	            ?>
	            <SELECT NAME="link_subgroup[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"><OPTION>
				<?
	            }else{
	            	echo "<input type=\"text\" style=\"width:100px;\" value=\"".$result_links["ext"][$bzm]."\">";
	            }

	            foreach($link_groupdesc[$result_links["maingroup"][$bzm]] as $key1 => $value1){
					if($key1){
						echo "<OPTION VALUE=\"$key1\"";
						if($result_links["subgroup"][$bzm] == $key1){echo "SELECTED";}
						echo ">$value1";
					}
				}
				echo "</SELECT>\n";
	            ?>
	            </TD>

	            <TD nowrap VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="15" VALUE="<?echo htmlentities($result_links["link_name"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);?>" NAME="link_name[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
	            <TD nowrap VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="25" VALUE="<?echo htmlentities($result_links["beschreibung"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);?>" NAME="link_desc[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
				<TD nowrap VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="15" VALUE="<?echo htmlentities($result_links["action"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);?>" NAME="link_action[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
				<TD nowrap VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="40" VALUE="<?echo htmlentities($result_links["link_url"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);?>" NAME="link_url[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
				<TD nowrap VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="30" VALUE="<?echo htmlentities($result_links["icon_url"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);?>" NAME="link_iconurl[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
	            <TD nowrap VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="30" VALUE="<?echo htmlentities($result_links["help_url"][$bzm],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]);?>" NAME="link_helpurl[<?=$result_links["id"][$bzm]?>]" onchange="document.form1.change_link_<?=$result_links["id"][$bzm]?>.value='1'"></TD>
				<TD nowrap VALIGN="TOP" ALIGN="CENTER">
	            <?
	            if($result_links["action"][$bzm] AND $extfiles["name"]){
	            	echo "<SELECT NAME=\"link_ext[".$result_links["id"][$bzm]."]\" style=\"width:100px;\" onchange=\"document.form1.change_link_".$result_links["id"][$bzm].".value='1'\"><OPTION>";
					foreach ($extfiles["name"] as $key1 => $filename){
						if($extfiles["typ"][$key1] == "file" AND $extfiles["ext"][$key1] == "ext"){
							$path = lmb_substr($extfiles["path"][$key1],lmb_strlen($umgvar["pfad"]),100);
							if($result_links[ext][$bzm] == $path.$filename){$selected = "SELECTED";}else{$selected = "";}
							echo "<OPTION VALUE=\"".$path.$filename."\" $selected>".str_replace("/EXTENSIONS/","",$path).$filename;
						}
					}
					echo "</SELECT>";
	            }
	            ?>
	            </TD>
			    <TD nowrap ALIGN="CENTER">&nbsp;&nbsp;&nbsp;<?if($result_links["icon_url"][$bzm]){echo "<i class=\"lmb-icon ".$result_links["icon_url"][$bzm]."\"></i>";}?></TD>
                            <TD nowrap ALIGN="CENTER" VALIGN="CENTER"><?if($supervisor OR $result_links["local"][$bzm]){?><A HREF="main_admin.php?<?=SID?>&action=setup_links&del=1&id=<?echo $result_links["id"][$bzm];?>"><i class="lmb-icon lmb-trash" BORDER="0"></i></A><?}?></TD>
			    <input type="hidden" name="change_link_<?=$result_links["id"][$bzm]?>">
			<?
		}
		}
		}
	}
	?>

<TR><TD COLSPAN="14"><HR></TD></TR>
<TR><TD ALIGN="LEFT" COLSPAN="14"><INPUT TYPE="submit" VALUE="<?=$lang[1090]?>" NAME="change"></TD></TR>
<TR><TD COLSPAN="14"><HR></TD></TR>

<TR>
<TD COLSPAN="2"></TD>
<TD VALIGN="TOP"><SELECT NAME="new_maingroup" OnChange="set_subgroup(this.value);document.form1.new_subgroup.value='';" style="width:100px;">
<?
foreach($link_groupdesc as $key => $value){
	if($umgvar["admin_mode"] AND $key == 5){continue;}
	echo "<OPTION VALUE=\"".$key."\">".$value[0];
}
?>
</SELECT>
<BR>

<?
$display = "";
foreach($link_groupdesc as $key => $value){
echo "<SELECT STYLE=\"$display width:100px;\" NAME=\"new_subgroup_$key\" ID=\"subgroup_$key\" OnChange=\"document.form1.new_subgroup.value=this.value;\"><OPTION>";
foreach($link_groupdesc[$key] as $key1 => $value1){
	if($key1){
		echo "<OPTION VALUE=\"$key1\">$value1";
	}
}
echo "</SELECT>\n";
$display = "display:none;";
}
?>
</TD>

<TD VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="15" NAME="new_linkname"></TD>
<TD VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="20" NAME="new_desc"></TD>
<TD VALIGN="TOP">
<INPUT TYPE="TEXT" SIZE="15" NAME="new_linkaction" ID="link_action">
</TD>
<TD VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="40" NAME="new_linkurl"></TD>
<TD VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="25" NAME="new_iconurl"></TD>
<TD></TD>
<TD COLSPAN="2" valign="top"><INPUT TYPE="submit" VALUE="<?=$lang[1089]?>" NAME="add"></TD>
</TR>

<TR><TD colspan="15" class="tabFooter"></TD></TR>
</TABLE><BR><BR></div>


</FORM>
