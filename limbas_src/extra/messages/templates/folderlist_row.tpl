<!-- [folderlist_row.tpl] -->
<tr>
    <td  style="${style1}">
                    ${toggle}
                </td>
                <td style="width:20px">
                    <i id="folderpic_${n}" style="cursor:hand" class="lmb-icon lmb-folder-closed"></i>
                </td>
                <td class="mail"><a id="${mbox}" class="${class}" href="${_self}&amp;mbox=${mbox}" onclick="return mail_list('${mbox}')">${namepart}</a>
    </td>
</tr>
<tr id="_${n}" style="display:none">
    <td style="${style2}"></td>
    <td colspan='2'>${next}</td>
</tr>



<!-- [/folderlist_row.tpl] -->
