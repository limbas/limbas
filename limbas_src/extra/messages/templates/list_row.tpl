<!-- [list_row.tpl] -->
<tr id="${uid}" class="${tr_class}">
	<!--<td class="${td_class}" valign="top">
                <a class="mail" href="${_self}&amp;mbox=${mbox}&amp;del_uid=${uid}" onclick="mail_delete('${mbox}', ${uid}); return false"><i class="lmb-icon-cus lmb-email-del" title="Nachricht löschen" border="0"></i></a>
	</td>-->
	<td class="${td_class}" valign="top" style="min-width:200px;">
		<a class="mail" href="${_self}&amp;mbox=${mbox}&amp;uid=${uid}" onclick="return false">${subject}</a>
	</td>
	<td class="${td_class}" valign="top">
		<a class="mail" href="${_self}&amp;mbox=${mbox}&amp;uid=${uid}" onclick="return false">${from}</a>
	</td>
	<td class="${td_class}" valign="top" align="right">
		<a class="mail" href="${_self}&amp;mbox=${mbox}&amp;uid=${uid}" onclick="return false">${size}&nbsp;kB</a>
	</td>
	<td class="${td_class}" valign="top" align="right">
		<a class="mail" href="${_self}&amp;mbox=${mbox}&amp;uid=${uid}" onclick="return false">${date}</a>
	</td>
</tr>
<!-- [/list_row.tpl] -->
