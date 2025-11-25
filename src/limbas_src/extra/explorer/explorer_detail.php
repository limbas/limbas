<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


global $farbschema, $ID, $umgvar, $level, $lang, $gtab, $gformlist, $gfield;

# include extensions
if($GLOBALS["gLmbExt"]["ext_explorer_detail.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_explorer_detail.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

?>
<script src="assets/js/extra/printer/printer.js?v=<?=$umgvar['version']?>"></script>

<div class="container-fluid" id="explorer-detail-container">
    <?php
/*<div class="border bg-contrast w-100 mb-2"><?=$file_url?></div>*/
 if($ffile["vact"]){$vidnr = "<SPAN STYLE=\"color:green;\"><b>".$ffile["vid"]."</b>";}else{$vidnr = "<SPAN STYLE=\"color:red;\"><b>".$ffile["vid"]."</b>";}?>

<ul class="nav nav-tabs" id="explorer-detail-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?=empty($show_part) ? 'active' : ''?>" id="menu_format" data-bs-toggle="tab" data-bs-target="#format" type="button" role="tab" aria-controls="format" aria-selected="<?=empty($show_part) ? 'true' : 'false'?>>"><?=$lang[1634]?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?=$show_part == 'metadata' ? 'active' : ''?>" id="menu_metadata" data-bs-toggle="tab" data-bs-target="#metadata" type="button" role="tab" aria-controls="metadata" aria-selected="<?=$show_part == 'metadata' ? 'true' : 'false'?>"><?=$lang[1635]?>&nbsp;</button>
    </li>

    <?php if($vfile['count']){?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?=$show_part == 'versioning' ? 'active' : ''?>" id="menu_versioning" data-bs-toggle="tab" data-bs-target="#versioning" type="button" role="tab" aria-controls="versioning" aria-selected="<?=$show_part == 'versioning' ? 'true' : 'false'?>"><?=$lang[2]?>&nbsp;<?=$vidnr?>&nbsp;</button>
    </li> <?php }?>

    <?php if($dfile["id"]){?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?=$show_part == 'duplicates' ? 'active' : ''?>" id="menu_duplicates" data-bs-toggle="tab" data-bs-target="#duplicates" type="button" role="tab" aria-controls="duplicates" aria-selected="<?=$show_part == 'duplicates' ? 'true' : 'false'?>"><?=$lang[1685]?>&nbsp;<span style="color:Green">(<?=lmb_count($dfile["id"])?>)&nbsp;</span></button>
    </li> <?php }?>

    <?php if($forigin OR $ffile["d_tabid"]){?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?=$show_part == 'origin' ? 'active' : ''?>" id="menu_origin" data-bs-toggle="tab" data-bs-target="#origin" type="button" role="tab" aria-controls="origin" aria-selected="<?=$show_part == 'origin' ? 'true' : 'false'?>"><?=$lang[2236]?>&nbsp;</button>
    </li> <?php }?>

    <?php if($exifdata){?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?=$show_part == 'exifdata' ? 'active' : ''?>" id="menu_exifdata" data-bs-toggle="tab" data-bs-target="#exifdata" type="button" role="tab" aria-controls="exifdata" aria-selected="<?=$show_part == 'exifdata' ? 'true' : 'false'?>"><?=$lang[1737]?>&nbsp;</button>
    </li> <?php }?>
</ul>


<div class="tab-content bg-contrast ps-2 border-start border-bottom border-end" id="myTabContent">
    <div class="tab-pane <?=empty($show_part) ? 'show active' : ''?>" id="format" role="tabpanel" aria-labelledby="format-tab">
        <div class="row">
        <div class="col-6">
            <dl class="row">
                <?php //# --- Allgemein --- ?>
                <h5 class="mt-4"><?=$lang[1634]?></h5>
                <dt class="col-3"><?=$lang[4]?></dt><dd class="col-9"><A HREF="main.php?action=download&ID=$ID" TARGET="new"><?=htmlentities($ffile["name"],ENT_QUOTES,$umgvar["charset"])?></A></dd>
                <dt class="col-3"><?=$lang[1638]?></dt><dd class="col-9"><?=$ffile['erstuser']?></dd>
                <dt class="col-3"><?=$lang[1639]?></dt><dd class="col-9"><?=$ffile['datum']?></dd>
                <dt class="col-3"><?=$lang[210]?></dt><dd class="col-9"><?=$ffile['size']?></dd>


                <?php # --- Format --- ?>
                <h5 class="mt-4"><?=$lang[1667]?></h5>
                <?php
                if($ffile['mimetype']) {
                    echo "<dt class=\"col-3\">$lang[1637]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["mimetype"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }
                if($ffile['format']) {
                    echo "<dt class=\"col-3\">$lang[1563]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["format"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }
                if($ffile['geometry']) {
                    echo "<dt class=\"col-3\">$lang[1564]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["geometry"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }
                if($ffile['resolution']) {
                    echo "<dt class=\"col-3\">$lang[1565]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["resolution"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }
                if($ffile['depth']) {
                    echo "<dt class=\"col-3\">$lang[1566]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["depth"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }
                if($ffile['colors']) {
                    echo "<dt class=\"col-3\">$lang[1567]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["colors"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }
                if($ffile['type']) {
                    echo "<dt class=\"col-3\">$lang[623]</dt><dd class=\"col-7\">".nl2br(htmlentities($ffile["type"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]))."</dd>";
                }

                # --- Indizierung ---
                if($ffile["indize"]){
                    echo "<h5 class=\"mt-4\">".$lang[1720]."</h5>";
                    echo "<dt class=\"col-3\">$lang[1719]</dt><dd class=\"col-7\">".get_date($ffile['indize_time'],2)." (".round($ffile['indize_needtime'],2)."sec)<dd>";
                }


                ?>
            </dl>
        </div>
        <div class="col-6">
            <div class="text-end mt-2 me-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-cog"></i></button>
                    <div class="dropdown-menu p-2 bg-contrast">
                        <?php
                        ob_start();
                        include(COREPATH . 'extra/printer/html/options.php');
                        echo ob_get_clean(); ?>
                    </div>
                    <button type="button" id="dms-print" class="btn btn-outline-dark" data-file-action="print" data-text="<?=$lang[391]?>" onclick="explorerDetailPrintFile(<?=$ffile['id']?>)"><i class="fas fa-print me-2"></i><?=$lang[391]?></button>
                </div>

            </div>
            <?php
            # --- Vorschau ---
            $size = explode("x",$umgvar["thumbsize2"]);
            try {
                $img = lmb_getThumbnail(array($ID, $ffile["secname"], $ffile["mimeid"], $ffile["thumb_ok"], null, $ffile["mid"]), $size[0], $size[1], 1);
            } catch (ImagickException $e) {
                lmb_error_log('explorer_detail image preview', $e);
            }
            if($img){
                #$filename = $umgvar["upload_pfad"].$ffile["secname"].".".$ffile["ext"];
                $filename = lmb_getFilePath($ID,$level,$ffile["secname"],$ffile["ext"]);
                echo "<img class=\"border mt-2\" src=\"$img\">";
            } ?>
        </div>
        </div>
    </div>
    <div class="tab-pane <?=$show_part == 'metadata' ? 'show active' : ''?>" id="metadata" role="tabpanel" aria-labelledby="metadata-tab">
        <div class="ratio ratio-4x3">
            <iframe src="main.php?action=gtab_change&old_action=explorer&gtabid=<?=$gtab["argresult_id"]["LDMS_META"]?>&ID=<?=$ID?>" class="frame-fill"></iframe>
        </div>

        <?php
        /*
        $gtabid = $gtab["argresult_id"]["LDMS_META"];
        if($gtab["tab_view_form"][$gtabid]){
            $dimension = explode('x',$gformlist[$gtabid]["dimension"][$gtab["tab_view_form"][$gtabid]]);
            require_once(COREPATH . 'gtab/gtab_form.lib');
            $gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
            form_gresult($ID,$gtabid,$gtab["tab_view_form"][$gtabid],$gresult); # need for fields of related tables
            form_gresult($ID,$gtab["argresult_id"]["LDMS_FILES"],$gtab["tab_view_form"][$gtabid],$gresult); # need for fields of related tables
            echo '<div style="width:100%;position:relative;height:'.$dimension[1].'px;">';
            formListElements('gtab_change',$gtabid,$ID,$gresult,$gtab["tab_view_form"][$gtabid]);
            echo '</div>';
        }else{
            $gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
            echo "<table width=\"650px\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border-collapse:collapse;\">\n";
            defaultViewElements($gtabid,$ID,$gresult);
            echo "<tr><td></td><td><input class=\"submit\" type=\"button\" name=\"LmEx_changeMeta\" style=\"cursor:pointer\" value=\"$lang[33]\" onclick=\"document.form1.show_part.value='metadata';send_form('1');\"></td></tr>";
            echo "</table>";
        }*/

        ?>
    </div>
    <div class="tab-pane <?=$show_part == 'versioning' ? 'show active' : ''?>" id="versioning" role="tabpanel" aria-labelledby="versioning-tab">
        <?php if($vfile['count']) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>diff</th>
                    <th>version</th>
                    <th>upload date</th>
                    <th>upload user</th>
                    <th>note</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $maxvid = lmb_count($vfile['id']);
            $bzm=1;
            if($vfile['id'] AND lmb_count($vfile['id']) > 1){
                if($ffile['mimetype']){$meta = explode("/",$ffile['mimetype']);}

                foreach($vfile['id'] as $key => $value){
                    echo "<tr>";
                    if($value == $ffile['id']){$fstyle = "color:black;";}else{$fstyle = "color:grey;";}
                    if($vfile['nr'][$key] == $maxvid){$fdesc = " ($lang[2018])";}else{$fdesc = "";}
                    if($meta[0] != "image" AND  $meta[0] != "video" AND $meta[0] != "audio"){
                        echo "<td>";
                        if($maxvid != $bzm){
                            echo "&nbsp;<i class=\"lmb-icon lmb-file-code\" OnClick=\"limbasFileVersionDiff(this,$value,".$vfile['id'][$key+1].",1,'#versioning')\" STYLE=\"cursor:pointer;\"></i>";
                        }
                        echo "</td>";
                    }

                    if($value != $ffile['id']){echo "<A HREF=\"JavaScript:document.location.href='main.php?&action=explorer_detail&level=$level&LID=$level&ID=$value'\"></A>";}
                    echo "<td><SPAN STYLE=\"$fstyle\">".$lang[2]." ".$vfile['nr'][$key]."</SPAN></A> $fdesc</td>
                        <td>".$vfile['erstdatum'][$key]."</td><td>".$vfile['erstuser'][$key]."</td>
                        <td>".$vfile['vnote'][$key]."</td></tr>";

                    $bzm++;
                }
            }
            ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
    <div class="tab-pane <?=$show_part == 'duplicates' ? 'show active' : ''?>" id="duplicates" role="tabpanel" aria-labelledby="duplicates-tab">
        <table class="table">
                <?php
                if($dfile["id"]){
                    echo "<thead>
                            <tr>
                                <th>name</th>
                                <th>path</th>
                                <th>size</th>
                                <th>upload date</th>
                                <th>upload user</th>
                            </tr></thead><tbody>";
                    foreach ($dfile["id"] as $key => $value){
                        echo "<tr>
                                <td nowrap><A HREF=\"main.php?&action=download&ID=".$dfile["id"][$key]."\" TARGET=\"new\">".$dfile["name"][$key]."</A></td>
                                <td><I class='text-break'>"."/".lmb_getUrlFromLevel($dfile["level"][$key],0)."</I></A></td>
                                <td >".file_size($dfile["size"][$key])."</td>
                                <td>".get_date($dfile["erstdatum"][$key],1)."</td>
                                <td>".$userdat["vorname"][$dfile["erstuser"][$key]]." ".$userdat["name"][$dfile["erstuser"][$key]]."</td>
                              </tr>";
                    }
                    echo "</tbody>";
                }
                ?>
        </table>
    </div>
    <div class="tab-pane <?=$show_part == 'origin' ? 'show active' : ''?>" id="origin" role="tabpanel" aria-labelledby="origin-tab">

        <?php
        # Feldtyp Upload
        #if($ffile["d_tabid"]){
        #	echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\" COLSPAN=\"5\">".htmlentities($ffile["d_tab"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."&nbsp;&nbsp;(".htmlentities($ffile["d_field"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]).")</TD></TR>";
        #	echo "<TR class=\"tabBody\" OnClick=\"open_tab('".$ffile["d_tabid"]."','".$ffile["d_id"]."')\"><TD style=\"width:20px;\"></TD><TD class=\"link\" VALIGN=\"TOP\">".$ffile["d_id"]."</TD><TD></TD></TR>";
        #}

        # Verknüpfungen
        if($forigin){
            foreach($forigin as $key => $value){
                foreach($value as $key1 => $value1){
                    echo "<h5 class='pt-2'>".$gtab["desc"][$key].".".$forigin[$key][$key1]["field"]."</h5>";
                    echo "<table class='table'>";
                    echo "<thead><tr><th>id</th><th>".$lang[2235]."</th><th>".$lang[2111]."</th><th>".$lang[160]."</th></tr></thead>";
                    echo "<tbody>";

                    foreach($forigin[$key][$key1]["id"] as $key2 => $value2){
                        echo "<tr>
                            <td OnClick=\"lmEx_openDataset('".$key."','".$forigin[$key][$key1]["id"][$key2]."')\" class=\"link\">".$forigin[$key][$key1]["id"][$key2]."</td>
                            <td OnClick=\"lmEx_openDataset('".$key."','".$forigin[$key][$key1]["id"][$key2]."')\" class=\"link\">".$forigin[$key][$key1]["value"][$key2]."</td>
                            <td>".$forigin[$key][$key1]["folder"][$key2]."</td>";
                        if($gfield[$key]["perm_edit"][$key1]){echo "<td class=\"link\"><i class=\"lmb-icon lmb-trash\" border=0 OnClick=\"lmEx_dropRelation($ID,$level,'".$key."_".$key1."_".$forigin[$key][$key1]["id"][$key2]."')\"></i></td>";}
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
            }
        }
        ?>
    </div>
    <div class="tab-pane <?=$show_part == 'exifdata' ? 'show active' : ''?>" id="exifdata" role="tabpanel" aria-labelledby="exifdata-tab">
        <?php if($exifdata){?>
            <table class="table">
                <TR><TD><?= $exifdata ?></TD></TR>
            </table>
        <?php }?>
    </div>
</div>

</div>
