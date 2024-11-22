<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

if ($BODYHEADER || $HEADER):

    if($HEADER){
        $BODYHEADER = $HEADER;
    }

    # add icon
    if($HEADERICON){
        $BODYHEADER = "<i class=\"{$HEADERICON}\" style=\"vertical-align:middle;\"></i>&nbsp;&nbsp;" . $BODYHEADER;
    }else if($LINK['icon_url'][$LINK_ID[$action]]){
		$BODYHEADER = "<i class=\"lmb-icon {$LINK["icon_url"][$LINK_ID[$action]]}\" style=\"vertical-align:middle;\"></i>&nbsp;&nbsp;" . $BODYHEADER;
	}

	# add help icon
	$helpIcon = '';
	if ($LINK['help_url'][$LINK_ID[$action]] && $LINK[18]) {
		$helpIcon = "<a href=\"{$LINK['help_url'][$LINK_ID[$action]]}\" target=\"new\" class=\"d-inline-block\"><i class=\"lmb-icon lmb-help\"></i></a>";
	}

	# replace non-breaking whitespaces
	$pageTitleText = str_replace(array("\xC2\xA0", "\xA0"), "", trim(strip_tags(html_entity_decode($BODYHEADER))));

	# insert custom page title
	$pageTitle = $umgvar['page_title'] ? sprintf($umgvar['page_title'], $pageTitleText) : $pageTitleText;
	?>

    <script type="text/javascript">
        $('#lmb-main-title',window.parent.document).html('<?= $BODYHEADER ?>');
        $('#lmb-main-subtitle',window.parent.document).html('');
        $('#lmb-helplink',window.parent.document).html('<?= $helpIcon ?>');
        window.parent.document.title = '<?= $pageTitle ?>';
        parent.$('.form_tabmenu').remove(); // remove form tabmenu from mainmenu if exists
    </script>

<?php elseif ($BODYHEADER != "0"): ?>

	<script type="text/javascript">
        $('#main_top_value',window.parent.document).html('');
        parent.$('.form_tabmenu').remove(); // remove form tabmenu from mainmenu if exists
	</script>

<?php endif; ?>
