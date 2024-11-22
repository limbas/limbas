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

function multitenant_delete(mid,name){

    $('#MultitenantDeleteModal').modal('show');
    $('#MultitenantDeleteModalTitle').html('<?=$lang[2287]?> ('+name+')');

    buttonMultitenantDelete = document.getElementById("buttonMultitenantDelete");
    buttonMultitenantDeleteData = document.getElementById("buttonMultitenantDeleteData");
    buttonMultitenantDeleteAll = document.getElementById("buttonMultitenantDeleteAll");

    buttonMultitenantDelete.replaceWith(buttonMultitenantDelete.cloneNode(true));
    buttonMultitenantDeleteData.replaceWith(buttonMultitenantDeleteData.cloneNode(true));
    buttonMultitenantDeleteAll.replaceWith(buttonMultitenantDeleteAll.cloneNode(true));

    buttonMultitenantDelete = document.getElementById("buttonMultitenantDelete");
    buttonMultitenantDeleteData = document.getElementById("buttonMultitenantDeleteData");
    buttonMultitenantDeleteAll = document.getElementById("buttonMultitenantDeleteAll");

    buttonMultitenantDelete.addEventListener("click", function() {
        limbasWaitsymbol(null,1,0);
        document.form1.drop_multitenant.value=mid;
        document.form1.drop_multitenant_level.value=1;
        document.form1.submit();
    });

    buttonMultitenantDeleteData.addEventListener("click", function() {
        limbasWaitsymbol(null,1,0);
        document.form1.drop_multitenant.value=mid;
        document.form1.drop_multitenant_level.value=2;
        document.form1.submit();
    });

    buttonMultitenantDeleteAll.addEventListener("click", function() {
        limbasWaitsymbol(null,1,0);
        document.form1.drop_multitenant.value=mid;
        document.form1.drop_multitenant_level.value=3;
        document.form1.submit();
    });

}

function multitenant_info(mid) {
    limbasWaitsymbol(null,1,0);
    document.form1.info_multitenant.value=mid;
    document.form1.submit();
}

</script>


<div class="modal fade" id="MultitenantDeleteModal" tabindex="-1">
  <div class="modal-dialog  modal-lg" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="MultitenantDeleteModalTitle"><?=$lang[3105]?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-warning" id="buttonMultitenantDelete"><?=$lang[3105]?></button>
          <button type="button" class="btn btn-danger" id="buttonMultitenantDeleteData"><?=$lang[3107]?></button>
          <button type="button" class="btn btn-danger" id="buttonMultitenantDeleteAll"><?=$lang[3106]?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[844]?></button>
      </div>
    </div>
  </div>
</div>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_multitenant">
<input type="hidden" name="drop_multitenant">
<input type="hidden" name="drop_multitenant_level">
<input type="hidden" name="info_multitenant">

    <div class="container-fluid p-3">

        <table class="table table-sm table-striped mb-0 border bg-contrast">
            <thead>
                <tr>
                    <th><i class="lmb-icon  lmb-info-circle-alt2" style="cursor:pointer;" onclick="multitenant_info()"></i></th>
                    <th><?=$lang[2235]?></th>
                    <th><?=$lang[4]?></th>
                    <th>ID</th>
                    <?php if ($lmb_sync_clients): ?>
                        <th><?=$lang[2916]?></th>
                    <?php endif; ?>
                    <?php if ($result_crypto_keys): ?>
                        <th>Public-Key</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
            <?php if($result_multitenant) :
                foreach($result_multitenant['name'] as $mkey => $mval) : ?>
                    
                <tr>
                    <TD>
                        <i class="lmb-icon lmb-trash" style="cursor:pointer;" onclick="multitenant_delete(<?=$mkey?>,'<?=$result_multitenant['name'][$mkey]?>')"></i>
                        <i class="lmb-icon  lmb-info-circle-alt2" style="cursor:pointer;" onclick="multitenant_info(<?=$mkey?>)"></i>
                    </TD>
                    <TD><input type="text" class="form-control form-control-sm" value="<?=$result_multitenant['mid'][$mkey]?>" name="multitenant[mid][<?=$mkey?>]" onchange="document.getElementById('multitenant_<?=$mkey?>').value=1"></TD>
                    <TD><input type="text" class="form-control form-control-sm" value="<?=$result_multitenant['name'][$mkey]?>" name="multitenant[name][<?=$mkey?>]" onchange="document.getElementById('multitenant_<?=$mkey?>').value=1"></TD>
                    <TD><?=$mkey?></TD>
                    <?php if($lmb_sync_clients) : ?>
                    <td>
                        <select name="multitenant[syncslave][<?=$mkey?>]" onchange="document.getElementById('multitenant_$mkey').value=1" class="form-select form-select-sm">
                            <option value="0"></option>

                        <?php foreach($lmb_sync_clients as $skey => $svalue) : ?>
                            <option value="<?=$skey?>" <?=($result_multitenant['syncslave'][$mkey] == $skey)?'selected':''?>><?=$svalue?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <?php endif; ?>

                    <?php if($result_crypto_keys) : ?>
                        <td>
                            <select name="multitenant[crypto_key][<?=$mkey?>]" onchange="document.getElementById('multitenant_$mkey').value=1" class="form-select form-select-sm">
                                <option value="0"></option>

                                <?php foreach($result_crypto_keys as $skey => $svalue) : ?>
                                    <option value="<?=$skey?>" <?=($result_multitenant['crypto_key'][$mkey] == $skey)?'selected':''?>><?=$svalue?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    <?php endif; ?>
                    <td></td>
                    <input type="hidden" value="" name="multitenant[edit][<?=$mkey?>]" id="multitenant_<?=$mkey?>">
                </tr>
            <?php
                endforeach;
            endif;
            ?>
            </tbody>


                <tfoot>
                <tr class="border-bottom border-top">
                    <td colspan="6">
                        <button class="btn btn-sm btn-primary" type="submit" name="edit_multitenant" value="1"><?= $lang[522] ?></button>
                    </td>
                </tr>



                <TR>
                    <td></td>
                    <TD><input type="TEXT" name="new_multitenant_id" class="form-control form-control-sm"></TD>
                    <TD><input type="TEXT" name="new_multitenant_name" class="form-control form-control-sm"></TD>
                    <?php if($lmb_sync_clients) : ?>
                        <td></td><td>
                        <select name="new_multitenant_syncslave" class="form-select form-select-sm">
                            <option value="0"></option>
                        <?php foreach($lmb_sync_clients as $skey => $svalue) : ?>
                            <option value="<?=$skey?>"><?=$svalue?></option>
                        <?php endforeach; ?>
                        </select>
                        </td>
                    <?php endif; ?>
                    <?php if($result_crypto_keys) : ?>
                        <td></td>
                    <?php endif; ?>
                    <td><button class="btn btn-sm btn-primary" type="submit" name="add_multitenant" value="1"><?= $lang[540] ?></button></td>
                </TR>

                </tfoot>




        </table>

    </div>
    
    
    
</FORM>
