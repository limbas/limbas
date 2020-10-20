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
 * ID: 155
 */
?>
<!-- include codemirror with sql syntax highlighting and sql code completion -->
<script src="extern/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/mode/sql/sql.js?v=<?=$umgvar["version"]?>"></script>
<script src="extern/codemirror/addon/hint/show-hint.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" href="extern/codemirror/addon/hint/show-hint.css?v=<?=$umgvar["version"]?>">
<script src="extern/codemirror/addon/hint/sql-hint.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" href="extern/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
<style>
    .CodeMirror {
        border: 1px solid <?=$farbschema['WEB3']?>;
        width: 600px;
        height: 300px;
    }
</style>
<script src="extern/sqlFormatter/sql-formatter.min.js?v=<?=$umgvar["version"]?>"></script>

<div class="lmbPositionContainerMain">


<?php /* --- Tabellenliste ------------------------------- */
if(!$use_codemirror){$use_codemirror='true';}
?>

<form action="main_admin.php" method="post" name="form2">
    <input type="hidden" name="action" value="setup_tabtools">
    <input type="hidden" name="empty">
    <input type="hidden" name="delete">
    <input type="hidden" name="sqlFavoriteName">
    <input type="hidden" name="use_codemirror" value="<?=$use_codemirror?>">

    <table class="tabfringe" border="0" cellspacing="1" cellpadding="0">
        <tr class="tabHeader"><td class="tabHeaderItem" colspan="5"><b><?=$lang[577]?></b></td></tr>
        <tr class="tabBody">
            <td>
                <select name="table" style="width: 200px;">
                    <?php
                    $odbc_table = dbf_20(array($DBA['DBSCHEMA'], null, "'TABLE','VIEW'"));
                    foreach($odbc_table['table_name'] as $tkey => $tvalue) {
                        $domaintables['tablename'][] = $odbc_table['table_name'][$tkey];
                        $domaintables['owner'][] = $odbc_table['table_owner'][$tkey];
                        $domaintables['type'][] = $odbc_table['table_type'][$tkey];
                        if($table == $odbc_table['table_name'][$tkey]){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                        if(lmb_strtoupper($odbc_table['table_type'][$tkey]) == 'VIEW'){
                            $val = 'VIEW :: ' . $odbc_table['table_name'][$tkey];
                        }else{
                            $val = $odbc_table['table_name'][$tkey];
                        }
                        echo "<option value='{$odbc_table['table_name'][$tkey]}' $SELECTED>$val</option>";
                    }
                    ?>
                </select>
            </td>
            <td><input type="submit" value="info" name="info"></td>
            <td><input type="submit" value="<?=$lang[1061]?>" name="show"></td>
            <td><input type="button" onclick="if(confirm('<?=$lang[2153]?>')){ document.form2.empty.value=1; document.form2.submit(); }" value="<?=$lang[550]?>" style="color: orange"></td>
            <td><input type="button" onclick="if(confirm('<?=$lang[2287]?>')){ document.form2.delete.value='table'; document.form2.submit(); }" value="<?=$lang[160]?>" style="color: red"></td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>

        <tr class="tabHeader"><td class="tabHeaderItem" colspan="5"><b><?=$lang[1060]?></b></td></tr>
        <tr class="tabBody">
            <td>
                <select name="domaintable" style="width: 200px;">
                    <?php
                    foreach ($DBA['DOMAINTABLE'] as $key => $val){
                        echo "<option value='{$DBA['DOMAINSCHEMA'][$key]}.{$DBA['DOMAINTABLE'][$key]}'>{$DBA['DOMAINTABLE'][$key]}</option>";
                    }
                    ?>
                </select>
            </td>
            <td></td>
            <td><input type="submit" value="<?=$lang[1061]?>" name="showsys"></td>
            <td colspan="2"></td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>

        <?php if ($sqlFavorites) { ?>
        <tr class="tabHeader"><td class="tabHeaderItem" colspan="5"><b><?=$lang[2932]?></b></td></tr>
        <tr class="tabBody">
            <td>
                <select name="favorite" style="width: 200px;">
                    <?php
                    foreach ($sqlFavorites as $favoriteID => $favoriteName){
                        $selected = ($favorite == $favoriteID) ? 'selected' : '';
                        echo "<option value='$favoriteID' $selected>$favoriteName</option>";
                    }
                    ?>
                </select>
            </td>
            <td></td>
            <td><input type="submit" value="<?=$lang[1061]?>" name="showFavorite"></td>
            <td></td>
            <td><input type="button" onclick="if(confirm('<?=$lang[2287]?>')){ document.form2.delete.value='favorite'; document.form2.submit(); }" value="<?=$lang[160]?>"></td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <?php } ?>

        <tr class="tabBody"><td class="tabHeaderItem" colspan="5"><b>SQL-Query</b></td></tr>
        <tr class="tabBody">
            <td colspan="5">
                <textarea id="sqlvalue" name="sqlvalue" <?php if($use_codemirror != 'true'){echo "style=\"width:600px;height:300px;padding=5px;\"";}?> ><?= $sqlvalue ?></textarea>
                <?php if($use_codemirror == 'true'){?>
                <script language="JavaScript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("sqlvalue"), {
                        lineNumbers: true,
                        matchBrackets: true,
                        mode: "text/x-sql",
                        indentWithTabs: true,
                        smartIndent: true,
                        autofocus: true,
                        extraKeys: {
                            "Ctrl-Enter": function() {$("#sqlexec").trigger("click");},
                            "Ctrl-Space": "autocomplete"
                        }
                    });

                    /**
                     * Format sql only if content was changed (!clean) and formatted content differs current content
                     * This prevents the page jumps caused by scrolling the codemirror into view after changing the content
                     * @param checkClean whether to skip formatting if the content wasnt changed
                     */
                    function formatSQL(checkClean=true) {
                        if (checkClean && editor.doc.isClean())
                            return;
                        var oldValue = editor.getValue();
                        var newValue = sqlFormatter.format(oldValue, { indent: "    "});
                        if (oldValue.length !== newValue.length || oldValue !== newValue) {
                            editor.setValue(newValue);
                        }
                        editor.doc.markClean();
                    }
                    editor.on('blur', formatSQL);

                    $(function() {
                        formatSQL(false);
                    });
                </script>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <input id="sqlexec" type="submit" value="<?=$lang[1065]?>" name="sqlexec">
                <input type="button" value="<?=$lang[2218]?>" name="sqlFavorite" onclick="const name = window.prompt('Name:'); document.form2.sqlFavoriteName.value  = name; document.form2.submit();">
                &nbsp;&nbsp;&nbsp;&nbsp;format Code: <input type="checkbox" value="2" onclick="document.form2.use_codemirror.value=this.checked; document.form2.submit();" <?php if($use_codemirror == 'true'){echo 'checked';}?> >
                <div style="float: right">
                    <?=$lang[2770]?>:&nbsp;<input type="text" name="sqlexecnum" style="width: 50px" value="<?=$sqlexecnum?>">
                </div>
            </td>
        </tr>
        <tr><td class="tabFooter" colspan="5">&nbsp;</td></tr>
    </table>
</form>
<br>
<?php

echo $result;
if ($rssql) {
    echo ODBCResourceToHTML($rssql, 'cellpadding="2" cellspacing="0" style="border-collapse:collapse;"', 'style="border: 1px solid grey;"', $sqlexecnum);
} elseif (lmb_strtoupper(lmb_substr($sqlvalue,0,7)) == 'EXPLAIN') {
    $rs = lmbdb_exec($db,$sqlvalue);
    echo '<table style="border-collapse:collapse" cellpadding="3">';
    while($ra = lmbdb_fetch_array($rs)){
        
        if(!$bzm){
            foreach($ra as $col=>$value){
                echo "<td style=\"border:1px solid grey\"><b>$col</b></td>";
            }
        }
        
        echo '<tr>';
        foreach($ra as $col=>$value){
            echo "<td style=\"border:1px solid grey\">$value</td>";
        }
        echo '</tr>';
        $bzm++;
    }
    echo '</table>';
}

$zeit0 = gettime();

if ($show AND $table) {
    # show table
    echo "<h3>$table</h3><br>";
    $sqlquery = "SELECT * FROM $table";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    if ($rs) {
        echo ODBCResourceToHTML($rs, 'cellpadding="2" cellspacing="0" style="border-collapse:collapse;"', 'style="border: 1px solid grey;"', $sqlexecnum);
    }
} else if ($info AND $table) {
    # show info of table
    echo "<h3>$table</h3><br>";
    $rs = dbf_5(array($DBA['DBSCHEMA'], $table, null, 1));
    lmbdb_result_all($rs,'border=1 style="border-collapse: collapse;padding:3px"');
} else if ($showsys AND $domaintable) {
    # show system info
    echo "<h3>$domaintable</h3><br>";
    $sqlquery = "SELECT * FROM $domaintable";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    if ($rs) {
        echo ODBCResourceToHTML($rs, 'cellpadding="2" cellspacing="0" style="border-collapse:collapse;"', 'style="border: 1px solid grey;"', $sqlexecnum);
    }
} else if (!$table AND !$showsys) {
    # show all tables and views
    echo '<table border="0" cellspacing="1" cellpadding="0" width="500">';
    echo "<tr class=\"tabHeader\"><td>$lang[164]</td><td>$lang[1067]</td><td>$lang[1068]</td></tr>";
    $bzm = 0;
    while ($domaintables['tablename'][$bzm]) {
        echo '<TR><TD>' . $domaintables['tablename'][$bzm] . '</TD><TD>' . $domaintables['owner'][$bzm] . '</TD><TD>' . $domaintables['type'][$bzm] . '</TD></TR>';
        $bzm++;
    }
    echo '</table>';
}

$zeit = number_format(gettime() - $zeit0, 8, ',', '.');
echo "<br><br><span style='font-size: small; color: green'>finished execution!</span>&nbsp;($zeit sec.)";
?>
</div>





