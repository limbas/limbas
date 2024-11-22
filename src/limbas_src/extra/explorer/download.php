<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

// download from DMS
if ($ID && is_numeric($ID)) {
    lmb_fileDownload($ID, $disposition);

// download zip archive
} elseif ($activelist and $download_archive and $LINK[190]) {
    if ($archivefile = download_archive($activelist, $LID)) {
        lmb_PHPDownload($archivefile, $disposition);
    }

// download from hash
} elseif ($hash && $GLOBALS['session']['download'][$hash]) {
    lmb_PHPDownload($GLOBALS['session']['download'][$hash]);
    if (!$GLOBALS['session']['download'][$hash]['permanent']) {
        unset($GLOBALS['session']['download'][$hash]);
    }

} else {
    throw new AccessDeniedHttpException();
}

exit(1);
