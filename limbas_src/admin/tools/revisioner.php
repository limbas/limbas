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
 * ID:
 */
?>




<?php

$result_revisioner = array();

function lmb_get_revision()
{
    global $db;

    $sqlquery = "SELECT ID,ERSTDATUM,ERSTUSER,REVISION,VERSION,COREV,DESCRIPTION FROM LMB_REVISION ORDER BY ERSTDATUM DESC";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    while (lmbdb_fetch_row($rs)) {
        $key = lmbdb_result($rs, "ID");
        $result_revisioner['id'][$key] = $key;
        $result_revisioner['erstdatum'][$key] = lmbdb_result($rs, 'ERSTDATUM');
        $result_revisioner['erstuser'][$key] = lmbdb_result($rs, 'ERSTUSER');
        $result_revisioner['revision'][$key] = lmbdb_result($rs, 'REVISION');
        $result_revisioner['version'][$key] = lmbdb_result($rs, 'VERSION');
        $result_revisioner['core'][$key] = lmbdb_result($rs, 'COREV');
        $result_revisioner['desc'][$key] = lmbdb_result($rs, 'DESCRIPTION');

        if (!$result_revisioner['last_revision']) {
            $result_revisioner['last_revision'] = $result_revisioner['revision'][$key];
            $result_revisioner['last_version'] = $result_revisioner['version'][$key];
            $result_revisioner['last_date'] = $result_revisioner['erstdatum'][$key];
        }
    }

    return $result_revisioner;
}


if (!$revisioner_typ) {
    $revisioner_typ = 1;
}

$result_revisioner = lmb_get_revision();

if ($new_revision) {
    require_once("admin/tools/export.lib");
    lmb_create_revision($result_revisioner, $lmb_revision_nr, $lmb_revision_desc);
    $revisioner_typ = 2;
    $result_revisioner = lmb_get_revision();
}

function lmb_create_revision($last, $revision, $desc)
{
    global $db;
    global $umgvar;
    global $session;
    global $DBA;

    // clean user temp dir
    rmdirr($umgvar["pfad"] . "/USER/" . $session["user_id"] . "/temp/");

    if ($revision != $last['last_revision']) {
        $next['revision'] = ($last['last_revision'] + 1);
        $next['version'] = 1;
    } else {
        $next['revision'] = $last['last_revision'];
        $next['version'] = ($last['last_version'] + 1);
    }

    // copy EXTENSION source
    $path = $umgvar['path'] . '/BACKUP/revision/' . $next['revision'] . '_' . $next['version'];
    mkdir($path);

    if (!file_exists($path)) {
        lmb_alert("can not create directory ($path)");
        return false;
    }

    chdir($umgvar['path'] . '/EXTENSIONS');
    $cmd = 'tar cvfz ' . $path . '/EXTENSIONS.tar.gz *';
    $out = `$cmd`;

    // create revision dataset
    $NEXTID = next_db_id('LMB_REVISION');
    $sqlquery = "INSERT INTO LMB_REVISION(ID,ERSTDATUM,ERSTUSER,REVISION,VERSION,COREV,DESCRIPTION) values(" . $NEXTID . "," . LMB_DBDEF_TIMESTAMP . "," . $session["user_id"] . "," . parse_db_int($next['revision']) . "," . parse_db_int($next['version']) . ",'" . parse_db_string($umgvar["version"]) . "','" . parse_db_string($desc) . "')";
    lmbdb_exec($db, $sqlquery);

    /*$exptables = array();
    // Bestehenden Tabellen für Vergleich exportieren

    $odbc_table = dbf_20(array($DBA["DBSCHEMA"], null, "'TABLE'"));
    foreach ($odbc_table["table_name"] as $tkey => $table) {
        // no systemtables, system_files, ldms_* , dontinclude
        if (lmb_strpos(lmb_strtolower($table), 'lmb_') === false && lmb_strpos(lmb_strtolower($table), 'systemtables') === false && lmb_strpos(lmb_strtolower($table), 'system_files') === false && lmb_strpos(lmb_strtolower($table), 'ldms_') === false) {
            $exptables[] = $table;
        }
    }

    $exptables = array_merge($exptables, array(
        dbf_4('lmb_trigger'),
        dbf_4('lmb_form_list'),
        dbf_4('lmb_forms'),
        dbf_4('lmb_report_list'),
        dbf_4('lmb_reports')
    ));

    lmbExport_ToSystem($exptables, null, null, null, true);
    */

    lmbExport(null, 'sync', null, array('tabs', 'forms', 'rep'));

    // move export to BACKUP directory
    rename($umgvar["pfad"] . "/USER/" . $session["user_id"] . "/temp/sync_export_bundle.tar.gz", $path . '/schema.tar.gz');
    #chdir($umgvar['path'] . '/USER/' . $session['user_id'] . '/temp/');
    #$cmd = 'tar cvfz ' . $path . '/schema.tar.gz *';
    #$out = `$cmd`;

    // clean user temp dir
    rmdirr($umgvar["pfad"] . "/USER/" . $session["user_id"] . "/temp/");
}

function lmb_compare_revision($compare_revision1, $compare_version1, $compare_revision2=null, $compare_version2=null)
{
    global $umgvar;

    require_once('admin/tools/import_sync.php');

    # switch versions to right order
    if (($compare_revision2 && $compare_version2) && ($compare_revision1 > $compare_revision2 || ($compare_revision1 == $compare_revision2 && $compare_version1 > $compare_version2))) {
        $tmp = $compare_revision1;
        $compare_revision1 = $compare_revision2;
        $compare_revision2 = $tmp;
        $tmp = $compare_version1;
        $compare_version1 = $compare_version2;
        $compare_version2 = $tmp;
    }

    $other = ($compare_revision2 && $compare_version2) ? "version {$compare_revision2}.{$compare_version2}" : "current version";
    echo "<h3>Comparison of version {$compare_revision1}.{$compare_version1} with {$other}</h3>";

    # move revision to user folder
    $revisionToCompare1 = $umgvar['path'] . '/BACKUP/revision/' . $compare_revision1 . '_' . $compare_version1 . '/schema.tar.gz';
    $copyToCompare1 = $umgvar['path'] . '/BACKUP/revision/' . $compare_revision1 . '_' . $compare_version1 . '/schema2.tar.gz';
    copy($revisionToCompare1, $copyToCompare1);

    # 2nd to compare or null (compare with local)
    $copyToCompare2 = null;
    if ($compare_revision2 && $compare_version2) {
        $revisionToCompare2 = $umgvar['path'] . '/BACKUP/revision/' . $compare_revision2 . '_' . $compare_version2 . '/schema.tar.gz';
        $copyToCompare2 = $umgvar['path'] . '/BACKUP/revision/' . $compare_revision2 . '_' . $compare_version2 . '/schema2.tar.gz';
        copy($revisionToCompare2, $copyToCompare2);
    }

    return sync_import($copyToCompare2, true, false, $copyToCompare1, false, true);
}

?>


<Script language="JavaScript">

    function open_definition(id) {
        if (document.getElementById('definition_' + id).style.display == 'none') {
            document.getElementById('definition_' + id).style.display = '';
        } else {
            document.getElementById('definition_' + id).style.display = 'none';
        }
    }

    function delete_definition(id) {
        document.getElementById('revisioner_' + id).style.display = 'none';
        document.getElementById('definition_' + id).style.display = 'none';
        document.getElementById("definition_change_" + id).value = '2';
    }

    function selectToCompare(revision, version) {
        if (document.form1.compare_revision.value === "") {
            document.form1.compare_revision.value = revision;
            document.form1.compare_version.value = version;
            $('#compare_'+revision+'_'+version).css('background-color', 'green').css('color', 'white').removeAttr('onclick');
        } else {
            document.form1.compare_revision2.value = revision;
            document.form1.compare_version2.value = version;
            document.form1.submit();
        }
    }

</Script>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
    <input type="hidden" name="action" value="setup_revisioner">
    <input type="hidden" name="revisioner_typ" value="<?= $revisioner_typ ?>">
    <input type="hidden" name="compare_revision" value="">
    <input type="hidden" name="compare_version" value="">
    <input type="hidden" name="compare_revision2" value="">
    <input type="hidden" name="compare_version2" value="">

    <DIV class="lmbPositionContainerMainTabPool">

        <TABLE class="tabpool" BORDER="0" width="700" cellspacing="0"
               cellpadding="0">
            <TR>
                <TD>

                    <?php
                   if ($revisioner_typ == 1){
                    ?>
                    <TABLE BORDER="0" cellspacing="0" cellpadding="0">
                        <TR class="tabpoolItemTR">
                            <TD nowrap class="tabpoolItemActive">Revision<?= $lang[992450] ?></TD>
                            <TD nowrap class="tabpoolItemInactive"
                                OnClick="document.location.href='main_admin.php?action=setup_revisioner&revisioner_typ=2'">
                                Historie<?= $lang[992451] ?></TD>
                            <TD class="tabpoolItemSpace">&nbsp;</TD>
                        </TR>
                    </TABLE>

                </TD>
            </TR>

            <TR>
                <TD class="tabpoolfringe">

                    <TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%"
                           class="tabBody">
                        <tr>
                            <td valign="top">&nbsp;</td>
                        </tr>
                        <?php if ($result_revisioner['last_revision']) { ?>
                            <tr>
                            <td valign="top">Letzte Version</td>
                            <td valign="top">
                                <b><?= $result_revisioner['last_revision'] . '.' . $result_revisioner['last_version'] . '</b> (' . get_date($result_revisioner['last_date'], 1) . ')' ?>
                            </td>
                            </tr><?php } ?>
                        <tr>
                            <td colspan=2>
                                <hr>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">Revision</td>
                            <td valign="top"><select name="lmb_revision_nr">
                                    <option><?php if ($result_revisioner['last_revision']) {
                                            echo $result_revisioner['last_revision'];
                                        } else {
                                            echo 1;
                                        } ?>

                                    <option><?= ($result_revisioner['last_revision'] + 1) ?></select></td>
                        </tr>
                        <tr>
                            <td valign="top">Bemerkung</td>
                            <td valign="top"><textarea name="lmb_revision_desc"
                                                       style="width: 550px; height: 200px"></textarea></td>
                        </tr>

                        <tr>
                            <td></td>
                            <TD align="left"><input type="submit" NAME="new_revision"
                                                    value="Revision erstellen"></TD>
                        </TR>
                        <TR class="tabFooter">
                            <TD colspan="7"></TD>
                        </TR>
                    </TABLE>

                    <?php
                    }elseif ($revisioner_typ == 2){
                    ?>
                    <TABLE BORDER="0" cellspacing="0" cellpadding="0">
                        <TR class="tabpoolItemTR">
                            <TD nowrap class="tabpoolItemInactive"
                                OnClick="document.location.href='main_admin.php?action=setup_revisioner&revisioner_typ=1'">
                                Revision<?= $lang[992450] ?></TD>
                            <TD nowrap class="tabpoolItemActive">Historie<?= $lang[992451] ?></TD>
                            <TD class="tabpoolItemSpace">&nbsp;</TD>
                        </TR>
                    </TABLE>

                </TD>
            </TR>

            <TR>
                <TD class="tabpoolfringe">

                    <TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%"
                           class="tabBody" style="border-collapse: collapse;">

                        <TR class="tabHeader">
                            <TD class="tabHeaderItem">Datum<?= $lang[992267] ?></TD>
                            <TD style="width: 80px" class="tabHeaderItem">Revision<?= $lang[992270] ?></TD>
                            <TD style="width: 80px" class="tabHeaderItem">Version<?= $lang[992271] ?></TD>
                            <TD class="tabHeaderItem">Core<?= $lang[992271] ?></TD>
                        </TR>
                        <?php
                       if ($result_revisioner) {
                            foreach ($result_revisioner['id'] as $key => $value) {
                                $revision = $result_revisioner['revision'][$key];
                                $version = $result_revisioner['version'][$key];

                                echo "<tr class=\"gtabBodyTRColorA\">
                                    <td>" . get_date($result_revisioner['erstdatum'][$key], 1) . "</td>
                                    <td>" . $revision . "</td>
                                    <td>" . $version . "</td>
                                    <td>" . $result_revisioner['core'][$key] . "</td>
                                    <td>
                                        <a title=\"compare to current version\" onclick=\"document.form1.compare_revision.value='" . $revision . "';document.form1.compare_version.value='" . $version . "';document.form1.submit();\">
                                            <i class=\"lmb-icon lmb-compare-version\" style=\"cursor:pointer\" border=\"0\"></i>
                                        </a>
                                        <i title=\"select to compare with other version\" id=\"compare_{$revision}_{$version}\" onclick=\"selectToCompare({$revision}, {$version});\" class=\"lmb-icon lmb-compare-version2\" style=\"cursor:pointer\" border=\"0\"></i>                                        
                                    </td>";
                                echo "<tr class=\"gtabBodyTRColorB\"><td colspan=\"5\"><div style=\"overflow:auto;max-height:100px\"><i>" . str_replace(chr(10), '<br>', $result_revisioner['desc'][$key]) . "</i></div></td></tr>";
                                echo "<tr><td colspan=\"5\">&nbsp;</td></tr>";

                            }
                        }
                        ?>
                        <TR class=\"tabFooter\">
                            <TD colspan=\"7\"></TD>
                        </TR>
                    </TABLE>


                    <?php
                    }
                    ?>

                </td>
            </tr>
        </table>

        <?php

        # compare revisions
        if ($compare_revision && $compare_version) {
            echo '<div class="lmbPositionContainerMain">';
            if ($result = lmb_compare_revision($compare_revision, $compare_version, $compare_revision2, $compare_version2)) {
                if (!$result['success']) {
                    print_r($result['errLog']);
                }
            }
            echo '</div>';
        }
        ?>

    </div>
</FORM>
