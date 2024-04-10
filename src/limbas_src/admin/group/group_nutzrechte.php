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
// --- Wertespeicher ----------
var ftab;
var ffield;
var saverules = new Array();

function save_rules(id){
	saverules[id] = 1;
}

function send(){
	var saval = '';
    for (var e in saverules){
    	var saval = saval + e + ";";    
    }
    document.form1.rules.value = saval;
    document.form1.submit();
}

function check_all(kat,val,sub,main){
	var chk = val.checked;
	var cc = null;
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.id.split("_");
		if(sub == 1){var group = cid[1];var rule = cid[3];}
		else if(sub == 2){var group = cid[2];var rule = cid[3]}
		if(cc.type == "checkbox" && group == kat && main == cid[1]){
			if(chk){
				cc.checked = 1;
			}else{
				cc.checked = 0;
			}
			save_rules(rule);
		}
	}
}
</script>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="ID" value="<?=$ID?>">
        <input type="hidden" name="action" value="setup_group_nutzrechte">
        <input type="hidden" name="rules">

        <div class="row">
        <?php
        $activeTabLinkId = 76;
        require(__DIR__.'/group_tabs.php') ?>

        <div class="tab-content col-9 border border-start-0 bg-contrast">
            <div class="tab-pane active p-3">

                <h5><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></h5>

                <hr>

                <table class="table table-sm table-striped table-hover border bg-contrast">
                    <thead>
                    <tr>
                        <th><?=$lang[573]?></th>
                        <th><?=$lang[126]?></th>
                        <th></th>
                        <th><?=$lang[575]?></th>
                    </tr>
                    </thead>

                    <?php foreach($link_groupdesc as $key => $value): ?>
                    
                    <tr class="table-section">
                        <td colspan="3"><?=$link_groupdesc[$key][0]?></td>
                        <td><input type="checkbox" onchange="check_all('<?=$key?>',this,1,'<?=$key?>');"></td>
                    </tr>
                    
                    <?php

                        foreach($link_groupdesc[$key] as $key2 => $value2){
                            foreach($result_links["sort"] as $bzm => $value0){
                                if($result_links["maingroup"][$bzm] == $key AND $result_links["subgroup"][$bzm] == $key2){
                                    if($result_links["subgroup"][$bzm] != $tmpsubg):?>
                                        
                                    <tr class="table-sub-section"><td colspan="3"><?=$link_groupdesc[$key][$result_links["subgroup"][$bzm]]?></td><td><input type="checkbox" onchange="check_all('<?=$result_links['subgroup'][$bzm]?>',this,2,'<?=$key?>');"></td></tr>
                                    
                                        <?php
                                    endif;
                                    $tmpsubg = $result_links["subgroup"][$bzm];
                                    if($LINK[$bzm] OR $session["superadmin"] == 1): ?>
                                        
                                    <tr>
                                        <td><?=$lang[$result_links["name"][$bzm]]?></td>
                                        <td><?=$lang[$result_links["desc"][$bzm]]?></td>
                                        <td>
                                            <?php if($result_links["icon_url"][$bzm]): ?>
                                            <i class="lmb-icon <?=$result_links["icon_url"][$bzm]?>"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($result_lgroup_link["PERM"][$bzm] == 2 OR !$result_lgroup_link["PERM"]): ?>
                                                <input type="checkbox" ID="menu_<?=$key?>_<?=$tmpsubg?>_<?=$result_links["link_id"][$bzm]?>" name="menu[<?=$result_links['link_id'][$bzm]?>]" onchange="save_rules('<?=$result_links['link_id'][$bzm]?>');" <?=($result_links["perm"][$bzm] == 2)?'checked':''?>>
                                            <?php else: ?>
                                                <input type="checkbox" readonly disabled>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                        
                                        
                                        <?php
                                        
                                        
                                    endif;

                                }
                            }
                        }
                    endforeach;
                    ?>

                </table>
                
                
                <?php
                $useSubmitJavascript = true;
                require __DIR__ . '/submit-footer.php'; ?>

            </div>
        </div>
        </div>


    </FORM>
</div>


    
