<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 124
 */
?>

<script language="JavaScript">

img3=new Image();img3.src="pic/outliner/plusonly.gif";
img4=new Image();img4.src="pic/outliner/minusonly.gif";

function listdata(ID,LEVEL,TABID,TYP,NAME,NO){
	var picname = "p" + ID;

        $('.lmb-folder-open').filter('i[id^=p]').removeClass('lmb-folder-open').addClass('lmb-folder-closed');
        $('#'+picname).removeClass('lmb-folder-closed');
        $('#'+picname).addClass('lmb-folder-open');
        
	document.form2.filename_.value = NAME;
	document.form2.group_id.value = ID;
	if(!NO){
	parent.user_main.location.href="main_admin.php?action=setup_user_erg&group_id=" + ID + "";
	}
}

function showuser(ID){
	parent.user_main.location.href="main_admin.php?action=setup_user_change_admin&ID=" + ID + "";
}

function showgroup(ID){
	parent.user_main.location.href="main_admin.php?action=setup_group_erg&ID=" + ID + "";
}

function popup(ID,LEVEL,TABID,TYP){
	var cli;
	if(browser_ns5){cli = ".nextSibling";}else{cli = "";}
	eval("var nested = document.getElementById('f_"+ID+"_"+LEVEL+"').nextSibling"+cli);
	var picname = "i" + ID;
	if (document.images[picname].src == img4.src) {
		document.images[picname].src = img3.src;
		nested.style.display="none";
	}else {
		document.images[picname].src = img4.src;
		nested.style.display='';
	}
}

/* --- Plaziere DIV-Element auf Cursorposition ----------------------------------- */
function setxypos(evt,el) {
	if(browser_ns5){
		document.getElementById(el).style.left=evt.pageX - 60;
		document.getElementById(el).style.top=evt.pageY;
	}else{
		document.getElementById(el).style.left=window.event.clientX + document.body.scrollLeft - 60;
		document.getElementById(el).style.top=window.event.clientY + document.body.scrollTop;
	}
}


</script>
<div class="lmbPositionContainerMainTabPool" style="width:90%;height:90%">

<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0" width="100%" height="100%"><TR><TD valign="top">

<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
<TD nowrap ID="mo" class="tabpoolItemActive" OnClick="document.getElementById('filelist').style.display='';document.getElementById('searchmenu').style.display='none';limbasSetLayoutClassTabs(this,'tabpoolItemInactive','tabpoolItemActive');"><?=$lang[1469]?></TD>
<TD nowrap ID="ms" class="tabpoolItemInactive" OnClick="document.getElementById('filelist').style.display='none';document.getElementById('searchmenu').style.display='';limbasSetLayoutClassTabs(this,'tabpoolItemInactive','tabpoolItemActive');"><?=$lang[1242]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>

<TR><TD height="100%" valign="top" class="tabpoolfringe">


<TABLE width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<TR><TD>&nbsp;</TD></TR>
<TR><TD>

<DIV ID="filelist">
<?php
function files1($LEVEL){
	global $userstruct;
	if($LEVEL){
		if($LEVEL){$vis = "style=\"display:none\"";}else{$vis = "";}
		echo "<div id=\"foldinglist\" $vis>\n";
		echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD WIDTH=\"10\">&nbsp;</TD><TD>\n";
	}
	$bzm = 0;
	while($userstruct['id'][$bzm]){
		if($userstruct['level'][$bzm] == $LEVEL){
			if(in_array($userstruct['id'][$bzm],$userstruct['level'])){
				$next = 1;
				$pic = "<IMG SRC=\"pic/outliner/plusonly.gif\" NAME=\"i".$userstruct['id'][$bzm]."\" OnClick=\"popup('".$userstruct['id'][$bzm]."','$LEVEL','".$userstruct['tabid'][$bzm]."','".$userstruct['typ'][$bzm]."')\" STYLE=\"cursor:pointer\">";
			}else{
				$next = 0;
				$pic = "<IMG SRC=\"pic/outliner/blank.gif\">";
			}

			if($userstruct['user_id'][$bzm]){
				# --- Hauptgruppe ----
				if($userstruct['maingroup'][$bzm]){
					if($userstruct['del'][$bzm]){$iconclass = "lmb-user1-3";}
					elseif($userstruct['lock'][$bzm]){$iconclass = "lmb-user1-2";}
					else{$iconclass = "lmb-user1-1";}
				# --- Untergruppe ----
				}else{
					if($userstruct['del'][$bzm]){$iconclass = "lmb-user2-3";}
					elseif($userstruct['lock'][$bzm]){$iconclass = "lmb-user2-2";}
					else{$iconclass = "lmb-user2-1";}
				}
				echo "<div ID=\"u_".$userstruct['id'][$bzm]."_$LEVEL\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon " .$iconclass. "\" ID=\"u".$userstruct['id'][$bzm]."\" NAME=\"u".$userstruct['id'][$bzm]."\" ";
				if(!$userstruct['del'][$bzm]){echo "OnClick=\"showuser('".$userstruct['user_id'][$bzm]."')\" STYLE=\"cursor:hand\" ";}
				echo "TITLE=\"".$userstruct['user_name'][$bzm]."\"></i></TD><TD ";
				if(!$userstruct['del'][$bzm]){echo "style=\"cursor:pointer;\" OnClick=\"showuser('".$userstruct['user_id'][$bzm]."')\"";}
				echo "></i>&nbsp;".$userstruct['name'][$bzm]."</TD></TR></TABLE></div>\n";
			}else{
				echo "<div ID=\"f_".$userstruct['id'][$bzm]."_$LEVEL\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>$pic</TD><TD><i class=\"lmb-icon lmb-folder-closed\" ID=\"p".$userstruct['id'][$bzm]."\" NAME=\"p".$userstruct['id'][$bzm]."\" OnClick=\"listdata('".$userstruct['id'][$bzm]."','$LEVEL','".$userstruct['tabid'][$bzm]."','".$userstruct['typ'][$bzm]."','".$userstruct['name'][$bzm]."')\" STYLE=\"cursor:pointer\" title=\"show user\"></i></TD><TD ";
				echo "style=\"cursor:pointer;\" OnClick=\"showgroup(".$userstruct['id'][$bzm].");listdata('".$userstruct['id'][$bzm]."','$LEVEL','".$userstruct['tabid'][$bzm]."','".$userstruct['typ'][$bzm]."','".$userstruct['name'][$bzm]."',1)\" title=\"open details\"";
				echo ">&nbsp;".$userstruct['name'][$bzm]."</TD></TR></TABLE></div>\n";
			}

			if($next){
				$tab = 20;files1($userstruct['id'][$bzm]);
			}else{
				echo "<div id=\"foldinglist\" style=\"display:none\"></div>\n";
			}
		}
		$bzm++;
	}
	if($LEVEL){
		echo "</TD></TR></TABLE>\n";
		echo "</div>\n";
	}
}
files1(0);

?>
</DIV>



<DIV ID="searchmenu" STYLE="display:none;">
<FORM ACTION="main_admin.php" METHOD="post" name="form2" TARGET="user_main">
<input type="hidden" name="action" value="setup_user_erg">
<input type="hidden" name="group_id">

<TABLE BORDER="0" STYLE="width:240px;" class="tabBody">
    <TR><TD COLSPAN="2"><i class="lmb-icon lmb-folder-open" align="top"></i>&nbsp;&nbsp;<INPUT TYPE="TEXT" READONLY NAME="filename_" VALUE="root" STYLE="border:none;width:100px;"></TD></TR>
<TR><TD COLSPAN="2"><?=$lang[519]?>:<BR><INPUT TYPE="TEXT" NAME="ufilter_user" STYLE="width:160px;"></TD></TR>
<TR><TD COLSPAN="2"><?=$lang[142]?>:<BR><INPUT TYPE="TEXT" NAME="ufilter_vorname" STYLE="width:160px;"></TD></TR>
<TR><TD COLSPAN="2"><?=$lang[4]?>:<BR><INPUT TYPE="TEXT" NAME="ufilter_name" STYLE="width:160px;"></TD></TR>
<TR><TD COLSPAN="2"><?=$lang[561]?>:<BR><INPUT TYPE="TEXT" NAME="ufilter_group" STYLE="width:160px;"></TD></TR>
<TR><TD><HR></TD></TR>
<TR><TD COLSPAN="10"><INPUT TYPE="RADIO" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="" <?php if(!$ufilter){echo "CHECKED";}?>>&nbsp;<i class="lmb-icon lmb-user1-1"></i><?=$lang[1790]?></TD></TR>
<TR><TD COLSPAN="10"><INPUT TYPE="RADIO" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="lock" <?php if($ufilter == "lock"){echo "CHECKED";}?>>&nbsp;<i class="lmb-icon-cus lmb-user1-2"></i><?=$lang[1793]?></TD></TR>
<TR><TD COLSPAN="10"><INPUT TYPE="RADIO" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="viewdel" <?php if($ufilter == "viewdel"){echo "CHECKED";}?>>&nbsp;<i class="lmb-icon lmb-user1-3"></i><?=$lang[1687]?></TD></TR>
<TR><TD COLSPAN="10"><INPUT TYPE="RADIO" STYLE="border:none;background-color:transparent" NAME="ufilter" VALUE="activ" <?php if($ufilter == "activ"){echo "CHECKED";}?>>&nbsp;<i class="lmb-icon lmb-user1-4"></i><?=$lang[1789]?></TD></TR>
<TR><TD><HR></TD></TR>
<TR><TD COLSPAN="2" HEIGHT="30"><INPUT TYPE="button" OnClick="document.form2.group_id.value=0;document.form2.submit();" VALUE="<?=$lang[1626]?>"></TD></TR>
</TABLE>
</FORM>
</DIV>



</TD></TR></TABLE>
</TD></TR></TABLE>
</DIV>