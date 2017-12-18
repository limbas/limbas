<?php
/* Zum Thema Templates
 * -------------------
 *
 * Der optionale PHP-Teil MUSS unmittelbar am Anfang des Templates kommen!
 *
 * Zwischendurch DARF kein PHP mehr vorkommen, und es DARF auch keine direkte
 * Ausgabe innerhalb dieses PHP-Blocks geschehen. Das geht in zwei Schritten:
 * Dazu muss zuerst eine Template-Variable hinzugefügt werden, die dann im
 * anschliessenden HTML-Block mit zur Ausgabe kommt.
 *
 * BEISPIELE
 * ---------
 * 1) Auf eine bereits vorab gesetzte Template-Variable innerhalb
 *    des PHP-Blocks zugreifen:
 *
 *		$Variablenname = $this->search("Variablenname");
 *		...
 *
 * 2) Eine Template-Variable im PHP-Block des gleichen Templates hinzufügen:
 *
 *		$this->add("Variablenname", "Wert");
 *		...
 *
 * 3) Eine Template-Variable innerhalb des HTML-Teils ausgeben:
 *
 *		<p>${Variablenname}</p>
 *		...
 *
 * 4) Standardvariablen, die immer gesetzt sind:
 *		- ${_self} (=die eigene URL)
 *		- ${_path} (=der eigene Pfad)
 *
 * WICHTIG
 * -------
 * Ein PHP-Block mit implementierter Logik innerhalb eines Templates sollte nur
 * in Ausnahmefällen (Notlösung, Hack, faule Ausrede, Pragmatismus) existieren.
 *
 * Per Definition SOLLTE nur (X)HTML und JavaScript in ein Template rein:
 *
 *  Logik (Server) = .php
 *  Logik (Client) = .js
 *  Ausgabe = .tpl
 *  Optik = .css
 *
 * -----------------------------------------------------------------------------
 * [Nach Kenntnissnahme darf dieser Kommentar entweder der Vernichtung oder auch
 * der internen Dokumentation zugeführt werden ;)]
 */

global $gtab,$gtabid;
$this->add("tablename", $gtab["table"][$gtabid]);
$this->add("tabledesc", $gtab["desc"][$gtabid]);
?>
<!-- [gui_menu.tpl] -->


<table class="GtabTableFringeHeader" cellspacing="0" cellpadding="0" border="0" style="margin:0px;width:100%">

<tr>
<td class="gtabHeaderInputTR" id="mail_textmenu"></td>
</tr>

<tr>
    <td class="gtabHeaderSymbolTR">
        
        <table cellspacing="2" cellpadding="0" border="0">
        <tbody>
            <tr id="mail_buttons">

            </tr>
        </tbody>
        </table>
    </td>
</tr>
</table>










<!-- [/gui_menu.tpl] -->