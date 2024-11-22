<?php



require_once (COREPATH . 'admin/user/user_permission.dao');


$tableRuleUser1 = getUserTablePerms($user_id);
$tableRuleUser2 = getUserTablePerms($compare_id);

$fieldRuleUser1 = getUserFieldPerms($user_id);
$fieldRuleUser2 = getUserFieldPerms($compare_id);

?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="options-tab" data-bs-toggle="tab" href="#table" role="tab"><?=$lang[497]?></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#action" role="tab"><?=$lang[483]?></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#report" role="tab"><?=$lang[2277]?></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#formular" role="tab"><?=$lang[2745]?></a>
    </li>
</ul>





<div class="tab-content border border-top-0 bg-contrast">




<div class="tab-pane show active" id="table" role="tabpanel">

<table class="table table-sm table-striped table-hover mb-0">

<thead>
<tr>
<th></th>
<th colspan="3"><?=$userdat["bezeichnung"][$user_id]?></th>
<th></th>
<th colspan="3"><?=$userdat["bezeichnung"][$compare_id]?></th>
</tr>
</thead>

<?php foreach($fieldRuleUser1 as $tabid => $value){

    // check if table permission is different
    $diff_tables = array_diff_assoc($tableRuleUser1[$tabid][0], $tableRuleUser2[$tabid][0]);

    // list of field permissions
    foreach($value[0] as $fieldid => $perm) {

        // check if difference in field permissions
        if(!is_array($fieldRuleUser1[$tabid][0][$fieldid]) OR !is_array($fieldRuleUser2[$tabid][0][$fieldid])){
            continue;
        }

        // check if field permission is different
        $diff_fields = array_diff_assoc($fieldRuleUser1[$tabid][0][$fieldid], $fieldRuleUser2[$tabid][0][$fieldid]);

        // continue if table & fields have same permissions
        if(!$diff_fields && !$diff_tables){continue;}?>


        <?php
        // table header

        if($tablename != $gtab['table'][$tabid]){
            $tablename = $gtab['table'][$tabid];
            ?>
            <thead>
            <tr class="table-primary">
                <th><?=$gtab['table'][$tabid]?></th>
                <th colspan="3">
                <?php
                // table permissions user1
                foreach($tablePermType as $permName => $icon) { ?>
                    <?php if($tableRuleUser1[$tabid][0][$permName]){
                        $title = "<em><u> ".$icon[1]." </u></em><br>".implode("\n",array_map('getGroupName',$tableRuleUser1[$tabid][1][$permName]));
                        ?>
                        <i class="<?=$icon[0]?> text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
                    <?php } else {?>
                        <i class="<?=$icon[0]?> link-danger" title="<?=$icon[1]?>"></i>
                    <?php } ?>
                <?php } ?>

                <th></th>
                <th colspan="3">
                <?php
                // table permissions user2
                foreach($tablePermType as $permName => $icon) { ?>
                    <?php if($tableRuleUser2[$tabid][0][$permName]){
                        $title = "<em><u> ".$icon[1]." </u></em><br>".implode("\n",array_map('getGroupName',$tableRuleUser2[$tabid][1][$permName]));
                        ?>
                        <i class="<?=$icon[0]?> text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
                    <?php } else {?>
                        <i class="<?=$icon[0]?> link-danger" title="<?=$icon[1]?>"></i>
                    <?php } ?>
                <?php } ?>

                </th>
           </tr>
           </thead>

            <?php
            // continue if only fields have same permissions
            if(!$diff_fields){continue;}
            ?>


            <?php // fields header ?>

            <thead>
            <tr>
                <th></th>
                <th><i class="lmb-icon lmb-eye" title="<?= $lang[2302] ?>"></i></th>
                <th><i class="lmb-icon lmb-pencil" title="<?= $lang[1259] ?>"></i></th>
                <th><i class="lmb-icon lmb-copy" title="<?= $lang[1464] ?>"></i></th>
                <th></th>
                <th><i class="lmb-icon lmb-eye" title="<?= $lang[2302] ?>"></i></th>
                <th><i class="lmb-icon lmb-pencil" title="<?= $lang[1259] ?>"></i></th>
                <th><i class="lmb-icon lmb-copy" title="<?= $lang[1464] ?>"></i></th>
            </tr>
            </thead>

        <?php }
        $tablename = $gtab['table'][$tabid];
        // continue if only fields have same permissions
        if(!$diff_fields){continue;}
        ?>


        <?php // fields body ?>

        <tr>

        <td><div class="ps-3"><?=$gfield[$tabid]['field_name'][$fieldid]?></div></td>

        <?php foreach($fieldPermType as $key => $permName) { ?>

            <td>
            <?php if($fieldRuleUser1[$tabid][0][$fieldid][$permName]){
                $title = implode("\n",array_map('getGroupName',$fieldRuleUser1[$tabid][1][$fieldid][$permName]));
                ?>
                <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
            <?php } else {?>
                <i class="lmb-icon fas fa-times link-danger"></i>
                <?php } ?>
            </td>

        <? } ?>



        <td></td>



        <?php foreach($fieldPermType as $key => $permName) { ?>

            <td>
            <?php if($fieldRuleUser2[$tabid][0][$fieldid][$permName]){
                $title = implode("\n",array_map('getGroupName',$fieldRuleUser2[$tabid][1][$fieldid][$permName]));
                ?>
                <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
            <?php } else {?>
                <i class="lmb-icon fas fa-times link-danger"></i>
                <?php } ?>
            </td>

        <? } ?>

        </tr>

    <?php
    }

}


?>

</table>
</div>












<?php

require_once(COREPATH . 'admin/group/group_nutzrechte.dao');
$actionRuleUser1 = groupActionRule(explode(';',$userdat["subgroup"][$user_id]));
$actionRuleUser2 = groupActionRule(explode(';',$userdat["subgroup"][$compare_id]));
$link_groupdesc = groupActionGrouping();

?>

<div class="tab-pane" id="action" role="tabpanel">

    <table class="table table-sm table-striped table-hover border bg-contrast">
        <thead>
        <tr>
            <th></th>
            <th><?=$lang[435]?></th>
            <th><?=$userdat['bezeichnung'][$user_id]?></th>
            <th><?=$userdat['bezeichnung'][$compare_id]?></th>
        </tr>
        </thead>
    
        <?php foreach($link_groupdesc as $key => $value){
            foreach($link_groupdesc[$key] as $key2 => $value2){
                foreach($actionRuleUser1["sort"] as $bzm => $value0){
                    if($actionRuleUser1["maingroup"][$bzm] == $key AND $actionRuleUser1["subgroup"][$bzm] == $key2){

                        // check if permission is different
                        if($actionRuleUser1['perm'][$bzm] == $actionRuleUser2['perm'][$bzm]) {
                            continue;
                        }

                        if($actionRuleUser1["subgroup"][$bzm] != $tmpsubg){?>
                            <thead>
                            <tr class="table-primary"><th colspan="4"><?=$link_groupdesc[$key][$actionRuleUser1["subgroup"][$bzm]]?></th></tr>
                            </thead>
                        <?php }
                        $tmpsubg = $actionRuleUser1["subgroup"][$bzm];

                        ?>
                            <td>
                                <?php if($actionRuleUser1["icon_url"][$bzm]): ?>
                                <i class="lmb-icon <?=$actionRuleUser1["icon_url"][$bzm]?>"></i>
                                <?php endif; ?>
                            </td>
                            <td><?=$lang[$actionRuleUser1["name"][$bzm]]?></td>
                            <td>
                                <?php if($actionRuleUser1["perm"][$bzm] == 2){
                                    $title = implode("\n",array_map('getGroupName',$actionRuleUser1["grouplist"][$bzm]));
                                    ?>
                                    <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
                                <?php }else{ ?>
                                    <i class="lmb-icon fas fa-times link-danger"></i>
                                <?php }; ?>
                            </td>
                            <td>
                                <?php if($actionRuleUser2["perm"][$bzm] == 2){
                                    $title = implode("\n",array_map('getGroupName',$actionRuleUser2["grouplist"][$bzm]));
                                    ?>
                                    <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
                                <?php }else{ ?>
                                    <i class="lmb-icon fas fa-times link-danger"></i>
                                <?php }; ?>
                            </td>
                        </tr>
                    <?php }
                }
            }
        }
        ?>
    
    </table>

</div>








<?php
$reportRuleUser1 = getUserReportPerms($user_id);
$reportRuleUser2 = getUserReportPerms($compare_id);

#error_log(print_r($reportRuleUser2,1));

?>

<div class="tab-pane" id="report" role="tabpanel">
<table class="table table-sm table-striped table-hover border bg-contrast">
<thead>
<tr>
    <th></th>
    <th colspan='3'><?=$userdat['bezeichnung'][$user_id]?></th>
    <th></th>
    <th colspan='3'><?=$userdat['bezeichnung'][$compare_id]?></th>
</tr>
</thead>
<?php
foreach($reportRuleUser1 as $tabid => $value){

    // list of field permissions
    foreach($value[0] as $reportid => $perm) {

        // check if difference is array
        if(!is_array($reportRuleUser1[$tabid][0][$reportid]) OR !is_array($reportRuleUser2[$tabid][0][$reportid])){
            continue;
        }

        // check if report permission is different
        $diff = array_diff_assoc($reportRuleUser1[$tabid][0][$reportid], $reportRuleUser2[$tabid][0][$reportid]);

        // continue if report have same permissions
        if(!$diff){continue;}?>


        <?php
        // table header

        if($tablename != $gtab['table'][$tabid]){
            $tablename = $gtab['table'][$tabid];
            ?>
            <thead>
            <tr class="table-primary">
                <th><?=$gtab['table'][$tabid]?></th>
                <th><i class="lmb-icon lmb-eye" title="<?= $lang[2303] ?>"></i></th>
                <th><i class="lmb-icon lmb-eye-slash" title="<?= $lang[2302] ?>"></i></th>
                <th><i class="lmb-icon lmb-pencil" title="<?= $lang[1259] ?>"></i></th>
                <th></th>
                <th><i class="lmb-icon lmb-eye" title="<?= $lang[2303] ?>"></i></th>
                <th><i class="lmb-icon lmb-eye-slash" title="<?= $lang[2302] ?>"></i></th>
                <th><i class="lmb-icon lmb-pencil" title="<?= $lang[1259] ?>"></i></th>
            </tr>
            </thead>
        <?php }
        $tablename = $gtab['table'][$tabid];
        ?>


        <?php // fields body ?>

        <tr>

        <td><div class="ps-3"><?=$greportlist[$tabid]["name"][$reportid] ?></div></td>

        <?php foreach($reportPermType as $key => $permName) { ?>

            <td>
            <?php if($reportRuleUser1[$tabid][0][$reportid][$permName]){
                $title = implode("\n",array_map('getGroupName',$reportRuleUser1[$tabid][1][$reportid][$permName]));
                ?>
                <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
            <?php } else {?>
                <i class="lmb-icon fas fa-times link-danger"></i>
                <?php } ?>
            </td>

        <? } ?>

        <td></td>

        <?php foreach($reportPermType as $key => $permName) { ?>

            <td>
            <?php if($reportRuleUser2[$tabid][0][$reportid][$permName]){
                #error_log($tabid.' - '.$reportid.' - '.$permName);
                #error_log(print_r($reportRuleUser2[$tabid][1][$reportid][$permName],1));
                $title = implode("\n",array_map('getGroupName',$reportRuleUser2[$tabid][1][$reportid][$permName]));
                ?>
                <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
            <?php } else {?>
                <i class="lmb-icon fas fa-times link-danger"></i>
                <?php } ?>
            </td>

        <? } ?>

        </tr>
    <?php
    }

}
?>

</table>
</div>





    
    
    
    
<?php
$formRuleUser1 = getUserformPerms($user_id);
$formRuleUser2 = getUserformPerms($compare_id);

?>

<div class="tab-pane" id="formular" role="tabpanel">
<table class="table table-sm table-striped table-hover border bg-contrast">
<thead>
<tr>
    <th></th>
    <th colspan='3'><?=$userdat['bezeichnung'][$user_id]?></th>
    <th></th>
    <th colspan='3'><?=$userdat['bezeichnung'][$compare_id]?></th>
</tr>
</thead>
<?php
foreach($formRuleUser1 as $tabid => $value){

    // list of field permissions
    foreach($value[0] as $formid => $perm) {

        // check if difference is array
        if(!is_array($formRuleUser1[$tabid][0][$formid]) OR !is_array($formRuleUser2[$tabid][0][$formid])){
            continue;
        }

        // check if form permission is different
        $diff = array_diff_assoc($formRuleUser1[$tabid][0][$formid], $formRuleUser2[$tabid][0][$formid]);

        // continue if form have same permissions
        if(!$diff){continue;}?>


        <?php
        // table header

        if($tablename != $gtab['table'][$tabid]){
            $tablename = $gtab['table'][$tabid];
            ?>
            <thead>
            <tr class="table-primary">
                <th><?=$gtab['table'][$tabid]?></th>
                <th><i class="lmb-icon lmb-eye cursor-help" title="<?= $lang[2303] ?>"></i></th>
                <th><i class="lmb-icon lmb-eye-slash" title="<?= $lang[2302] ?>"></i></th>
                <th></th>
                <th><i class="lmb-icon lmb-eye cursor-help" title="<?= $lang[2303] ?>"></i></th>
                <th><i class="lmb-icon lmb-eye-slash cursor-help" title="<?= $lang[2302] ?>"></i></th>
            </tr>
            </thead>
        <?php }
        $tablename = $gtab['table'][$tabid];
        ?>

        <?php // fields body ?>

        <tr>

        <td><div class="ps-3"><?=$gformlist[$tabid]["name"][$formid] ?></div></td>

        <?php foreach($formPermType as $key => $permName) { ?>

            <td>
            <?php if($formRuleUser1[$tabid][0][$formid][$permName]){
                $title = implode("",array_map('getGroupName',$formRuleUser1[$tabid][1][$formid][$permName]));
                ?>
                <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
            <?php } else {?>
                <i class="lmb-icon fas fa-times link-danger"></i>
                <?php } ?>
            </td>

        <? } ?>

        <td></td>

        <?php foreach($formPermType as $key => $permName) { ?>

            <td>
            <?php if($formRuleUser2[$tabid][0][$formid][$permName]){
                $title = implode("",array_map('getGroupName',$formRuleUser2[$tabid][1][$formid][$permName]));
                ?>
                <i class="lmb-icon fa-solid fa-circle-check text-success" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover" title="<?=$title?>"></i>
            <?php } else {?>
                <i class="lmb-icon fas fa-times link-danger"></i>
                <?php } ?>
            </td>

        <? } ?>

        </tr>
    <?php
    }

}
?>

</table>
</div>
    





</div>

<?php

#error_log(print_r($user1,1));
#error_log(print_r($user2,1));



