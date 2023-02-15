<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




if($activelist AND $download_archive AND $LINK[190]){
	if($download_link['url'] = download_archive($activelist,$LID,$download_archive)){
	    echo $download_link['url'];
	    lmb_HTMLDownload($download_link);
	}
}elseif($ID){
    lmb_fileDownload($ID,$sendas);
}else{
	header("HTTP/1.1 401 Unauthorized",true);
	echo "<BR><BR>".$lang[114];
}

?>