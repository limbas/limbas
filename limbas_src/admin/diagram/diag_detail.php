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
 * ID:
 */

/* Get Field Settings */
$sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS, FUNCTION, COLOR FROM LMB_CHARTS WHERE CHART_ID=$diag_id";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$bzm = 1;
$diagdetaillist = array();
while(odbc_fetch_row($rs, $bzm)){
	$fi = odbc_result($rs, "FIELD_ID");
	$diagdetaillist[$fi]["axis"] = odbc_result($rs, "AXIS");
	$diagdetaillist[$fi]["function"] = odbc_result($rs, "FUNCTION");
	$diagdetaillist[$fi]["color"] = odbc_result($rs, "COLOR");	
	$bzm++;
}

/* Get Customization Settings */
$settingnames = array('DIAG_TYPE','DIAG_WIDTH','DIAG_HEIGHT','TEXT_X','TEXT_Y','FONT_SIZE','PADDING_LEFT',
				'PADDING_TOP','PADDING_RIGHT','PADDING_BOTTOM','LEGEND_X','LEGEND_Y','LEGEND_MODE',
				'PIE_WRITE_VALUES','PIE_RADIUS');
$sqlquery = "SELECT " . implode(",", $settingnames) . " FROM LMB_CHART_LIST WHERE ID=$diag_id";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
odbc_fetch_row($rs, 1);	
$settings = array();
foreach($settingnames as $name){
	$settings[$name] = odbc_result($rs, $name);
}

?>


<!---------------- Diagramm-Detail --------------------->
<script type="text/javascript">
/* Create Diagram and load into #diagram-div */
$( document ).ready(function() {
	$('#save_diag_settings').on('click', function(){
		/* First save settings, then render diagram */
		$.ajax({
			method: "POST",
			type: "POST",
			url: "main_dyns_admin.php",
			data: { 
				'actid' : "saveDiagSettings",
				'diag_id' : $('#diag_id').val(),
				'diag_width' : $('#diag_width').val(),
				'diag_height' : $('#diag_height').val(),
				'text_x' : $('#text_x').val(),
				'text_y' : $('#text_y').val(),
				'font_size' : $('#font_size').val(),
				'padding_left' : $('#padding_left').val(),
				'padding_top' : $('#padding_top').val(),
				'padding_right' : $('#padding_right').val(),
				'padding_bottom' : $('#padding_bottom').val(),
				'legend_x' : $('#legend_x').val(),
				'legend_y' : $('#legend_y').val(),
				'legend_mode' : $('#legend_mode').val(),
				'pie_write_values' : $('#pie_write_values').val(),
				'pie_radius' : $('#pie_radius').val()
			}
		});		
	});
	
	$('#create_diag').on('click', function(){
		$.ajax({
			method: "POST",
			type: "POST",
			url: "main_dyns_admin.php",
			data: { 
				'actid' : "createDiagPChart",
				'diag_id' : $('#diag_id').val()					
			}
		})
		.done(function( msg ) {
			if(ajaxEvalScript(msg) === false && msg){
    			    diagramm = open(msg+'?'+Date.now() ,"Diagram");
			}
		});
	});
});	

/* Save settings after field-settings changed */
function saveDetail(fieldid){
	var axis = 0;
	if($('#xAxis_'+fieldid).is(':checked')){
		axis = 1;
	}else if($('#yAxis_'+fieldid).is(':checked')){
		axis = 2;
	}

	$.ajax({
		method: "POST",
		type: "POST",
		url: "main_dyns_admin.php",
		data: { 
			'actid' : "saveDiagDetail",
			'diag_id' : $('#diag_id').val(),
			'diag_tab_id' : $('#diag_tab_id').val(),
			'field_id' : fieldid,
			'show' : $('#show_'+fieldid).is(':checked'),
			'axis' : axis,
			'color' : rgb2hex($('#color_'+fieldid).css('backgroundColor'))				
		}
	})
	.done(function( rval ) {
		rval = JSON.parse(rval);
		if(rval == null){
			$('.hideShow_'+fieldid).hide();
		}else{			
			$('.hideShow_'+fieldid).show();
			$('#xAxis_'+fieldid).prop('checked', rval['axis']==1);
			$('#yAxis_'+fieldid).prop('checked', rval['axis']==2);	
			$('#color_'+fieldid).css('background-color', '#'+rval['color']);
		}
	});				
}

function rgb2hex(rgb) {
	function hex(x) {
		var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 
		return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
	}
	rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

/* color select div */
var justopened = false;
function divclose(){
	if(justopened == true){
		justopened = false;
	}else{
		$('#menu_color').css('visibility','hidden');
		$('#field_settings').css('visibility','hidden');
	}
}
function showdiv(evt,divid,fieldid) {
	actid=fieldid;
	setxypos(evt,divid);
	$('#'+divid).css('visibility','visible');
	justopened = true;
}
function setColor(value) {
	$('#color_'+actid).css('background-color', '#'+value);
	saveDetail(actid);
}

$(function() {
    $("input[id^='auto_']").change(function() {
        var id = $(this).attr('id');
        var input = $('#' + id.replace('auto_', ''));
        
        // if auto-calculation-checkbox is checked
        if($(this).attr('checked')) {
            // disable corresponding input and set value to 'auto'
            input.prop('disabled', true);
            input.val('auto');
        } else {
            // enable corresponding input and clear value
            input.prop('disabled', false);
            input.val('');
        }
    });
});

</script>

<!-- Color Settings Div -->
<DIV ID="menu_color" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10002;">
    <FORM NAME="fcolor_form"><TABLE BORDER="0" cellspacing="0" cellpadding="0">
        <TR><TD><?pop_top('menu_color');?></TD></TR>
        <TR><TD><?pop_color('setColor',null,'menu_color');?></TD></TR>
        <TR><TD><?pop_bottom();?></TD></TR>
    </TABLE></FORM>
</DIV>


<table class="tabfringe" cellspacing="0" cellpadding="3" border="0">
    <tr class="tabHeader">
        <td class="tabHeaderItem">
            <?php echo htmlentities($gdiaglist[$diag_tab_id]["name"][$diag_id]) . " (" . $lang[2869] . ": " . htmlentities($gtab["desc"][$diag_tab_id]) . ")"; ?>
        </td>
        <td class="tabHeaderItem" style="margin-left:20px; margin-right:10px; padding-left: 10px; border-left:1px solid #ccc;">
            <?php echo $lang[2893]; ?>
        </td>
    </tr>
            
    <tr>      
        <!-- Field-Settings -->    
        <td class="vAlignTop">            
            <TABLE BORDER="0" cellspacing="0" cellpadding="1"> 
                <tr class="tabHeader">
                    <TD class="tabHeaderItem">ID</td>
                    <TD class="tabHeaderItem"></td>
                    <TD class="tabHeaderItem"><?=$lang[2865]?></td>
                    <TD class="tabHeaderItem"><?=$lang[2866]?></td>
                    <TD class="tabHeaderItem" COLSPAN=2><?=$lang[2867]?></td>
                    <TD class="tabHeaderItem"><?=$lang[2868]?></td>
                </tr>

<?php
                foreach($gfield[$diag_tab_id]["key"] as $fieldkey){
                    $checked = "";
                    $checkedXAxis = "";
                    $checkedYAxis = "";
                    $checkedColor = "FBE16B";
                    $style = "style=\"display:none\"";

                    if($diagdetaillist != null && array_key_exists($fieldkey, $diagdetaillist)){
                        $checked = "CHECKED";
                        if($diagdetaillist[$fieldkey]["axis"] == 1){
                            $checkedXAxis = "CHECKED";
                        }elseif($diagdetaillist[$fieldkey]["axis"] == 2){	
                            $checkedYAxis = "CHECKED";
                        }
                        $checkedFunction = $diagdetaillist[$fieldkey]["function"];
                        $checkedColor = $diagdetaillist[$fieldkey]["color"];
                        $style = "";
                    }
?>

                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td valign="top"><?=$fieldkey?></td>
                        <td valign="top">
                            <!--<i class="lmb-icon lmb-cog-alt" onclick="showdiv(event,'field_settings');" style="cursor:pointer;"></i>-->
                        </td>
                        <td valign="top"><?=$gfield[$diag_tab_id]["spelling"][$fieldkey]?></td>
                        <td valign="top">
                            <input id="show_<?=$fieldkey?>" type="checkbox" onchange="saveDetail(<?=$fieldkey?>);" value="0" <?=$checked?>>
                        </td>
                        <td class="hideShow_<?=$fieldkey?>" valign="top" <?=$style?>>
                            <input id="xAxis_<?=$fieldkey?>" name="<?=$fieldkey?>" onchange="saveDetail(<?=$fieldkey?>);" type="radio" <?=$checkedXAxis?>>Daten
                        </td>
                        <td class="hideShow_<?=$fieldkey?>" valign="top" <?=$style?>>
                            <input id="yAxis_<?=$fieldkey?>" name="<?=$fieldkey?>" onchange="saveDetail(<?=$fieldkey?>);" type="radio" <?=$checkedYAxis?>>Beschriftung
                        </td>
                        <td class="hideShow_<?=$fieldkey?>" valign="top" <?=$style?>>
                            <div id="color_<?=$fieldkey?>" onclick="showdiv(event,'menu_color',<?=$fieldkey?>);" style="margin:auto; cursor:pointer; width:20px; height:20px; border:1px solid black; background-color:#<?php echo htmlentities($checkedColor); ?>"></div>
                        </td>
                    </tr>

<?php
                } // end foreach	
?>
            </table>
        </td>
        
        <!-- Customization-Settings -->
        <td class="vAlignTop" style="margin-left:20px; margin-right:10px; padding-left: 10px; border-left:1px solid #ccc;">
            <table BORDER="0" cellspacing="0" cellpadding="2" class="tabfringe">
                <tr class="tabHeader">
                    <td class="tabHeaderItem"><?=$lang[2891]?></td>
                    <td class="tabHeaderItem"><?=$lang[2892]?></td>
                    <td class="tabHeaderItem">Auto-calculation</td>
                </tr>
                <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                    <td><?=$lang[2870]?>:</td>
                    <td><input type="text" id="diag_width" value="<?=$settings['DIAG_WIDTH']?>"> px</td>
                    <td></td>
                </tr>
                <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                    <td><?=$lang[2871]?>:</td>
                    <td><input type="text" id="diag_height" value="<?=$settings['DIAG_HEIGHT']?>"> px</td>
                    <td></td>
                </tr>	
                <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                    <td><?=$lang[2872]?>:</td>
                    <td><input type="text" id="font_size" value="<?=$settings['FONT_SIZE']?>"> pt</td>
                    <td></td>
                </tr>
                <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                    <td><?=$lang[2873]?>:</td>
                    <td><input type="text" id="padding_left" value="<?=$settings['PADDING_LEFT']==null ? 'auto" disabled="disabled' : $settings['PADDING_LEFT']?>"> px</td>
                    <td><input type="checkbox" id="auto_padding_left" <?=$settings['PADDING_LEFT']==null ? 'checked' : ''?>></td>
                </tr>
                <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                    <td><?=$lang[2874]?>:</td>
                    <td><input type="text" id="padding_top" value="<?=$settings['PADDING_TOP']==null ? 'auto" disabled="disabled' : $settings['PADDING_TOP']?>"> px</td>
                    <td><input type="checkbox" id="auto_padding_top" <?=$settings['PADDING_TOP']==null ? 'checked' : ''?>></td>
                </tr>
	
<?php 
                if($settings['DIAG_TYPE'] != "Pie-Chart"){ 
?>

                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2875]?>:</td>
                        <td><input type="text" id="padding_right" value="<?=$settings['PADDING_RIGHT']==null ? 'auto" disabled="disabled' : $settings['PADDING_RIGHT']?>"> px</td>
                        <td><input type="checkbox" id="auto_padding_right" <?=$settings['PADDING_RIGHT']==null ? 'checked' : ''?>></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2876]?>:</td>
                        <td><input type="text" id="padding_bottom" value="<?=$settings['PADDING_BOTTOM']==null ? 'auto" disabled="disabled' : $settings['PADDING_BOTTOM']?>"> px</td>
                        <td><input type="checkbox" id="auto_padding_bottom" <?=$settings['PADDING_BOTTOM']==null ? 'checked' : ''?>></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2877]?>:</td>
                        <td><input type="text" id="text_x" value="<?=$settings['TEXT_X']?>"></td>
                        <td></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2878]?>:</td>
                        <td><input type="text" id="text_y" value="<?=$settings['TEXT_Y']?>"></td>
                        <td></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2879]?>:</td>
                        <td><input type="text" id="legend_x" value="<?=$settings['LEGEND_X']==null ? 'auto" disabled="disabled' : $settings['LEGEND_X']?>"> px</td>
                        <td><input type="checkbox" id="auto_legend_x" <?=$settings['LEGEND_X']==null ? 'checked' : ''?>></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2880]?>:</td>
                        <td><input type="text" id="legend_y" value="<?=$settings['LEGEND_Y']==null ? 'auto" disabled="disabled' : $settings['LEGEND_Y']?>"> px</td>
                        <td><input type="checkbox" id="auto_legend_y" <?=$settings['LEGEND_Y']==null ? 'checked' : ''?>></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2881]?>:</td>
                        <td>
                            <select id="legend_mode" style="width: 160px;">
                                <option value="none" <?=($settings['LEGEND_MODE']=="none")?"SELECTED":""?>><?=$lang[2882]?></option>
                                <option value="vertical" <?=($settings['LEGEND_MODE']=="vertical")?"SELECTED":""?>><?=$lang[2883]?></option>
                                <option value="horizontal" <?=($settings['LEGEND_MODE']=="horizontal")?"SELECTED":""?>><?=$lang[2884]?></option>
                            </select> 
                        </td>
                        <td></td>
                    </tr>
	
<?php 
                } 
                if($settings['DIAG_TYPE'] == "Pie-Chart"){ 
?>
                    
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2885]?>:</td>
                        <td>
                            <select id="pie_write_values" style="width: 160px;">
                                <option value="none" <?=($settings['PIE_WRITE_VALUES']=="none")?"SELECTED":""?>><?=$lang[2886]?></option>
                                <option value="value" <?=($settings['PIE_WRITE_VALUES']=="value")?"SELECTED":""?>><?=$lang[2887]?></option>
                                <option value="percent" <?=($settings['PIE_WRITE_VALUES']=="percent")?"SELECTED":""?>><?=$lang[2888]?></option>
                            </select> 
                        </td>
                        <td></td>
                    </tr>
                    <tr onmouseout="this.style.backgroundColor=''" onmouseover="this.style.backgroundColor='#FBE16B'">
                        <td><?=$lang[2889]?>:</td>
                        <td><input type="text" id="pie_radius" value="<?=$settings['PIE_RADIUS']==null ? 'auto" disabled="disabled' : $settings['PIE_RADIUS']?>"> px</td>
                        <td><input type="checkbox" id="auto_pie_radius" <?=$settings['PIE_RADIUS']==null ? 'checked' : ''?>></td>
                    </tr>
<?php
                }       
?>
                <!-- Submit- and Preview- Buttons -->
                <tr><td colspan=3><hr style="display: block; height: 1px;border: 0; border-top: 1px solid #ccc; padding: 0;"></td></tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="button" id="save_diag_settings" value="<?=$lang[2894]?>">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="button" id="create_diag" value="<?=$lang[2890]?>">
                    </td>
                    <td></td>
                </tr>

            </table>
        </td>
    </tr>
</table>
