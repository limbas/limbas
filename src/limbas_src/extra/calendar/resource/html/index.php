<?php
global $umgvar;

use Limbas\extra\calendar\resource\ResourceCalendarController;

$tabId = $request->get('gtabid');
?>

<script>
    let unprocessedOptions = JSON.parse('<?= json_encode((new ResourceCalendarController())->getOptions($tabId)) ?>');
</script>

<script type="text/javascript" src="assets/js/extra/calendar/resource/resourcecalendar.js?v=<?= $umgvar["version"] ?>"></script>

<style>
    table {
        width: 100%;
        text-align: center;
        border-collapse: separate;
        border-spacing: 0;
    }

    thead th:not(:first-child) {
        border-top: 1px rgba(0, 0, 0, 0.3) solid;
    }

    table th {
        border-bottom: 1px rgba(0, 0, 0, 0.3) solid;
        border-right: 1px rgba(0, 0, 0, 0.3) solid;
    }

    table td {
        border-bottom: 1px rgba(0, 0, 0, 0.3) solid;
        border-right: 1px rgba(0, 0, 0, 0.3) solid;
    }

    table thead th {
        position: sticky;
        top: 0;
    }

    td {
        min-height: 15px;
        vertical-align: top !important;
        overflow: visible;
        position: relative;
    }

    tr {
        min-height: 15px;
        height: 15px;
    }

    .dropzone {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    .rc-event {
        height: 15px;
        background-color: var(--bs-success-bg-subtle);
        border: 1px solid var(--bs-success-border-subtle);
        border-radius: 5px;
        color: var(--bs-warning-text-emphasis);
        padding: 0px;
        cursor: grab;
        text-align: center;
        line-height: 40px;
        user-select: none;
        display: inline-block;
        position: absolute;
        z-index: 10;
        box-sizing: border-box;
    }

    .rc-event:active:not(.resizing) {
        cursor: grabbing;
    }

    .resize-handle {
        opacity: 0;
        position: absolute;
        width: 10px;
        height: 100%;
        background-color: var(--bs-danger);
        border: 1px solid var(--bs-success-border-subtle);
        cursor: ew-resize;
        z-index: 11;
        border-radius: 2px;
        bottom: 0;
    }

    .handle-start {
        left: -5px;
    }

    .handle-end {
        right: -5px;
    }

    .overflow-handle {
        position: absolute;
        width: 25px;
        height: 100%;
        z-index: 11;
        border-radius: 2px;
        bottom: 0;
    }

    td.drag-over {
        background-color: var(--bs-success-bg-subtle);
        border-style: dashed;
    }

    .resizing {
        /* border-style: dashed; */
        user-select: none;
        cursor: ew-resize !important;
    }
</style>

<?php require COREPATH  . 'gtab/html/contextmenus/gtab_filter.php'; ?>

<div class="lmbfringegtab bg-contrast container-fluid border mt-3 py-2" style="width: 98%">
    <div id="container-resourcecalendar" data-tab_id="<?= $tabId ?>">

    </div>
</div>

<form action="main.php" method="post" name="form1" id="form1" autocomplete="off">
    <input type="hidden" id="eventID" name="ID">
    <input type="hidden" name="history_fields">
    <input type="hidden" name="action" value="kalender">
    <input type="hidden" name="gtabid" value="<?= $tabId ?>">
    <input type="hidden" name="history_search">
    <input type="hidden" name="change_ok">
</form>