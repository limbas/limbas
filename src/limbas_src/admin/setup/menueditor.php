<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>

<?php if(!$tab){$tab = 1;} ?>


<script src="assets/js/admin/setup/menueditor.js?v=<?=$umgvar["version"]?>"></script>


<div class="container-fluid p-3" data-linkid="<?=$linkid?>">
    <form name="form1" method="post" action="main_admin.php">
        <input type="hidden" name="action" value="setup_menueditor">
        <input type="hidden" name="tab" value="<?=$tab?>">
        <input type="hidden" name="linkid" value="<?=$linkid?>">
        <input type="hidden" name="listact">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?=(($tab==1)?'active bg-contrast':'')?>" href="main_admin.php?action=setup_menueditor&tab=1"><?=$lang[2981]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=(($tab==2)?'active bg-contrast':'')?>" href="main_admin.php?action=setup_menueditor&tab=2"><?=$lang[2982]?></a>
            </li>
        </ul>
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active p-3">

                <?php if($tab == 1): ?>


                    <?php if(!$linkid): ?>
                <div id="lmbMainContainer">
                        <?php $menusExist = false; ?>
                        <ul class="list-unstyled m-0">
                        
                    
                        <?php foreach ($LINK['name'] as $key => $value): ?>
                            <?php if ($LINK['typ'][$key] == 1 AND ($LINK['subgroup'][$key] == 2 OR $LINK['subgroup'][$key] == 3) AND $key >= 1000): ?>
                                <li><a href="main_admin.php?action=setup_menueditor&linkid=<?=$key?>"><i class="lmb-icon lmb-edit"></i> <?= $lang[$value] ?></a></li>
                                <?php $menusExist = true; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </ul>
                    
                        <?php if(!$menusExist): ?>
                            No extended menu exists. First add an individual mainmenu link in <a href="main_admin.php?action=setup_links"><u>menu functions</u></a>
                        <?php endif; ?>
                    
                </div>
                    <?php else: ?>

                        <select class="form-select w-auto" onchange="document.form1.linkid.value=this.value;document.form1.submit();">
                            <?php foreach($LINK['name'] as $key => $value): ?>
                                <?php if ($LINK['typ'][$key] == 1 AND $LINK['subgroup'][$key] == 2 AND $key >= 1000): ?>
                                    <option value="<?=$key?>" <?=$key == $linkid ? 'selected' : ''?>><?=$lang[$value]?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    
                    <div id="lmbMainContainer">
                        <?php
                        $custmenu = lmb_custmenuGet($linkid);
                        lmb_custmenu($custmenu,$linkid,0);
                        ?>
                    </div>
                        
                    
                    <?php endif; ?>
                
                <?php elseif($tab == 2): ?>
                    
                    <?php
                    if($custmenulist){
                        lmb_custmenulistEdit($listact,$linkid,$custmenulist);
                    }
                    $custmenu_list = lmb_custmenulistGet();
                    ?>
                    
                    <?php if(!$linkid OR $listact): ?>
                        <div id="lmbMainContainer">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><?=$lang[4]?></th>
                                        <th><?=$lang[164]?></th>
                                        <th><?=$lang[168]?></th>
                                        <th><?=$lang[925]?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <?php foreach ($custmenu_list['name'] as $id => $name): ?>
                                
                                <tr>
                                    <td class="align-middle"><a href="main_admin.php?action=setup_menueditor&tab=2&linkid=<?=$id?>"><i class="lmb-icon lmb-pencil cursor-pointer"></a></i></td>
                                    <td><input type="text" class="form-control form-control-sm" name="custmenulist[<?=$id?>][name]" value="<?=$lang[$name]?>" onchange="document.form1.listact.value='change';document.form1.linkid.value='<?=$id?>';document.form1.submit();"></td>
                                    <td>
                                        <select name="custmenulist[<?=$id?>][tabid]" class="form-select form-select-sm" onchange="document.form1.listact.value='change';document.form1.linkid.value='<?=$id?>';document.form1.submit();">
                                            <option></option>
                                            <?php foreach ($tabgroup["id"] as $key0 => $value0): ?>
                                                <optgroup label="<?=$tabgroup["name"][$key0]?>">
                                                    <?php foreach ($gtab["tab_id"] as $key => $value): ?>
                                                        <?php if($gtab["tab_group"][$key] == $value0): ?>
                                                            <option value="<?=$value?>" <?=($custmenu_list['tabid'][$id] == $value) ? 'selected' : ''?>><?=$gtab['desc'][$key]?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="custmenulist[<?=$id?>][fieldid]" class="form-select form-select-sm" onchange="document.form1.listact.value='change';document.form1.linkid.value='<?=$id?>';document.form1.submit();">
                                            <option></option>
                                            <?php if($tabid = $custmenu_list['tabid'][$id]): ?>
                                                <?php foreach($gfield[$tabid]['field_name'] as $fkey => $fval): ?>
                                                    <option value="<?=$fkey?>" <?=$custmenu_list['fieldid'][$id] == $fkey ? 'selected' : ''?>><?=$fval?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                        $type_ = array();
                                        $type_[$custmenu_list['type'][$id]] = 'SELECTED';
                                        ?>
                                        <select name="custmenulist[<?=$id?>][type]" class="form-select form-select-sm" onchange="document.form1.listact.value='change';document.form1.linkid.value='<?=$id?>';document.form1.submit();">
                                            <option></option>
        
                                            <optgroup label="<?=$lang[365]?>">
                                                <option value="1" <?=$type_[1]?>><?=$lang[2983]?></option>
                                                <option value="18" <?=$type_[18]?>><?=$lang[2983] . ' ' . $lang[3185]?></option>
                                                <option value="3" <?=$type_[3]?>><?=$lang[545]?></option>
                                                <option value="4" <?=$type_[4]?>><?=$lang[843]?></option>
                                                <option value="5" <?=$type_[5]?>><?=$lang[1625]?></option>
                                                <option value="6" <?=$type_[6]?>><?=$lang[1939]?></option>
                                                <option value="2" <?=$type_[2]?>><?=$lang[2984]?></option>
                                                <option value="17" <?=$type_[17]?>><?=$lang[2984] . ' ' . $lang[3185]?></option>
                                            </optgroup>
                                            <optgroup label="<?=$lang[357]?>">
                                                <option value="13" <?=$type_[13]?>><?=$lang[545]?></option>
                                                <option value="15" <?=$type_[15]?>><?=$lang[1625]?></option>
                                                <option value="16" <?=$type_[16]?>><?=$lang[1939]?></option>
                                                <option value="12" <?=$type_[12]?>><?=$lang[2984]?></option>
                                                <option value="14" <?=$type_[14]?>><?=$lang[2984] . ' ' . $lang[3185]?></option>
                                            </optgroup>
                                            <optgroup label="<?=$lang[1460]?>">
                                                <option value="20" <?=$type_[20]?>><?=$lang[2983]?></option>
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="javascript:if(confirm('delete?')){document.form1.listact.value='del';document.form1.linkid.value='<?=$id?>';document.form1.submit();}">
                                        <i class="lmb-icon lmb-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <?php endforeach; ?>
                                
                            </table>
        
                            <div class="row row-cols-lg-auto gap-3 align-items-center pt-3">
                                <div class="col-12">
                                    <input type="text" name="custmenulist[add][name]" class="form-control">
                                </div>
        
                                <div class="col-12">
                                    <button class="btn btn-primary" type="submit" onclick="document.form1.listact.value='add';document.form1.submit();"><?=$lang[540]?></button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div id="lmbMainContainer">
                            <?php
                            $custmenu = lmb_custmenuGet($linkid);
                            lmb_custmenu($custmenu,$linkid,0);
                            ?>
                        </div>
                    <?php endif; ?>
                
                <?php endif; ?>
                

            </div>
        </div>



    </form>
</div>
