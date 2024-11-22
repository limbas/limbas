<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<script>

$(function(){
    $('[data-load-chain]').click(function (event) {
        document.form5.submit();
    });
});

function create_view() {
	alert('<?=$lang[2214]?>');
}

function drop_view() {
	val = confirm('<?=$lang[2370]?>',' ');
	if(val){
		document.form1.drop_viev.value = 1;
	}
}

function LIM_relationTree(rel){
	val = confirm('<?=$lang[2856]?>',' ');
	if(val){
		document.form1.relationtree.value = rel;
		document.form1.submit();
	}
}




function changeorder(el,id){
	
	maxsort = 0;
	$(".verknviewid").each(function(index) {
		if($( this ).prop( "checked" )){
			esort = parseInt($("#verknviewid_"+$(this).attr('elid')).text());
			if(esort > maxsort){maxsort = esort;}
		}
	});
	
	maxsort = parseInt(maxsort)+1;

	if(el.checked){
		document.getElementById("verknviewid_"+id).innerHTML = maxsort;
		document.getElementById("verknsort_"+id).value = maxsort;
	}else{
		document.getElementById("verknviewid_"+id).innerHTML = '';
		document.getElementById("verknsort_"+id).value = '';
	}
	
	

}


function changeorderf(el,id){
	
	maxsort = 0;
	$(".verknfindid").each(function(index) {
		if($( this ).prop( "checked" )){
			esort = $("#verknfindid_"+$(this).attr('elid')).text();
			if(esort > maxsort){maxsort = esort;}
		}
	});
	
	maxsort = parseInt(maxsort)+1;

	if(el.checked){
		document.getElementById("verknfindid_"+id).innerHTML = maxsort;
		document.getElementById("verknsortf_"+id).value = maxsort;
	}else{
		document.getElementById("verknfindid_"+id).innerHTML = '';
		document.getElementById("verknsortf_"+id).value = '';
	}
	
	

}

</SCRIPT>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?=(!$category || $category=='edit')?'active':''?>" id="general-tab" data-bs-toggle="tab" href="#general" role="tab"><?=$lang[1634]?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?=($category=='relation')?'active':''?>" id="filters-tab" data-bs-toggle="tab" href="#filters" role="tab"><?=$lang[2376]?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?=($category=='relparams')?'active':''?>" id="params-tab" data-bs-toggle="tab" href="#params" role="tab"><?=$lang[2331]?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?=($category=='relationtree')?'active':''?>" id="chain-tab" data-bs-toggle="tab" href="#chain" role="tab" <?=($category=='relationtree')?'':'data-load-chain="1"'?>><?=$lang[3013]?></a>
        </li>
        <?php if($rfield['verkntabletype'] == 1 AND $rfield['datatype'] != 25): ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?=($category=='create')?'active':''?>" id="generator-tab" data-bs-toggle="tab" href="#generator" role="tab"><?=$lang[1823]?></a>
            </li>
        <?php endif; ?>
    </ul>


    <div style="position:absolute;top:10px;right:15px;">
        <a target="new" href="<?=$LINK["help_url"][$LINK_ID[$action]]?>">
            <i align="absmiddle" border="0" class="lmb-icon lmb-help"></i>
        </a>
    </div>


    <div class="tab-content border border-top-0 bg-contrast p-3">
        <div class="tab-pane <?=(!$category || $category=='edit')?'show active':''?>" id="general" role="tabpanel">
            <?php include(__DIR__.'/verkn_editor/general.php'); ?>
        </div>
        <div class="tab-pane <?=($category=='relation')?'show active':''?>" id="filters" role="tabpanel">
            <?php if($fieldid AND $tabid AND $rfield['verkntabid'] AND $rfield['datatype'] != 23){
                require(__DIR__.'/verkn_editor/filters.php');
            }?>
        </div>
        <div class="tab-pane <?=($category=='relparams')?'show active':''?>" id="params" role="tabpanel">
            <?php require(__DIR__.'/verkn_editor/params.php'); ?>
        </div>
        <div class="tab-pane <?=($category=='relationtree')?'show active':''?>" id="chain" role="tabpanel">
            <?php require(__DIR__.'/verkn_editor/chain.php'); ?>
        </div>
        <div class="tab-pane <?=($category=='create')?'show active':''?>" id="generator" role="tabpanel">
            <?php
            if($fieldid AND $tabid AND $rfield['verkntabid'] AND $rfield['datatype'] != 23){
                require(__DIR__.'/verkn_editor/generator.php');
            }
            ?>
        </div>


    </div>




<?php





function relationtree($rfield){
    global $lang;
    global $gfield;
    global $gtab;

    if(($rfield['verkntabletype'] == 1 OR $rfield['verkntabletype'] == 3) AND $rfield['datatype'] != 25 AND $tree = recrelationtree($rfield)){
        
        foreach($tree as $tkey => $path){
            $tree_identifier = md5(implode(",",$path));
            $CHECKED = '';
            if($rfield['verkntree'] == $tree_identifier){$CHECKED = 'CHECKED';}


            echo '<div class="mb-3 form-check">';
            echo '<label class="form-check-label">';
            
            
            
            echo "<input type=\"checkbox\" class=\"form-check-input\" onclick=\"LIM_relationTree('".implode(",",$path)."')\" $CHECKED>";
            $tabname = array();

            foreach($path as $key => $md5tab){
                $vTabID = getTabFromMd5($md5tab);

                if (isset($path[$key - 1])) {
                    $lastTab = getTabFromMd5($path[$key - 1]);
                } else {
                    $lastTab = $tabid;
                }

                $fieldName = '';
                foreach($gfield[$lastTab]['md5tab'] as $fieldKey => $md5) {
                    if ($md5 == $md5tab) {
                         $fieldName = $gfield[$lastTab]['field_name'][$fieldKey];
                         break;
                    }
                }
                $tabname[] = "<span title=\"{$fieldName}\">{$gtab['table'][$vTabID]}</span>";
            }
            echo '&rarr;' . implode(" &rarr; ",$tabname);
            echo '</label></div>';
        }
        
        if (empty($tree)) {
            return false;
        }
        return true;
    }
    return false;
}


function rec_verknpf_tabs($gtabid,$verkntab){
	static $recmd5;
	global $gfield;
	global $gtab;

	if(!$recmd5){$recmd5 = array();}
	if($gfield[$gtabid]["sort"]){
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		if($gfield[$gtabid]["field_type"][$key] == 11){

			if($gtabid == $verkntab){return;}
			if(in_array($gfield[$gtabid]["md5tab"][$key],$recmd5)){return;}
			$recmd5[] = $gfield[$gtabid]["md5tab"][$key];

			if($gfield[$gtabid]["verkntabletype"][$key] == 1){
				echo "</td><td class=\"text-info cursor-pointer\" OnCLick=\"document.getElementById('relation_preview').value='".$gtab["table"][$gtabid].".ID = ".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".ID \\nAND \\n".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".VERKN_ID = ".$gtab["table"][$gfield[$gtabid]["verkntabid"][$key]].".ID';\">".$gtab["desc"][$gtabid]."</td><td><i class=\"lmb-icon lmb-long-arrow-right\"></i></td><td>".$gtab["desc"][$gfield[$gtabid]["verkntabid"][$key]]."</td></tr>";
			}else{
				echo "</td><td class=\"text-info cursor-pointer\" OnCLick=\"document.getElementById('relation_preview').value='".$gtab["table"][$gtabid].".ID = ".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".ID \\nAND \\n".lmb_strtoupper($gfield[$gtabid]["md5tab"][$key]).".VERKN_ID = ".$gtab["table"][$gfield[$gtabid]["verkntabid"][$key]].".ID';\">".$gtab["desc"][$gtabid]."</td><td><i class=\"lmb-icon lmb-long-arrow-left\"></i></td><td>".$gtab["desc"][$gfield[$gtabid]["verkntabid"][$key]]."</td></tr>";
			}

			rec_verknpf_tabs($gfield[$gtabid]["verkntabid"][$key],$verkntab);
		}
	}}
	return;
}


function edit_relationparams($tabid,$fieldid){
    global $lang;

    $params = getRelationParameter($tabid,$fieldid);

    foreach ($params as $key => $value){
        if($value == 1){
            ${$key} = 'checked';
        }
    }

    ${'show_inframe_'.$params['show_inframe']} = 'selected';
    ${'viewmode_'.$params['viewmode']} = 'selected';
    ${'validity_'.$params['validity']} = 'selected';

    $show_inframe_mods = array('div','iframe','same','tab');
    if($params['show_inframe'] AND !in_array($params['show_inframe'],$show_inframe_mods)){$show_inframe_tag = 'selected';}else{$params['show_inframe'] = null;}
    if($params['show_inframe'] OR $show_inframe_tag){$inframe_tag_display = '';}else{$inframe_tag_display = 'none';}

    echo "
    <table
    <tr><td><i>{$lang[3038]}</i></td><td>
        <select name=\"params[show_inframe]\" onchange=\"if(this.value == 'tag'){document.getElementById('inframe_tag').style.display='';}\"><option>
        <option value=\"window\" $show_inframe_window>new window
        <option value=\"div\" $show_inframe_div>div
        <option value=\"iframe\" $show_inframe_iframe>iframe
        <option value=\"same\" $show_inframe_same>same
        <option value=\"tab\" $show_inframe_tab>new tab
        <option value=\"tag\" $show_inframe_tag>tag (Element-ID)
        </select>&nbsp;
    <input style=\"display:$inframe_tag_display\" id=\"inframe_tag\" type=\"text\" name=\"params[show_inframe_tag]\" size=\"5\" value=\"".htmlentities($params['show_inframe'],ENT_QUOTES)."\">
    </td></tr>
    <tr><td><i>{$lang[3014]}</i></td><td><select name=\"params[viewmode]\">
        <option>
        <option value=\"dropdown\" $viewmode_dropdown>dropdown
        <option value=\"single_ajax\" $viewmode_single_ajax>single_ajax
        <option value=\"multi_ajax\" $viewmode_multi_ajax>multi_ajax
        </select>
    </td></tr>
    <tr><td><i>{$lang[3002]}</i></td><td><select name=\"params[validity]\">
        <option>
        <option value=\"all\" $validity_all>all
        <option value=\"allto\" $validity_allto>all from today
        <option value=\"allfrom\" $validity_allfrom>all to today
        </select>
    </td></tr>
        
    <tr><td><i>{$lang[3015]}</i></td><td><input type=\"text\" name=\"params[formid]\" value=\"".htmlentities($params['formid'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3049]}</i></td><td><input type=\"text\" name=\"params[formsize]\" value=\"".htmlentities($params['formsize'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3016]}</i></td><td><input type=\"text\" name=\"params[count]\" value=\"".htmlentities($params['count'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3017]}</i></td><td><input type=\"text\" name=\"params[ondblclick]\" value=\"".htmlentities($params['ondblclick'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3039]}</i></td><td><input type=\"text\" name=\"params[showfields]\" value=\"".htmlentities($params['showfields'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3041]}</i></td><td><input type=\"text\" name=\"params[readonly]\" value=\"".htmlentities($params['readonly'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3040]}</i></td><td><input type=\"text\" name=\"params[width]\" value=\"".htmlentities($params['width'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3042]}</i></td><td><input type=\"text\" name=\"params[order]\" value=\"".htmlentities($params['order'],ENT_QUOTES)."\"></td></tr>
    <tr><td><i>{$lang[3070]}</i></td><td><input type=\"checkbox\" name=\"params[applyfilter]\" value=\"1\" $applyfilter></td></tr>
    <tr><td><i>{$lang[3029]}</i></td><td><input type=\"checkbox\" name=\"params[no_menu]\" value=\"1\" $no_menu></td></tr>
    <tr><td><i>{$lang[3018]}</i></td><td><input type=\"checkbox\" name=\"params[no_add]\" value=\"1\" $no_add></td></tr>
    <tr><td><i>{$lang[3019]}</i></td><td><input type=\"checkbox\" name=\"params[no_new]\" value=\"1\" $no_new></td></tr>
    <tr><td><i>{$lang[3020]}</i></td><td><input type=\"checkbox\" name=\"params[no_edit]\" value=\"1\" $no_edit></td></tr>
    <tr><td><i>{$lang[3021]}</i></td><td><input type=\"checkbox\" name=\"params[no_replace]\" value=\"1\" $no_replace></td></tr>
    <tr><td><i>{$lang[3022]}</i></td><td><input type=\"checkbox\" name=\"params[no_search]\" value=\"1\" $no_search></td></tr>
    <tr><td><i>{$lang[3023]}</i></td><td><input type=\"checkbox\" name=\"params[no_copy]\" value=\"1\" $no_copy></td></tr>
    <tr><td><i>{$lang[3024]}</i></td><td><input type=\"checkbox\" name=\"params[no_delete]\" value=\"1\" $no_delete></td></tr>
    <tr><td><i>{$lang[3025]}</i></td><td><input type=\"checkbox\" name=\"params[no_sort]\" value=\"1\" $no_sort></td></tr>
    <tr><td><i>{$lang[3026]}</i></td><td><input type=\"checkbox\" name=\"params[no_link]\" value=\"1\" $no_link></td></tr>
    <tr><td><i>{$lang[3027]}</i></td><td><input type=\"checkbox\" name=\"params[no_openlist]\" value=\"1\" $no_openlist></td></tr>
    <tr><td><i>{$lang[3028]}</i></td><td><input type=\"checkbox\" name=\"params[no_fieldselect]\" value=\"1\" $no_fieldselect></td></tr>
    <tr><td><i>{$lang[3051]}</i></td><td><input type=\"checkbox\" name=\"params[no_validity]\" value=\"1\" $no_validity></td></tr>
    <tr><td><i>{$lang[3030]}</i></td><td><input type=\"checkbox\" name=\"params[search]\" value=\"1\" $search></td></tr>
    <tr><td><i>{$lang[3143]}</i></td><td><input type=\"checkbox\" name=\"params[edit]\" value=\"1\" $edit></td></tr>
    <tr><td><i>{$lang[3144]}</i></td><td><input type=\"checkbox\" name=\"params[editnm]\" value=\"1\" $editnm></td></tr>
    <tr><td><i>{$lang[3031]}</i></td><td><input type=\"checkbox\" name=\"params[showall]\" value=\"1\" $showall></td></tr>
    <tr><td><i>{$lang[3032]}</i></td><td><input type=\"checkbox\" name=\"params[getlongval]\" value=\"1\" $getlongval></td></tr>
    <tr><td><i>{$lang[3033]}</i></td><td><input type=\"checkbox\" name=\"params[nogresult]\" value=\"1\" $nogresult></td></tr>
    <tr><td><i>{$lang[3034]}</i></td><td><input type=\"checkbox\" name=\"params[no_calendar]\" value=\"1\" $no_calendar></td></tr>
    <tr><td><i>{$lang[3035]}</i></td><td><input type=\"checkbox\" name=\"params[pagination]\" value=\"1\" $pagination></td></tr>
    <tr><td><i>{$lang[3036]}</i></td><td><input type=\"checkbox\" name=\"params[indicator]\" value=\"1\" $indicator></td></tr>
    <tr><td><i>{$lang[3037]}</i></td><td><input type=\"checkbox\" name=\"params[show_relationpath]\" value=\"1\" $show_relationpath></td></tr>

    </table>
    ";

}


if(isset($_POST['vknsave'])): ?>
    
<script>
    lmbShowSuccessMsg('<?=e($lang[2006])?>');
</script>

<?php endif; ?>
