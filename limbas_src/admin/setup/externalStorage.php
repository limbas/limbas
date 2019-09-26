<style>
    #extStorageTable {
        width: 100%;
        border-spacing: 2px;
    }
    #extStorageTable td:nth-child(1) {
        width: 10px;
    }
    #extStorageTable td:nth-child(2) {
        width: 20px;
    }
    #extStorageTable td:nth-child(3) {
        width: 80px;
    }
    #extStorageTable td:nth-child(4) {
        width: 80px;
    }
    #extStorageTable td:nth-child(5) {
        min-width: 80px;
    }
    #extStorageTable td:nth-child(6) {
        width: 200px;
    }
    #extStorageTable td:nth-child(7) {
        width: 70px;
    }
    #extStorageTable td:nth-child(8) {
        width: 80px;
    }
    #extStorageTable input {
        width: 100%;
    }
    #extStorageTable .lmb-icon[onclick], input[type=submit] {
        cursor: pointer;
    }
    #extStorageTable td {
        vertical-align: top;
    }
    #extStorageTable tr:last-child td {
        vertical-align: bottom;
    }
    #extStorageTable select {
        width: 100%;
    }
    #extStorageTable label {
        display: inline-block;
    }
    #newConfig_OwnCloud label {
        width: 24%;
    }
    #newConfig_SeaFile label {
        width: 19%;
    }
</style>

<script>
    function delStorage(storageID) {
        if (confirm('<?= $lang[84] ?>')) {
            document.form1.deleteID.value = storageID;
            document.form1.submit();
        }
    }
    function changeStorage(storageID) {
        if (document.form1.changedIDs.value.indexOf(';' + storageID) === -1) {
            document.form1.changedIDs.value += ';' + storageID;
        }
    }
    function selectClass(className) {
        if (className === 'Extension') {
            $('#newClass').val('').show();
        } else {
            $('#newClass').val(className).hide();
        }
        var configDiv = $('#newConfig_' + className);
        configDiv.parent().children().hide();
        configDiv.show();
    }
    $(function() {
        selectClass('Extension');
    })
</script>

<?php
global $externalStorage;
global $lang;

echo '<div class="lmbPositionContainerMain">';
echo '<form action="main_admin.php" method="post" name="form1">';
echo '<input type="hidden" name="action" value="setup_external_storage">';
echo '<input type="hidden" name="deleteID">';
echo '<input type="hidden" name="changedIDs">';
echo '<table id="extStorageTable" class="tabfringe">';
echo '<tbody>';

# header
echo '<tr class="tabHeader">';
echo '<td nowrap class="tabHeaderItem">' . $lang[949] . '</td>';
echo '<td nowrap class="tabHeaderItem"></td>';
echo '<td nowrap class="tabHeaderItem">' . $lang[126] . '</td>';
echo '<td nowrap class="tabHeaderItem">' . /* TODO */ 'Classname' . '</td>';
echo '<td nowrap class="tabHeaderItem">' . /* TODO */ 'Config' . '</td>';
echo '<td nowrap class="tabHeaderItem">' . /* TODO */ 'External Access url' . '</td>';
echo '<td nowrap class="tabHeaderItem">' . /* TODO */ 'Public cloud' . '</td>';
echo '<td></td>';
echo '</tr>';

# body
foreach ($externalStorage['desc'] as $storageID => $storageDesc) {
    $onchange = "onchange=\"changeStorage($storageID)\"";
    echo '<tr class="tabBody">';
    echo "<td>{$storageID}</td>";
    echo "<td><i class=\"lmb-icon lmb-trash\" onclick=\"delStorage($storageID)\" title=\"{$lang[160]}\"></i></td>";
    echo "<td><input type=\"text\" $onchange name=\"desc_$storageID\" value=\"{$storageDesc}\"></td>";
    echo "<td><input type=\"text\" $onchange name=\"className_$storageID\" value=\"{$externalStorage['className'][$storageID]}\" title=\"Lmb_Cloud_{$externalStorage['className'][$storageID]}\"></td>";
    echo "<td><input type=\"text\" $onchange name=\"config_$storageID\" value='" . $externalStorage['config'][$storageID] . '\'></td>';
    echo "<td><input type=\"text\" $onchange name=\"externalAccessUrl_$storageID\" value=\"{$externalStorage['externalAccessUrl'][$storageID]}\"></td>";
    echo "<td><input type=\"checkbox\" $onchange name=\"publicCloud_$storageID\" " . (($externalStorage['publicCloud'][$storageID]) ? 'checked' : '') . "></td>";
    echo '<td></td>';
    echo '</tr>';
}

# footer change
echo '<tr><td colspan="8"><hr></td></tr>';
echo "<tr><td colspan=\"3\"><input  type=\"submit\" name=\"change\" value=\"{$lang[522]}\"></td></tr>";
echo '<tr><td colspan="8"><hr></td></tr>';



# footer new
echo '<tr class="tabBody">';
echo '<td></td>';
echo '<td></td>';
echo '<td><input type="text" name="newDesc"></td>';
echo '
    <td>
        <select onchange="selectClass(this.value);">
            <option value="Extension">Extension</option>
            <option value="OwnCloud">OwnCloud</option>
            <option value="SeaFile">SeaFile</option>
            <option value="Dropbox">Dropbox</option>
            <option value="Filesystem">Filesystem</option>
        </select>
        <br>
        <input type="text" id="newClass" name="newClass" style="margin-top: 3px;">
    </td>';
echo '
    <td>
        <div id="newConfig_Extension">
            <input type="text" name="newConfig">
        </div>
        <div id="newConfig_OwnCloud">
            <label>Url: <input type="text" name="newConfig_OwnCloud_Url"></label>
            <label>User: <input type="text" name="newConfig_OwnCloud_User"></label>
            <label>Pass: <input type="text" name="newConfig_OwnCloud_Pass"></label>
            <label>Folder: <input type="text" name="newConfig_OwnCloud_Folder"></label>
        </div>
        <div id="newConfig_SeaFile">
            <label>Url: <input type="text" name="newConfig_SeaFile_Url"></label>
            <label>User: <input type="text" name="newConfig_SeaFile_User"></label>
            <label>Pass: <input type="text" name="newConfig_SeaFile_Pass"></label>
            <label>Folder: <input type="text" name="newConfig_SeaFile_Folder"></label>
            <label>Repo: <input type="text" name="newConfig_SeaFile_Repo"></label>
        </div>
        <div id="newConfig_Dropbox">
            <label>Token: <input type="text" name="newConfig_Dropbox_Token"></label>
        </div>
        <div id="newConfig_Filesystem">
            <label>Path: <input type="text" name="newConfig_Filesystem_Path"></label>
        </div>
    </td>';
echo '<td><input type="text" name="newExternalAccessUrl"></td>';
echo '<td><input type="checkbox" name="newPublicCloud"></td>';
echo "<td><input type=\"submit\" name=\"newExternalStorage\" value=\"{$lang[540]}\"</td>";
echo '</tr>';

echo '</tbody>';
echo '</table>';
echo '</form>';
echo '</div>';