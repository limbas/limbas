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

if ($BODYHEADER):
	# add icon
	if($LINK['icon_url'][$LINK_ID[$action]]){
		$BODYHEADER = "<i class=\"lmb-icon {$LINK["icon_url"][$LINK_ID[$action]]}\" style=\"vertical-align:middle;\"></i>&nbsp;&nbsp;" . $BODYHEADER;
	}

	# add help icon
	$helpIcon = '';
	if ($LINK['help_url'][$LINK_ID[$action]] && $LINK[18]) {
		$helpIcon = "<a href=\"{$LINK['help_url'][$LINK_ID[$action]]}\" target=\"new\" class=\"d-inline-block pt-2\"><i class=\"lmb-icon lmb-help\"></i></a>";
	}

	# replace non-breaking whitespaces
	$pageTitleText = str_replace(array("\xC2\xA0", "\xA0"), "", trim(strip_tags(html_entity_decode($BODYHEADER))));

	# insert custom page title
	$pageTitle = $umgvar['page_title'] ? sprintf($umgvar['page_title'], $pageTitleText) : $pageTitleText;
	?>

    <script type="text/javascript">
        $('#lmb-main-title',window.parent.document).html('<?= $BODYHEADER ?>');
        $('#lmb-helplink',window.parent.document).html('<?= $helpIcon ?>');
        window.parent.document.title = '<?= $pageTitle ?>';
    </script>

<?php elseif ($BODYHEADER != "0"): ?>

	<script type="text/javascript">
        $('#main_top_value',window.parent.document).html('');
	</script>

<?php endif; ?>