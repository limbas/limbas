<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 218
 */
?>

<Script language="JavaScript">

var saverules = new Array();
function save_rules(tab,field,typ){
	eval("saverules['"+tab+"_"+field+"_"+typ+"'] = 1;");
}

function send_form(){

	saval = new Array();
	for (var e in saverules){
		saval.push(e);
	}
	document.form1.edit_template.value = 1;
	document.form1.rules.value = saval.join('|');
	
	var popup = new Array();
	$.each($(".popicon"), function() {

	    if($(this).attr('src') == 'pic/outliner/minusonly.gif'){
	    	popup.push($(this).attr('tabid'));
	    }
	});

	document.form1.popup.value = popup.join(';');
	document.form1.submit();
}

//--- Popup-funktion ----------
var popups = new Array();
function pops(tab){
	eval("var ti = 'table_"+tab+"';");
	eval("var pi = 'popicon_"+tab+"';");
	if(document.getElementById(ti).style.display){
		document.getElementById(ti).style.display='';
		eval("document."+pi+".src='pic/outliner/minusonly.gif';");
	}else{
		document.getElementById(ti).style.display='none';
		eval("document."+pi+".src='pic/outliner/plusonly.gif';");
	}
}

</Script>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_datasync">
<input type="hidden" name="template" value="<?=$template?>">
<input type="hidden" name="tab" value="<?=$tab?>">
<input type="hidden" name="drop_template" value="">
<input type="hidden" name="drop_slave" value="">
<input type="hidden" name="edit_template" value="">
<input type="hidden" name="setting_template" value="">
<input type="hidden" name="rules">
<input type="hidden" name="popup" value="<?=$popup?>">

<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="700" cellspacing="0" cellpadding="0"><TR><TD>

<?php



if($tab == 1){
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemActive">Clients</TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_datasync&tab=2'">Templates</TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	<TR></TR><TD>&nbsp;</TD><TR>
	
	<TR class="tabHeader"><TD></TD><TD class="tabHeaderItem">Name</TD><TD class="tabHeaderItem">HOST</TD><TD class="tabHeaderItem">Username</TD><TD class="tabHeaderItem">Password</TD></TR>
	
    <?php
        if($result_slave){
        foreach($result_slave['name'] as $skey => $sval){
        echo "<TR>
        <TD style=\"width:20px\"><i class=\"lmb-icon lmb-trash\" onclick=\"document.form1.drop_slave.value=$skey;document.form1.submit();\" style=\"cursor:pointer\" border=\"0\"></i></TD>
        <TD><input type=\"text\" value=\"$sval\" name=\"slave[name][$skey]\" style=\"width:150px\" onchange=\"document.getElementById('slave_$skey').value=1\";></TD>
        <TD><input type=\"text\" value=\"".$result_slave['slave_url'][$skey]."\" name=\"slave[url][$skey]\" style=\"width:250\" onchange=\"document.getElementById('slave_$skey').value=1\";></TD>
        <TD><input type=\"text\" value=\"".$result_slave['slave_username'][$skey]."\" name=\"slave[username][$skey]\" style=\"width:80\" onchange=\"document.getElementById('slave_$skey').value=1\";></TD>
        <TD><input type=\"password\" value=\"".$result_slave['slave_pass'][$skey]."\" name=\"slave[pass][$skey]\" style=\"width:80\" onchange=\"document.getElementById('slave_$skey').value=1\";>
        <input type=\"hidden\" value=\"\" name=\"slave[edit][$skey]\" id=\"slave_$skey\">   
        </TD>
        </TR>";
        }}
        echo "
	    
	    
	    <TR class=\"tabBody\"><TD></TD><TD><INPUT TYPE=\"submit\" VALUE=".$lang[522]." NAME=\"edit_slave\"></TD></TR>
	    
        <TR></TR><TD colspan=\"6\"><HR></TD><TR>
        <TR>
	    <TD></TD>
	    <TD><input type=\"TEXT\" name=\"new_slavename\" style=\"width:150\"></TD>
	    <TD><input type=\"TEXT\" name=\"new_slaveurl\" style=\"width:250\"></TD>
	    <TD><input type=\"TEXT\" name=\"new_slaveuser\" style=\"width:80\"></TD>
        <TD><input type=\"password\" name=\"new_slavepass\" style=\"width:80\"></TD>
    
	    <TD><input type=\"submit\" VALUE=\"".$lang[540]."\" name=\"add_slave\"></TD></TR>";
    ?>
	
	<TR class="tabFooter"><TD colspan="7"></TD></TR>
	</TABLE>
	
<?php
}elseif($tab == 2){
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_datasync&tab=1'">Clients</TD>
	<TD nowrap class="tabpoolItemActive" >Templates</TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="850" class="tabBody">
	<TR><TD>&nbsp;</TD><TR>

    <?php
    
    
    if(!$template){
        if($result_template){
        echo "<TR><TD colspan=\"2\"></TD><TD>Name</TD><TD>Conflict Mode</TD></TR>";
        foreach($result_template['name'] as $tkey => $tval){
        $SELCTED = array();
        $SELCTED[$result_template['mode'][$tkey]] = "SELECTED";
        
        echo "<TR>
        <TD style=\"width:20px\"><i class=\"lmb-icon lmb-pencil\" onclick=\"document.form1.template.value=$tkey;document.form1.submit();\" style=\"cursor:pointer\" border=\"0\"></i></a></TD>
        <TD style=\"width:20px\"><i class=\"lmb-icon lmb-trash\" onclick=\"document.form1.drop_template.value=$tkey;document.form1.submit();\" style=\"cursor:pointer\" border=\"0\"></i></TD>
        <TD>&nbsp;&nbsp;$tval</TD>
        <TD>&nbsp;&nbsp<select name=\"template_mode[$tkey]\" onchange=\"document.form1.setting_template.value=$tkey;document.form1.submit();\">
        <option value=\"0\" ".$SELCTED[0].">master
        <option value=\"1\" ".$SELCTED[1].">slave
        <option value=\"2\" ".$SELCTED[2].">date
        <option value=\"3\" ".$SELCTED[3].">manuel
        </select></TD></TR>";
        }}
        echo "
        <TR></TR><TD colspan=\"4\"><HR></TD><TR>
        <TR><TD colspan=\"3\"><input type=\"TEXT\" name=\"new_template\" style=\"width:300\"></TD><TD><input type=\"submit\" VALUE=\"".$lang[540]."\" name=\"add_template\"></TD></TR>";
    }else{
    
        
    foreach($tabgroup['id'] as $bzm => $val) {
    	foreach($gtab["tab_id"] as $key => $tabid){
    	   if($gtab["tab_group"][$key] != $tabgroup["id"][$bzm] OR $gtab["typ"][$key] == 5 OR !$gtab["datasync"][$tabid]){continue;}
    	   $hasgroup[$bzm] = 1;
    	}
    }
    	    

    echo "<TR class=\"tabSubHeader\"><TD colspan=\"5\" class=\"tabSubHeaderItem\"><b>&nbsp;".$result_template['name'][$template]."</b></TD></TR>";

    foreach($hasgroup as $bzm => $val) {
        
        if(in_array($gfield[$tabid]["field_type"][$fkey],$skipftype)){continue;}
        
    	echo "<TR><TD colspan=\"2\"><span style=\"color:blue\">".$tabgroup['name'][$bzm]." (".$tabgroup['beschreibung'][$bzm].")</span></TD></TR>";
    	foreach($gtab["tab_id"] as $key => $tabid){
            
    		if($gtab["tab_group"][$key] != $tabgroup["id"][$bzm] OR $gtab["typ"][$key] == 5 OR !$gtab["datasync"][$tabid]){continue;}
    	    $icon = 'plusonly';
    		if($is_popup){if(in_array($tabid,$is_popup)){$display = "";$icon = 'minusonly';}else{$display = "none";$icon = 'plusonly';}}else{$display = "none";}
    	    
    		?>
			<TR>
			<TD style="width:50px" align="left"><IMG src="pic/outliner/<?=$icon?>.gif" tabid="<?=$key?>" NAME="popicon_<?=$key?>" CLASS="popicon" BORDER="0" STYLE="cursor:pointer" OnClick="pops('<?=$key?>')"></TD>
			<TD style="width:400px" align="left" ><FONT COLOR="green"><?=$gtab['table'][$key]?> (<?=$gtab['desc'][$key]?>)&nbsp;</TD>
			<TD style="width:300px"></TD>
			<TD style="width:50px" class="tabHeaderItem">master</TD>
			<TD style="width:50px" class="tabHeaderItem">client</TD>
			</TR>
			<TR ID="table_<?=$tabid?>" style="display:<?=$display?>"><TD colspan="5">
			<TABLE BORDER="0" width="850" cellspacing="2" cellpadding="0">
			<?php
			
			$skipftype = array(20,17,14,15);
			foreach($gfield[$tabid]["sort"] as $fkey => $fid){
			    if($gfield[$tabid]["field_type"][$fkey] >= 100  OR $gfield[$tabid]["data_type"][$fkey] == 22 OR in_array($gfield[$tabid]["field_type"][$fkey],$skipftype) OR $gfield[$tabid]["field_name"][$fkey] == 'LMB_SYNC_SLAVE'){continue;}
			
				echo "<TR>";
				
				echo "<TD style=\"height:20px;width:50px;\">&nbsp;$fkey&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
				echo "<TD style=\"width:400px;\" TITLE=\"".$gfield[$tabid]["beschreibung"][$fkey]."\" nowrap>".$gfield[$tabid]['field_name'][$fkey]."&nbsp;(".$gfield[$tabid]["spelling"][$fkey].")</TD>";
				echo "<TD style=\"width:300px;\">".$gfield[$tabid]["data_type_exp"][$fkey]."</TD>";
				
			    if($result_conf[$template][$tabid][$fkey]['master']){$CHECKED = "CHECKED";}else{$CHECKED = '';}
				echo "<TD style=\"width:50px;\"><INPUT TYPE=\"checkbox\" NAME=\"templ_conf[$tabid][$fkey][1]\" onclick=\"save_rules('$tabid','$fkey',1)\" VALUE=\"1\" $CHECKED>";
				
			    if($result_conf[$template][$tabid][$fkey]['slave']){$CHECKED = "CHECKED";}else{$CHECKED = '';}
				echo "<TD style=\"width:50px;\"><INPUT TYPE=\"checkbox\" NAME=\"templ_conf[$tabid][$fkey][2]\" onclick=\"save_rules('$tabid','$fkey',2)\" VALUE=\"1\" $CHECKED>";
				
				echo "</TR>";
			}
			echo "</TABLE></TD></TR>";
    	}
    	echo "</TD></TR>";
    }
    echo "<TR><TD colspan=\"5\"><hr></TD></TR>";
    echo "<TR><TD colspan=\"5\"><input type=\"button\" VALUE=\"".$lang[33]."\" onclick=\"send_form()\"></TD></TR>";
    }
    			
    			
    
    ?>

	</TABLE>
	
	<?php
}
?>

</td></tr></table>

</div>
</FORM>