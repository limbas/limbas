<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

require COREPATH . "gtab/gtab_erg.lib";
require COREPATH . "gtab/gtab_type.lib";
require COREPATH  . 'gtab/html/contextmenus/gtab_filter.php';

global $gtab;
global $lang;
global $session;
global $umgvar;
global $farbschema;

$gtabid = $request->get('gtabid');
$startstamp = $request->get('startstamp');
$verkn_ID = $request->get('verkn_ID');
$verkn_fieldid = $request->get('verkn_fieldid');
$verkn_tabid = $request->get('verkn_tabid');


$myExt_useIframe = 1; # set true if using iframe for details

# EXTENSIONS
if ($GLOBALS["gLmbExt"]["ext_calendar.inc"]) {
    foreach ($GLOBALS["gLmbExt"]["ext_calendar.inc"] as $key => $extfile) {
        require_once($extfile);
    }
}

# benutzes Formular
if ($gtab["tab_view_tform"][$gtabid]) {
    $fformid = $gtab["tab_view_tform"][$gtabid];
}
?>

<script type='text/javascript'>

    jsvar['jqday'] = ['<?= $lang[873] ?>', '<?= $lang[874] ?>', '<?= $lang[875] ?>', '<?= $lang[876] ?>', '<?= $lang[877] ?>', '<?= $lang[878] ?>', '<?= $lang[879] ?>'];
    jsvar['jqmonth'] = ['<?= $lang[880] ?>', '<?= $lang[881] ?>', '<?= $lang[882] ?>', '<?= $lang[883] ?>', '<?= $lang[884] ?>', '<?= $lang[885] ?>', '<?= $lang[886] ?>', '<?= $lang[887] ?>', '<?= $lang[888] ?>', '<?= $lang[889] ?>', '<?= $lang[890] ?>', '<?= $lang[891] ?>'];
    jsvar['gtabid'] = <?= $gtabid ?>;
    jsvar['startstamp'] = '<?= $startstamp ?>';
    jsvar['dateformat'] = <?= $session['dateformat'] ?>;
    jsvar['verkn_ID'] = '<?= $verkn_ID ?>';
    jsvar['verkn_tabid'] = '<?= $verkn_tabid ?>';
    jsvar['verkn_fieldid'] = '<?= $verkn_fieldid ?>';
    jsvar['fformid'] = '<?= $fformid ?>';

    var lmb_localobj = new lmb_localobj();

    <?php

    /* ----- setting parameters ------- */
    $calsetting = array(
        'viewmode' => $umgvar['calendar_viewmode'],
        'minTime' => '00:00:00',
        'maxTime' => '24:00:00',
        'weekNumberTitle' => '',
        'firstDay' => $umgvar['calendar_firstday'],
        'slotMinutes' => $umgvar['calendar_slotminutes'],
        'snapMinutes' => 0,
        'editable' => 'b',
        'selectable' => 'b',
        'weekNumbers' => 0,
        'weekends' => 'b',
        'allDayDefault' => 'b',
        'nowIndicator' => 'b'
    );

    foreach ($calsetting as $option => $type) {

        if (${'cal_' . $option}) {
            $value = ${'cal_' . $option};
        } elseif ($gtab['params2'][$gtabid][$option]) {
            $value = $gtab['params2'][$gtabid][$option];
        } else {
            $value = null;
        }

        ${'cal_' . $option} = $value;

        if (!$value and $type == 'b') {
            $value = 'false';
        } elseif (!$value and is_numeric($type)) {
            $value = $type;
        } elseif (!$value and !is_numeric($type)) {
            ${'cal_' . $option} = $type;
            $value = "'$type'";
        } else {
            ${'cal_' . $option} = $value;
            $value = "'" . $value . "'";
        }

        echo "jsvar['cal_$option'] = $value;\n";

        # hide-weekends in ganttview not supported
        if ($gtab['params1'][$gtabid]) {
            echo "jsvar['cal_weekends'] = true;\n";
            $cal_weekends = 1;
        }
    }

    if ($startstamp) { ?>
    jsvar['year'] = <?= date("Y", $startstamp) ?>;
    jsvar['month'] = <?= (date("m", $startstamp) - 1) ?>;
    jsvar['date'] = <?= date("d", $startstamp) ?>;
    <?php }

    if ($form_id or $form_id = $gformlist[$gtabid]["id"][$gtab["tab_view_tform"][$gtabid]]) {
    ?>
    jsvar['formid'] = '<?= $form_id ?>';
    jsvar['form_dimension'] = '<?= $gformlist[$gtabid]["dimension"][$gtab["tab_view_tform"][$gtabid]] ?>';
    jsvar['iframe'] = <?= $myExt_useIframe ?>;
    <?php
    }
    ?>

    jsvar['buttons'] =
        {
            <?php if($LINK[224] and $cal_viewmode != 3): ?>
            settings: {title: '<?= $lang[$LINK['name'][224]] ?>'},
            <?php endif; ?>
            <?php if ($extrasmenu): ?>
            extras: {title: '<?= $lang[1939] ?>'},
            <?php endif; ?>
            create: {title: '<?= $lang[1435] ?>'},
            search: {title: '<?= $lang[2255] ?>'},
            refresh: {title: '<?= 'Refresh' ?>'},
        };

    $(document).ready($(function () {
        lmb_calInit();
    }));

</script>
<style>
    .fc .fc-button-primary { color: <?=$farbschema["WEB13"]?>; background-color: <?=$farbschema["WEB2"]?> !important; border-color: <?=$farbschema["WEB2"]?> !important; }
</style>

<div id="viewmenu" class="lmbContextMenu" style="display:none;z-index:2000;" onclick="activ_menu = 1;">
    <?php #-----------------  -------------------
    pop_menu(255, '', '');
    pop_line();
    $opt = array();
    $opt['val'] = array('dayGridMonth', 'timeGridWeek', 'timeGridDay', 'listMonth', 'listWeek', 'listDay');
    $opt['desc'] = array($lang[1437], $lang[1436], $lang[1435], $lang[1437], $lang[1436], $lang[1435]);
    $opt['label'] = array('Raster', null, null, 'Liste');
    pop_select("lmb_calChangeSettings('viewmode',this.value);", $opt, $cal_viewmode, '', '', "Anzeigemodus: ");

    //Firstday
    $opt = array();
    $opt['val'] = array(1, 2, 3, 4, 5, 6, 0);
    $opt['desc'] = array($lang[874], $lang[875], $lang[876], $lang[877], $lang[878], $lang[879], $lang[873]);
    pop_select("lmb_calChangeSettings('firstDay',this.value);", $opt, $cal_firstDay, '', '', "Erster Tag der Woche: ");

    //Slotminutes
    $opt = array();
    $opt['val'] = array(5, 15, 30, 60, 120, 240);
    $opt['desc'] = array("5", "15", "30", "60", "120", "240");
    pop_select("lmb_calChangeSettings('slotMinutes',this.value);", $opt, $cal_slotMinutes, '', '', 'Minuten pro Zeile: ');

    //Snapminutes
    $opt = array();
    $opt['val'] = array();
    for ($i = 1; $i <= 48; $i++) {
        $opt['val'][$i] = $i * 5;
    }
    $opt['desc'] = $opt['val'];
    $sel = 10;
    pop_select("lmb_calChangeSettings('snapMinutes',this.value);", $opt, $cal_snapMinutes, '', '', 'Snapminutes: ');

    //Weekends
    if ($cal_weekends) {
        $cal_weekends_ = 'checked';
    }
    pop_checkbox('', "if($(this).is(':checked')){lmb_calChangeSettings('weekends',1)}else{lmb_calChangeSettings('weekends',0)}", '', "", $cal_weekends_, '', 'Wochenenden: ', '');

    //Zeitfilter
    pop_line();
    pop_input2("lmb_calChangeSettings('minTime',this.value);", '', $cal_minTime, "", "MinTime");
    pop_input2("lmb_calChangeSettings('maxTime',this.value);", '', $cal_maxTime, "", "MaxTime");
    pop_bottom();
    ?>
</div>

<?php
# extension
if (function_exists($GLOBALS["gLmbExt"]["menuCalExtras"][$gtabid])) {
    echo '<div ID="extrasmenu" class="lmbContextMenu" style="visibility:hidden;z-index:2001;" OnClick="activ_menu = 1;">';
    $GLOBALS["gLmbExt"]["menuCalExtras"][$gtabid]($gtabid, $form_id, $ID, $gresult);
    $extrasmenu = 1;
    echo '</div>';
}
?>

<?php if ($LINK[296]):
    $DPdateFormat = setDateFormat(1, 2);

    ?>
    <div ID="bulkmenu" class="lmbContextMenu" style="display:none;z-index:2001;" onclick="activ_menu = 1;">
        <div class="bulkContainer ui-widget-header p-2">
            <div class="container-fluid px-2">

                <div class="row py-1">
                    <div class="col"><b><?= $lang[1441] ?></b></div>
                </div>

                <div class="row">
                    <div class="col ui-widget-header" id="bulk_ts">
                        <div class="container-fluid px-1">
                            <div class="row pb-1 pt-1 px-0 align-items-center">
                                <div class="col-3 px-1">
                                    <?= $lang[2799] ?>:
                                </div>
                                <div class="col-auto px-1">
                                    <input class="form-control form-control-sm" name="calBulk_termStaH[0]"
                                           type="text" style="width: 100px" onchange="lmb_validateTime(this,'h')">
                                </div>
                                <div class="col-auto px-0">
                                    <span>&nbsp;<b>:</b>&nbsp;</span>
                                </div>
                                <div class="col-auto px-1">
                                    <input class="form-control form-control-sm" name="calBulk_termStaM[0]"
                                           type="text" style="width: 100px" value="00"
                                           onchange="lmb_validateTime(this,'m')">
                                </div>
                            </div>
                            <div class="row pb-1 px-0 align-items-center">
                                <div class="col-3 px-1">
                                    <?= $lang[2385] ?>:
                                </div>
                                <div class="col-auto px-1">
                                    <input class="form-control form-control-sm" name="calBulk_termEndH[0]"
                                           type="text"
                                           style="width: 100px" onchange="lmb_validateTime(this,'h')">
                                </div>
                                <div class="col-auto px-0">
                                    <span>&nbsp;<b>:</b>&nbsp;</span>
                                </div>
                                <div class="col-auto px-1">
                                    <input class="form-control form-control-sm" name="calBulk_termEndM[0]"
                                           type="text"
                                           style="width: 100px" value="00" onchange="lmb_validateTime(this,'m')">
                                </div>
                            </div>
                            <div class="row pb-1 px-0 align-items-center">
                                <div class="col-3 px-1">
                                    <?= $lang[2801] ?>:
                                </div>
                                <div class="col-auto px-1">
                                    <input class="form-control form-control-sm" name="calBulk_termLenD[0]" type="text"
                                           style="width: 100px" onchange="lmb_validateTime(this,'d')">
                                </div>
                                <div class="col-auto ps-1 pe-0">
                                    <span> min.</span>
                                </div>
                                <div class="col-auto px-1">
                                    <i class="lmb-icon lmb-plus align-middle" onclick="lmb_bulkAddTermin()"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row py-1">
                    <div class="col"><b><?= $lang[2803] ?></b></div>
                </div>

                <div class="row">
                    <div class="col ui-widget-header">
                        <div class="container-fluid p-1">
                            <div class="row">
                                <div class="col-3 pe-1"><?= $lang[874] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[1]" value="1"
                                                                  checked style=""></div>
                                <div class="col-3 pe-1"><?= $lang[875] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[2]" value="1"
                                                                  checked style=""></div>
                                <div class="col-3 pe-1"><?= $lang[876] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[3]" value="1"
                                                                  checked style=""></div>
                                <div class="col-3 pe-1"><?= $lang[877] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[4]" value="1"
                                                                  checked style=""></div>
                                <div class="col-3 pe-1"><?= $lang[878] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[5]" value="1"
                                                                  checked style=""></div>
                                <div class="col-3 pe-1"><?= $lang[879] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[6]" value="1"
                                                                  checked style=""></div>
                                <div class="col-3 pe-1"><?= $lang[873] ?></div>
                                <div class="col-auto ps-0"><input type="checkbox" class="form-check checkbox"
                                                                  name="calBulk_schmeaDay[7]" value="1"
                                                                  checked style=""></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row py-1">
                    <div class="col"><b><?= $lang[2802] ?></b></div>
                </div>

                <div class="row">
                    <div class="col ui-widget-header">
                        <div class="container-fluid px-1">
                            <div class="row pt-1 pb-1 px-0 align-items-center">
                                <div class="col-2 pe-0"><?= $lang[2799] ?>:</div>
                                <div class="col-auto ps-0 pe-0">
                                    <input class="form-control form-control-sm" type="text" id="calBulk_periodStartT"
                                           name="calBulk_periodStart"
                                           style="width:100px">
                                </div>
                                <div class="col-auto ps-0 pe-4">
                                    <i id="sel_7" border="0" align="middle"
                                       onclick="lmb_datepicker(event,this,'','','<?= $DPdateFormat ?>')"
                                       style="cursor:pointer;vertical-align:top;"
                                       title="öffne Quick-Kalender"
                                       class="lmb-icon lmb-caret-right"></i>
                                </div>
                                <div class="col-2 pe-0"><?= $lang[2385] ?>:</div>
                                <div class="col-auto ps-0 pe-0">
                                    <input class="form-control form-control-sm" type="text" id="calBulk_periodEndT"
                                           name="calBulk_periodEnd"
                                           style="width:100px">
                                </div>
                                <div class="col-auto px-0">
                                    <i id="sel_7" border="0" align="middle"
                                       onclick="lmb_datepicker(event,this,'','','<?= $DPdateFormat ?>')"
                                       style="cursor:pointer;vertical-align:top;"
                                       title="öffne Quick-Kalender"
                                       class="lmb-icon lmb-caret-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php
endif;
?>


<form action="main.php" method="post" name="formCalendar" id="formCalendar">
    <input type="hidden" name="action" value="kalender">

    <input type="hidden" name="startstamp" value="<?= $startstamp ?>">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="verkn_tabid" value="<?= $verkn_tabid ?>">
    <input type="hidden" name="verkn_fieldid" value="<?= $verkn_fieldid ?>">
    <input type="hidden" name="verkn_ID" value="<?= $verkn_ID ?>">

    <input type="hidden" name="cal_viewmode" value="<?= $cal_viewmode ?>">
    <input type="hidden" name="cal_firstDay" value="<?= $cal_firstDay ?>">
    <input type="hidden" name="cal_minTime" value="<?= $cal_minTime ?>">
    <input type="hidden" name="cal_maxTime" value="<?= $cal_maxTime ?>">
    <input type="hidden" name="cal_firstHour" value="<?= $cal_firstHour ?>">
    <input type="hidden" name="cal_slotMinutes" value="<?= $cal_slotMinutes ?>">
    <input type="hidden" name="cal_snapMinutes" value="<?= $cal_snapMinutes ?>">
    <input type="hidden" name="cal_weekends" value="<?= $cal_weekends ?>">
    <input type="hidden" name="filter_reset">

    <div class="lmbfringegtab bg-contrast container-fluid overflow-auto border mt-3 pb-2" style="width: 98%">


        <div class="row py-1 px-3 mb-2 border-bottom" style="<?= $farbschema['WEB3'] ?>;">
            <div class="col">
                <span class="fs-3 row"
                      onclick="lmb_calReload(true,false)"><?= $gtab['desc'][$gtabid] ?></span>
                <span class="fw-bold fs-4 row"
                      id="current_date"><span>
            </div>
            <?php /*
            <span class="col-auto d-flex align-items-center justify-content-center">
                <i class="lmb-icon lmb-top-y u-color-4 fs-2"
                   style="cursor:pointer;"
                   onclick="calendar.prev();lmb_calSetTitle();"></i>
                <i class="lmb-icon lmb-top-x u-color-4 fs-2"
                   style="cursor:pointer;"
                   onclick="calendar.next();lmb_calSetTitle();"></i>
            </span>
 */ ?>
        </div>

        <div class="row">
            <div class="col">
                <div class="container-fluid px-0">
                    <div class="row visually-hidden">
                        <div id="menu1" class="lmbGtabTabmenuActive" onclick="document.formCalendar.submit();">
                            <?= $gtab["desc"][$gtabid] ?>
                        </div>
                    </div>
                    <div class="row py-1 px-3">
                        <div id='calendar' class="overflow-auto p-0"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</form>


<div id="lmb_eventDetailFrame"
     style="position:absolute;display:none;z-index:9999;overflow:hidden;width:300px;height:300px;padding:0px;">
    <iframe id="lmb_detailFrame" style="width:100%;height:100%;overflow:auto;"></iframe>
</div>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<div id="limbasAjaxGtabContainer" class="ajax_container" style="padding:1px;position:absolute;display:none;"
     onclick="activ_menu=1;"></div>
<div id="lmbCalAjaxContainer" class="ajax_container" style="position:absolute;display:none;z-index:2003"></div>

<?php include('cal_detailmodal.php'); ?>

<div id="lmbAjaxContextmenu" class="lmbContextMenu" style="position:absolute;display:none;z-index:2004;"
     OnClick="activ_menu=1;">
    <?php
    pop_top();
    if ($gtab["add"][$gtabid] and $gtab["copy"][$gtabid]) {
        pop_menu(0, 'lmb_calMoveCopy(\'move\')', $lang[2666], '', '', 'lmb-icon-cus lmb-page-cut');
    }   # auschneiden
    if ($gtab["edit"][$gtabid]) {
        pop_menu(0, 'lmb_calMoveCopy(\'copy\')', $lang[817], '', '', 'lmb-page-copy');
    }        # kopieren
    if ($gtab["delete"][$gtabid]) {
        pop_menu(0, "lmb_calDelete(event,$gtabid,activeEvent.event.id);", $lang[160], '', '', 'lmb-icon-cus lmb-page-delete-fancy');
    }        # löschen
    pop_bottom();
    ?>
</div>

<div id="lmbAjaxContextPaste" class="lmbContextMenu" style="position:absolute;display:none;z-index:2004;"
     OnClick="activ_menu=1;">
    <?php
    pop_top();
    if ($gtab["add"][$gtabid] and $gtab["copy"][$gtabid]) {
        echo '<div id="lmb_eventPaste">';
        pop_menu(0, "lmb_calPaste(activeDate)", $lang[2667], '', '', 'lmb-paste');
        echo '</div>';
    }   # einfügen
    pop_bottom();
    ?>
</div>


<form action="main.php" method="post" name="form1" id="form1" autocomplete="off">
    <input type="hidden" id="eventID" name="ID">
    <input type="hidden" name="history_fields">
    <input type="hidden" name="action" value="<?= $action ?>">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="history_search">
    <input type="hidden" name="change_ok">

</form>
