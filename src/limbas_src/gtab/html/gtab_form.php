<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

if($gformlist[0]["extension"][$form_id] AND file_exists(EXTENSIONSPATH.$gformlist[0]["extension"][$form_id])){
	require_once(EXTENSIONSPATH.$gformlist[0]["extension"][$form_id]);
}else{
	if(!$gform[$form_id]["id"]){return false;}
	#if($gformlist[0]["css"][$form_id] AND lmb_strpos($gformlist[0]["css"][$form_id],"USER/") === false){
 	#	echo "<style type=\"text/css\">@import url(".$gformlist[0]["css"][$form_id]."?v={$umgvar['version']});</style>\n";
	#}
	formListElements($action,0,$ID,$null,$form_id);
}

?>
