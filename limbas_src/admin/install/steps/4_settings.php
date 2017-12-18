
<table class="table table-condensed">
    <thead>
        <tr>
            <th colspan="2">
                Settings
                <a href="http://limbas.org/wiki/Umgvar" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td style="vertical-align: middle;">Language:</td>
            <td><select class="form-control input-sm " name="setup_language">
                    <option value="1" <?= $setup_language == '1' ? 'selected' : ''?>>deutsch</option>
                    <option value="2" <?= $setup_language == '2' ? 'selected' : ''?>>english</option>
                </select>
            </td>
        </tr>
        <tr><td style="vertical-align: middle;">Dateformat:</td>
            <td><select class="form-control input-sm " name="setup_dateformat">
                    <option value="1" <?= $setup_dateformat == '1' ? 'selected' : ''?>>deutsch (dd-mm-yyyy)</option>
                    <option value="2" <?= $setup_dateformat == '2' ? 'selected' : ''?>>english (yyyy-mm-dd)</option>
                    <option value="3" <?= $setup_dateformat == '3' ? 'selected' : ''?>>us (mm-dd-yy)</option>
                </select>
            </td>
        </tr>
        <tr><td style="vertical-align: middle;">Charset:</td>
            <td><select class="form-control input-sm " name="setup_charset">
                    <option value="ISO-8859-1" <?= $setup_charset == 'ISO-8859-1' ? 'selected' : ''?>>LATIN1</option>
                    <option value="UTF-8" <?= $setup_charset == 'UTF-8' ? 'selected' : ''?>>UTF-8</option>
                </select>
            </td>
        </tr>
        <tr><td>Company:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_company ? $setup_company : 'your company' ?>" name="setup_company" size="50"></td></tr>
    </tbody>
</table>

<div>
    <input type="hidden" name="db_test" value="validate">
    <button type="button" class="btn btn-default" onclick="switchToStep('<?php $s=array_keys($steps); echo $s[3]?>')">Back</button>
    <button type="submit" class="btn btn-info pull-right" name="install" value="<?php $s=array_keys($steps); echo $s[5]?>">Next step</button>
</div>