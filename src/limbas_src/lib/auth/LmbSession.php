<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\auth;
class LmbSession
{

    public static function loadCss(): void
    {
        global $session;

        /* --- set main css ------------------- */
        $session['css'] = $session['layout'] . '-' . $session['farbschema'] . '.css';
        $session['legacyCss'] = 'legacy-' . $session['farbschema'] . '.css';
        if (!file_exists(LOCALASSETSPATH . 'css/' . $session['css'])) {
            $session['css'] = 'assets/css/default.css';
        } else {
            $session['css'] = 'localassets/css/' . $session['css'];
        }

        if (!file_exists(LOCALASSETSPATH . 'css/' . $session['legacyCss'])) {
            $session['legacyCss'] = 'assets/css/legacy-default.css';
        } else {
            $session['legacyCss'] = 'localassets/css/' . $session['legacyCss'];
        }
        
    }
    
}
