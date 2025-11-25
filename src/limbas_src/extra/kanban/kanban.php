<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if($gtab["tab_view_tform"][$gtabid]){
	$fformid = $gtab["tab_view_tform"][$gtabid];
}

#$gtabid = $params["gtabid"];
$lmb_kanban = new lmb_kanban($gtabid,$gsr);

# include extensions
if($GLOBALS["gLmbExt"]["ext_kanban.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_kanban.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

$kanban = $lmb_kanban->render();


$jscols = array();

for ($i=0;$i<$lmb_kanban->column_count;$i++)
{
    $jscols[] = '#kanban'.$i;
}

?>

<script type="text/javascript">
    var kanban_columns = '<?= implode(',',$jscols) ?>';
    
    <?php
    
echo "
jsvar['gtabid'] = {$gtabid};
jsvar['fformid'] = '{$fformid}';

";
    
if($form_id OR $form_id = $gformlist[$gtabid]["id"][$gtab["tab_view_tform"][$gtabid]]){
	echo "jsvar['formid'] = '".$form_id."';\n";
	echo "jsvar['form_dimension'] = '".$gformlist[$gtabid]["dimension"][$gtab["tab_view_tform"][$gtabid]]."';\n";
}


if ($gtab['params2'][$gtabid]['showactive']){
    echo "jsvar['kan_showactive'] = {$gtab['params2'][$gtabid]['showactive']};\n";
}
    ?>
</script>


<div class="p-3 d-flex flex-column">
<nav class="navbar navbar-expand-sm center-navigation bg-nav mb-3 lmbGtabmenu lmbGtabmenu-list lmbGtabmenu-table-20">
<div class="container-fluid">
    <a class="navbar-brand" href="#" onclick="kanban_reload()"><span nowrap="" class="lmbGtabTabmenuActive"><?= $gtab['desc'][$gtabid] ?></span></a>
</div>
</nav>

<div class="container-fluid p-0">
<?= $kanban ?>
</div>
</div>

<?php require(COREPATH  . 'gtab/html/contextmenus/gtab_filter.php'); ?>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<div id="limbasAjaxGtabContainer" class="ajax_container" style="padding:1px;position:absolute;display:none;" onclick="activ_menu=1;"></div>
<div id="lmbKanAjaxContainer" class="ajax_container" style="position:absolute;display:none;z-index:2003"></div>
<div id="lmbAjaxContextmenu" class="lmbContextMenu" style="position:absolute;display:none;z-index:2004;" OnClick="activ_menu=1;"></div>
<div id="lmbAjaxContextPaste" class="lmbContextMenu" style="position:absolute;display:none;z-index:2004;" OnClick="activ_menu=1;"></div>

<form action="main.php" method="post" name="form1" id ="form1" autocomplete="off">
<input type="hidden" id="cardID" name="ID">
<input type="hidden" name="history_fields">
<input type="hidden" name="action" value="<?=$action?>">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="history_search">
<input type="hidden" name="change_ok">
<input type="hidden" name="use_typ">
<input type="hidden" name="use_record">
<input type="hidden" name="snap_id">
<input type="hidden" name="gfrist">
<input type="hidden" name="verkn_relationpath" VALUE="<?=$verkn_relationpath;?>">
</form>
