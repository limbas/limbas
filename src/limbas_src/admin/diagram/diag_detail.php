<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/* Get Field Settings */
$sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS, FUNCTION, COLOR FROM LMB_CHARTS WHERE CHART_ID=$diag_id";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$diagdetaillist = array();
while(lmbdb_fetch_row($rs)){
	$fi = lmbdb_result($rs, "FIELD_ID");
	$diagdetaillist[$fi]["axis"] = lmbdb_result($rs, "AXIS");
	$diagdetaillist[$fi]["function"] = lmbdb_result($rs, "FUNCTION");
	$diagdetaillist[$fi]["color"] = lmbdb_result($rs, "COLOR");
}

/* Get Customization Settings */
$settingnames = array('DIAG_TYPE','DIAG_WIDTH','DIAG_HEIGHT','TEXT_X','TEXT_Y','FONT_SIZE','PADDING_LEFT',
				'PADDING_TOP','PADDING_RIGHT','PADDING_BOTTOM','LEGEND_X','LEGEND_Y','LEGEND_MODE',
				'PIE_WRITE_VALUES','PIE_RADIUS');
$sqlquery = "SELECT " . implode(",", $settingnames) . " FROM LMB_CHART_LIST WHERE ID=$diag_id";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(lmbdb_fetch_row($rs)) {
    $settings = array();
    foreach ($settingnames as $name) {
        $settings[$name] = lmbdb_result($rs, $name);
    }
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
            if (ajaxEvalScript(msg)) {
                msg = msg.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, '');
            }
			if(msg){
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
        <TR><TD><?php pop_top('menu_color');?></TD></TR>
        <TR><TD><?php pop_color('setColor',null,'menu_color');?></TD></TR>
        <TR><TD><?php pop_bottom();?></TD></TR>
    </TABLE></FORM>
</DIV>
<div class="bg-white p-3 border">
<div class="row">
    <div class="col-6">
        <h5><?= htmlentities($gdiaglist[$diag_tab_id]["name"][$diag_id]) . " (" . $lang[2023] . ": " . htmlentities($gtab["desc"][$diag_tab_id]) . ")" ?></h5>
        <table class="table table-sm table-striped mb-0 border">
            <tr>
                <th>ID</th>
                <th></th>
                <th><?=$lang[922]?></th>
                <th><?=$lang[2631]?></th>
                <th COLSPAN=2><?=$lang[2867]?></th>
                <th><?=$lang[294]?></th>
            </tr>

            <?php
            foreach($gfield[$diag_tab_id]["key"] as $fieldkey):
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

                <tr>
                    <td><?=$fieldkey?></td>
                    <td>
                        <!--<i class="lmb-icon lmb-cog-alt" onclick="showdiv(event,'field_settings');" style="cursor:pointer;"></i>-->
                    </td>
                    <td><?=$gfield[$diag_tab_id]["spelling"][$fieldkey]?></td>
                    <td>
                        <input id="show_<?=$fieldkey?>" type="checkbox" onchange="saveDetail(<?=$fieldkey?>);" value="0" <?=$checked?>>
                    </td>
                    <td class="hideShow_<?=$fieldkey?>" <?=$style?>>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="xAxis_<?=$fieldkey?>" name="<?=$fieldkey?>" onchange="saveDetail(<?=$fieldkey?>);" <?=$checkedXAxis?>>
                            <label class="form-check-label" for="xAxis_<?=$fieldkey?>">
                                Daten
                            </label>
                        </div>
                    </td>
                    <td class="hideShow_<?=$fieldkey?>" <?=$style?>>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="yAxis_<?=$fieldkey?>" name="<?=$fieldkey?>" onchange="saveDetail(<?=$fieldkey?>);" <?=$checkedYAxis?>>
                            <label class="form-check-label" for="xAxis_<?=$fieldkey?>">
                                Beschriftung
                            </label>
                        </div>
                    </td>
                    <td class="hideShow_<?=$fieldkey?>" valign="top" <?=$style?>>
                        <div id="color_<?=$fieldkey?>" onclick="showdiv(event,'menu_color',<?=$fieldkey?>);" style="margin:auto; cursor:pointer; width:20px; height:20px; border:1px solid black; background-color:#<?= htmlentities($checkedColor) ?>"></div>
                    </td>
                </tr>

                <?php
            endforeach;	
            ?>
        </table>
    </div>
    <div class="col-6">
        <h5><?= $lang[2893] ?></h5>
        <table class="table table-sm table-striped mb-0 border">
            <tr>
                <th><?=$lang[2891]?></th>
                <th><?=$lang[29]?></th>
                <th>Auto-calculation</th>
            </tr>
            <tr>
                <td><?=$lang[1141]?>:</td>
                <td><div class="input-group input-group-sm"><input type="text" id="diag_width" value="<?=$settings['DIAG_WIDTH']?>" class="form-control form-control-sm">
                                <span class="input-group-text">px</span>
                        </div></td>
                <td></td>
            </tr>
            <tr>
                <td><?=$lang[1142]?>:</td>
                <td><div class="input-group input-group-sm"><input type="text" id="diag_height" value="<?=$settings['DIAG_HEIGHT']?>" class="form-control form-control-sm"><span class="input-group-text">px</span>
                        </div></td>
                <td></td>
            </tr>
            <tr>
                <td><?=$lang[2872]?>:</td>
                <td><div class="input-group input-group-sm"><input type="text" id="font_size" value="<?=$settings['FONT_SIZE']?>" class="form-control form-control-sm"><span class="input-group-text">pt</span>
                    </div></td>
                <td></td>
            </tr>
            <tr>
                <td><?=$lang[2873]?>:</td>
                <td><div class="input-group input-group-sm"><input type="text" id="padding_left" value="<?=$settings['PADDING_LEFT']==null ? 'auto" disabled="disabled' : $settings['PADDING_LEFT']?>" class="form-control form-control-sm"><span class="input-group-text">px</span>
                        </div></td>
                <td><input type="checkbox" id="auto_padding_left" <?=$settings['PADDING_LEFT']==null ? 'checked' : ''?>></td>
            </tr>
            <tr>
                <td><?=$lang[2874]?>:</td>
                <td><div class="input-group input-group-sm"><input type="text" id="padding_top" value="<?=$settings['PADDING_TOP']==null ? 'auto" disabled="disabled' : $settings['PADDING_TOP']?>" class="form-control form-control-sm"><span class="input-group-text">px</span>
                        </div></td>
                <td><input type="checkbox" id="auto_padding_top" <?=$settings['PADDING_TOP']==null ? 'checked' : ''?>></td>
            </tr>

            <?php
            if($settings['DIAG_TYPE'] != "Pie-Chart"){
                ?>

                <tr>
                    <td><?=$lang[2875]?>:</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" id="padding_right" value="<?=$settings['PADDING_RIGHT']==null ? 'auto" disabled="disabled' : $settings['PADDING_RIGHT']?>" class="form-control form-control-sm">
                            <span class="input-group-text">px</span>
                        </div>
                    </td>
                    <td><input type="checkbox" id="auto_padding_right" <?=$settings['PADDING_RIGHT']==null ? 'checked' : ''?>></td>
                </tr>
                <tr>
                    <td><?=$lang[2876]?>:</td>
                    <td><div class="input-group input-group-sm"><input type="text" id="padding_bottom" value="<?=$settings['PADDING_BOTTOM']==null ? 'auto" disabled="disabled' : $settings['PADDING_BOTTOM']?>" class="form-control form-control-sm"><span class="input-group-text">px</span></td>
                    <td><input type="checkbox" id="auto_padding_bottom" <?=$settings['PADDING_BOTTOM']==null ? 'checked' : ''?>></td>
                </tr>
                <tr>
                    <td><?=$lang[2877]?>:</td>
                    <td><input type="text" id="text_x" value="<?=$settings['TEXT_X']?>" class="form-control form-control-sm"></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?=$lang[2878]?>:</td>
                    <td><input type="text" id="text_y" value="<?=$settings['TEXT_Y']?>" class="form-control form-control-sm"></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?=$lang[2879]?>:</td>
                    <td><div class="input-group input-group-sm"><input type="text" id="legend_x" value="<?=$settings['LEGEND_X']==null ? 'auto" disabled="disabled' : $settings['LEGEND_X']?>" class="form-control form-control-sm"><span class="input-group-text">px</span>
                        </div></td>
                    <td><input type="checkbox" id="auto_legend_x" <?=$settings['LEGEND_X']==null ? 'checked' : ''?>></td>
                </tr>
                <tr>
                    <td><?=$lang[2880]?>:</td>
                    <td><div class="input-group input-group-sm"><input type="text" id="legend_y" value="<?=$settings['LEGEND_Y']==null ? 'auto" disabled="disabled' : $settings['LEGEND_Y']?>" class="form-control form-control-sm"><span class="input-group-text">px</span>
                        </div></td>
                    <td><input type="checkbox" id="auto_legend_y" <?=$settings['LEGEND_Y']==null ? 'checked' : ''?>></td>
                </tr>
                <tr>
                    <td><?=$lang[2881]?>:</td>
                    <td>
                        <select id="legend_mode" class="form-select form-select-sm">
                            <option value="none" <?=($settings['LEGEND_MODE']=="none")?"SELECTED":""?>><?=$lang[2882]?></option>
                            <option value="vertical" <?=($settings['LEGEND_MODE']=="vertical")?"SELECTED":""?>><?=$lang[1245]?></option>
                            <option value="horizontal" <?=($settings['LEGEND_MODE']=="horizontal")?"SELECTED":""?>><?=$lang[1244]?></option>
                        </select>
                    </td>
                    <td></td>
                </tr>

                <?php
            }
            if($settings['DIAG_TYPE'] == "Pie-Chart"){
                ?>

                <tr>
                    <td><?=$lang[2885]?>:</td>
                    <td>
                        <select id="pie_write_values" class="form-select form-select-sm">
                            <option value="none" <?=($settings['PIE_WRITE_VALUES']=="none")?"SELECTED":""?>><?=$lang[2886]?></option>
                            <option value="value" <?=($settings['PIE_WRITE_VALUES']=="value")?"SELECTED":""?>><?=$lang[2887]?></option>
                            <option value="percent" <?=($settings['PIE_WRITE_VALUES']=="percent")?"SELECTED":""?>><?=$lang[2888]?></option>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td><?=$lang[2889]?>:</td>
                    <td><div class="input-group input-group-sm"><input type="text" id="pie_radius" value="<?=$settings['PIE_RADIUS']==null ? 'auto" disabled="disabled' : $settings['PIE_RADIUS']?>" class="form-control form-control-sm"><span class="input-group-text">px</span>
                        </div></td>
                    <td><input type="checkbox" id="auto_pie_radius" <?=$settings['PIE_RADIUS']==null ? 'checked' : ''?>></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
<div class="pt-3 text-end">
    <button class="btn btn-primary btn-sm" type="button" id="save_diag_settings"><?=$lang[2894]?></button> <button class="btn btn-primary btn-sm" type="button" id="create_diag"><?=$lang[1739]?></button>
</div>
</div>
