<?php
global $sort_col, $sort_desc;

foreach (array("subject", "from", "size", "date") as $col)
	if ($col!=$sort_col){
		$this->add("desc_$col", 1);
		$this->add("dir_$col", 'aufsteigend');
	}

if ($sort_col){
	$this->add("desc_$sort_col", $sort_desc ? 1 : 0);
	$this->add("dir_$sort_col", $sort_desc ? 'aufsteigend' : 'absteigend');
}
?>
<!--http://tjvantoll.com/2012/11/10/creating-cross-browser-scrollable-tbody/-->
<!-- [list.tpl] -->
<table id="${mbox}" class="mail_list" border="0" cellspacing="0" cellpadding="2"> <!--class fixed_headers-->
    <thead>
<tr>
    <td class="gtabHeaderInputTD">
        <input class="gtabHeaderInputINP " onkeyup="mail_search_column(event,this)" type="text" maxlength="14" value=""  style="width:100%;">
    </td>
	<td class="gtabHeaderInputTD">
        <input class="gtabHeaderInputINP " onkeyup="mail_search_column(event,this)" type="text" maxlength="14" value=""  style="width:100%;">
    </td>
    <td class="gtabHeaderInputTD">
        <input class="gtabHeaderInputINP " onkeyup="mail_search_column(event,this)" type="text" maxlength="14" value=""  style="width:100%;">
    </td>
    <td class="gtabHeaderInputTD">
        <input class="gtabHeaderInputINP " onkeyup="mail_search_column(event,this)" type="text" maxlength="14" value=""  style="width:100%;">
    </td>
</tr>
<tr>
	<!--<th class="mail_list">&nbsp;</th>-->
	<th align="left" class="gtabHeaderTitleTD"
		onclick="mail_sortlist('${mbox}', 'subject', ${desc_subject})"
		title="nach Betreff sortieren (${dir_subject})"><div class="gtabHeaderTitleTDItem">Betreff</div></a></th>
	<th align="left" class="gtabHeaderTitleTD"
		onclick="mail_sortlist('${mbox}', 'from', ${desc_from})"
                title="nach Absender sortieren (${dir_from})"><div class="gtabHeaderTitleTDItem">Absender</div></a></th>
	<th align="right" class="gtabHeaderTitleTD"
		onclick="mail_sortlist('${mbox}', 'size', ${desc_size})"
                title="nach Größe sortieren (${dir_size})"><div class="gtabHeaderTitleTDItem">Größe</div></a></th>
	<th align="right" class="gtabHeaderTitleTD"
		onclick="mail_sortlist('${mbox}', 'date', ${desc_date})"
                title="nach Datum sortieren (${dir_date})"><div class="gtabHeaderTitleTDItem">Datum</div></a></th>
</tr>
</thead>
<tbody style="overflow-y: scroll">
${rows}
</tbody>
</table>
<!-- [/list.tpl] -->
