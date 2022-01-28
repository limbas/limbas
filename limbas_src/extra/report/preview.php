<style>
    #container {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 20px;
        bottom: 20px;
        left: 20px;
        right: 20px;
        margin: 0 !important;
    }

    #header {
        display: flex;
        align-items: baseline;
    }
    #header h1 {
        margin-right: 2em;
    }

    #content {
        flex: 1;
        display: flex;
        justify-content: space-between;
        overflow-y: auto;
    }
    #content_left {
        resize: horizontal;
        width: 50%;
        overflow-y: auto;
    }
    #content_right {
        flex-grow: 1;
    }
    #content_both {
        flex-grow: 1;
    }

    #content_form {
        overflow: auto;
        flex-grow: 1;
    }

    #content_preview_wrapper {
        background-color: #525659; /* from chrome preview */
        padding: 20px;
        border: 1px solid black;
        overflow-y: auto;
    }
    #content_preview {
        background-color: white;
        /* from chrome preview */
        -webkit-box-shadow: 0 0 9px 1px rgba(0,0,0,0.7);
        -moz-box-shadow: 0 0 9px 1px rgba(0,0,0,0.7);
        box-shadow: 0 0 9px 1px rgba(0,0,0,0.7);
        padding: 3em;
    }
    .preview-footer {
        padding-top: 20px;
        border-top: 1px solid  <?= $farbschema["WEB3"] ?>;
        display: flex;
        justify-content: space-around;
    }
    #content_left .preview-footer {
        border-right:1px solid <?= $farbschema['WEB3'] ?>;
    }
    #preview_frame {
        width: 100%;
        height: 100%;
    }
    #content_form {
        margin: 20px;
    }
    .content-vertical {
        display: flex;
        flex-direction: column;
    }

    /* overwrite limbas styles for dark layout */
    #content_preview table,
    #content_preview tr,
    #content_preview td,
    #content_preview a,
    #content_preview del,
    #content_preview ins,
    #content_preview form,
    #content_preview select,
    #content_preview textarea,
    #content_preview input {
        color: black;
        background-color: white;
    }
</style>


<?php
global $LINK;
global $gprinter;
global $greportlist;
global $gtabid;
global $report_id;
global $ID;
global $use_record;
require_once('extra/report/report.dao');
require_once('extra/report/report_dyns.php');

// get params
$gtabid = $_REQUEST['gtabid'];
$report_id = $_REQUEST['report_id'];
$ID = $_REQUEST['ID'];
$use_record = urldecode($_REQUEST['use_record']);
$resolvedTemplateGroups = json_decode(urldecode($_REQUEST['resolvedTemplateGroups']), true);
$resolvedDynamicData = json_decode(urldecode($_REQUEST['resolvedDynamicData']), true);
if (!$resolvedDynamicData) {
    $resolvedDynamicData = [];
}

// report medium
$report_defformat = $greportlist[$gtabid]['defformat'][$report_id];
$report_medium = $_REQUEST['report_medium'];
if (!$report_medium) {
    $report_medium = $report_defformat;
}

// get archive name
$report_name = $greportlist[$gtabid]['name'][$report_id];
$report_savename = $greportlist[$gtabid]['savename'][$report_id];
$report_name = reportSavename($report_name,$report_savename,$ID,$report_medium,$report_rename, false);

// get dynamic data placeholders
list($reportTemplateElements, $dynamicDataPlaceholdersExist, $unresolvedDynamicDataPlaceholdersExist)
    = report_getDynamicDataPlaceholders($gtabid, $report_id, $ID, $use_record, $resolvedTemplateGroups, $resolvedDynamicData);

// check where to display preview as iframe
if (!$dynamicDataPlaceholdersExist) {
    $preview_side = 'both';
}
if ($preview_side) {

    $relation = null;
    if ($_REQUEST['verkn_ID']) {
        $relation = [
            'gtabid' => $_REQUEST['verkn_tabid'],
            'fieldid' => $_REQUEST['verkn_fieldid'],
            'ID' => $_REQUEST['verkn_ID'],
            'showonly' => $_REQUEST['verkn_showonly']
        ];
    }
    
    $path = limbasGenerateReport($report_id, $gtabid, $ID, 5, $report_medium, $report_rename, $use_record, 0, 0, $resolvedTemplateGroups, $resolvedDynamicData, $relation, true);
}

// define empty dynamic data placeholders for preview during editing
if ($unresolvedDynamicDataPlaceholdersExist) {
    foreach ($reportTemplateElements as &$reportTemplateElement) {
        foreach ($reportTemplateElement['unresolvedDynamicDataPlaceholders'] as $placeholder) {
            $resolvedDynamicData[$placeholder->getIdentifier()] = '';
        }
    }
}

/**
 * Returns an input/select/... for a given DynamicDataPlaceholder
 * @param $placeholder DynamicDataPlaceholder
 * @return string
 */
function report_formatDynamicDataPlaceholder($placeholder) {
    global $lang;
    global $resolvedDynamicData;

    // get options
    $options = $placeholder->getOptions();
    if (!$options['type']) {
        $options['type'] = 'text';
    }

    $value = $resolvedDynamicData[$placeholder->getIdentifier()];
    $html = array();
    switch ($options['type']) {
        case 'text':
            $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" value=\"{$value}\">";
            break;

        case 'number':
            $onchange = "checktyp(16,'{$placeholder->getIdentifier()}','{$placeholder->getDescription()}',null,null,this.value,null,null,null,null,null);";
            $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" onchange=\"$onchange\" value=\"{$value}\">";
            break;

        case 'date':
            $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" value=\"{$value}\">";
            $html[] = "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" TITLE=\"{$lang[700]}\" style=\"cursor:pointer;vertical-align:top;\" Onclick=\"lmb_datepicker(event,this,this.previousElementSibling,'','" . dateStringToDatepicker(setDateFormat(1, 1)) . "')\"></i>";
            break;

        case 'select':
            if (!$options['select_id']) {
                lmb_log::error('Dynamic data: "select_id" not set', $lang[56]);
                break;
            }
            require_once('gtab/sql/add_select.dao');
            $values = select_list_simple($options['select_id'], $options['is_attribute']);

            $html[] = "<select name=\"{$placeholder->getIdentifier()}\">";
            $html[] = '<option value=""></option>';
            foreach ($values['wert'] as $val) {
                $selected = ($val === $value) ? 'selected' : '';
                $html[] = "<option value=\"{$val}\" {$selected}>{$val}</option>";
            }
            $html[] = '</select>';
            break;

        case 'extension':
            if (!$options['function']) {
                lmb_log::error('Dynamic data: "function" for extension not set', $lang[56]);
                break;
            }
            $funcName = 'report_' . $options['function'];
            if (!function_exists($funcName)) {
                lmb_log::error('Dynamic data: function "' . $funcName . '" does not exist', $lang[56]);
                break;
            }
            $html[] = $funcName($placeholder->getIdentifier(), $placeholder->getDescription(), $options);
            break;
    }
    return implode('', $html);
}

/**
 * Returns information about template elements of the given report
 * @param $gtabid
 * @param $report_id
 * @param $ID
 * @param $use_record
 * @param $resolvedTemplateGroups
 * @param $resolvedDynamicData
 * @return array of array
 */
function report_getDynamicDataPlaceholders($gtabid, $report_id, $ID, $use_record, $resolvedTemplateGroups, $resolvedDynamicData) {

    if($GLOBALS['greportlist'][$gtabid]["defformat"][$report_id] != 'tcpdf'){
        return array(array(),false,false);
    }

    require_once('extra/template/report/ReportTemplateConfig.php');

    /**
     * custom DynamicDataPlaceholder to support formatting using
     * @see report_formatDynamicDataPlaceholder()
     */
    class SplitScreenDynamicDataPlaceholder extends DynamicDataPlaceholder {
        public function getAsHtmlArr() {
            return array(report_formatDynamicDataPlaceholder($this));
        }
    }

    /**
     * custom TemplateConfig to use the custom SplitScreenDynamicDataPlaceholder
     */
    class SplitScreenTemplateConfig extends ReportTemplateConfig {
        public function getDynamicDataPlaceholderInstance($description, $options) {
            // in case the arguments change
            $class = new ReflectionClass('SplitScreenDynamicDataPlaceholder');
            return $class->newInstanceArgs(func_get_args());
        }
    }

    require_once('gtab/gtab.lib');

    // return values
    $reportTemplateElements = [];
    $dynamicDataPlaceholdersExist = false;
    $unresolvedDynamicDataPlaceholdersExist = false;

    // get report
    $report = &get_report($report_id, 0);
    $pageWidth = intval($report['page_style'][0]);

    // for each template element in report
    $elements = &element_list(null, $report, 'all');
    foreach ($elements['typ'] as $key => $type) {
        // ignore other elements
        if ($type != 'templ') {
            continue;
        }

        // get limbas report element params
        $templGtabid = $report['pic_typ'][$key];
        $content = &$report['value'][$key];
        $parameter = &$report['parameter'][$key];
        $posX = intval($report['posx'][$key]);
        $posY = intval($report['posy'][$key]);
        $width = intval($report['width'][$key]);

        // create config
        $datid = $ID ? $ID : lmb_split('_', $use_record, 2)[0];
        TemplateConfig::$instance = new SplitScreenTemplateConfig($_ = array('referenz_tab' => $gtabid, 'listmode' => $report['listmode']), $parameter, $datid);
        TemplateConfig::$instance->resolvedTemplateGroups = $resolvedTemplateGroups;
        TemplateConfig::$instance->resolvedDynamicData = $resolvedDynamicData;

        // create initial element and build tree
        $baseElement = new ReportTemplateElement($templGtabid, 'lmbBaseElement', $content);
        $baseElement->resolve(intval($datid));

        $dynamicDataPlaceholders = &$baseElement->getAllDynamicDataPlaceholders();
        if ($dynamicDataPlaceholders) {
            $dynamicDataPlaceholdersExist = true;
        }
        $unresolvedDynamicDataPlaceholders = &$baseElement->getUnresolvedDynamicDataPlaceholders();
        if ($unresolvedDynamicDataPlaceholders) {
            $unresolvedDynamicDataPlaceholdersExist = true;
        }
        $reportTemplateElements[] = array(
            'unresolvedDynamicDataPlaceholders' => $unresolvedDynamicDataPlaceholders,
            'allDynamicDataPlaceholders' => $dynamicDataPlaceholders,
            'html' => implode('', $baseElement->getAsHtmlArr()),
            'xPercent' => round(100 * $posX / $pageWidth),
            'yAbs' => $posY,
            'widthPercent' => round(100 * $width / $pageWidth)
        );
    }

    // sort by y pos increasing
    usort($reportTemplateElements, function($a, $b) {
        $res = $a['yAbs'] - $b['yAbs'];
        if ($res != 0) {
            return $res;
        }
        return $a['xPercent'] - $b['xPercent'];
    });
    return array($reportTemplateElements, $dynamicDataPlaceholdersExist, $unresolvedDynamicDataPlaceholdersExist);
}


/**
 * Renders the report preview in html
 * @param $reportTemplateElements
 */
function renderDynamicDataHtmlPreview(&$reportTemplateElements) {
    // group by yAbs s.t. elements that are on same y can be displayed next to each other
    $listByY = array();
    foreach ($reportTemplateElements as &$element) {
        if (!$listByY[$element['yAbs']]) {
            $listByY[$element['yAbs']] = array(&$element);
        } else {
            $listByY[$element['yAbs']][] = &$element;
        }
    }

    echo '<div id="content_preview_wrapper"><div id="content_preview"><div id="content_preview_form">';
    foreach ($listByY as &$elementList) {
        echo '<div>';
        $xSum = 0;
        foreach ($elementList as &$element) {
            // remove first element's x & width from following elements x
            $x = $element['xPercent'] - $xSum;
            echo "<div style=\"display:inline-block;vertical-align:top;margin-left: {$x}%;width: {$element['widthPercent']}%;\">";
            echo $element['html'];
            echo '</div>';
            $xSum += $element['xPercent'] + $element['widthPercent'];
        }
        echo '</div>';
    }
    echo '</div></div></div>';
}

/**
 * Renders the list of inputs to defined dynamic data
 * @param $reportTemplateElements
 */
function renderDynamicDataInputs(&$reportTemplateElements) {
    echo '<div id="content_form">';
    echo '<table>';
    foreach ($reportTemplateElements as &$reportTemplateElement) {
        foreach ($reportTemplateElement['allDynamicDataPlaceholders'] as $placeholder) {
            echo '<tr>';
            echo "<td><label for=\"{$placeholder->getIdentifier()}\">{$placeholder->getDescription()}</label></td>";
            echo '<td>', report_formatDynamicDataPlaceholder($placeholder), '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
    echo '</div>';
}

/**
 * Renders an iframe to preview the generated report
 * @param $path
 */
function renderPreview($path) {
    echo "<iframe id='preview_frame' src='{$path}'></iframe>";
}

/**
 * Renders the footer for the preview iframe
 * @param $path
 * @param $report_name
 */
function renderPreviewFooter($report_name) {
    global $LINK, $gprinter, $lang;

    echo '<div class="preview-footer">';

    // print
    echo '<div>';
    if ($LINK[304] and $gprinter) {
        echo '<select name="report_printer">';
        foreach ($gprinter as $id => &$printer) {
            $sel = '';
            if ($printer['default']) {
                $sel = 'selected';
            }
            echo "<option value=\"{$id}\" {$sel}>{$printer['name']}</option>";
        }
        echo '</select>';

        echo "<input type=\"button\" value=\"{$lang[391]}\" onclick=\"reportPrint()\">";
    }
    echo '</div>';

    echo "<input type=\"button\" value=\"{$lang[2321]}\" onclick=\"openInNewTab();\">";
    echo "<input type=\"button\" value=\"{$lang[1612]}\" onclick=\"downloadUrl('{$report_name}');\">";

    // archive
    echo '<div>';
    echo '<label>', $lang[4], "<input type=\"text\" name=\"report_rename\" value=\"{$report_name}\">", '</label>';
    echo "<input type=\"button\" value=\"{$lang[1787]}\" onclick=\"reportArchive(this)\">";
    echo '</div>';

    echo '</div>';
}

/**
 * Renders the footer for the dynamic data entry side
 * @param $preview_side
 * @param $currentPreviewSide
 */
function renderDynamicDataFooter($preview_side, $currentPreviewSide) {
    global $lang;

    // preview/submit
    if (!$preview_side) {
        echo '<div class="preview-footer">';
        echo '<input type="button" value="', $lang[1500], '" onclick="loadPreview(\'', $currentPreviewSide, '\')">';
        echo '</div>';
    } else if ($preview_side !== $currentPreviewSide) {
        echo '<div class="preview-footer">';
        echo '<input type="button" value="', $lang[33], '" onclick="loadPreview(\'', $preview_side, '\')">';
        echo '</div>';
    }
}

?>
<script src="gtab/html/gtab.js?v=<?=$umgvar["version"]?>"></script>
<script>
    /**
     * Replaces entries in object base with entries of object overwrites, if set
     * @param base
     * @param overwrites
     * @returns object
     */
    function objectReplace(base, overwrites) {
        for (const propName in overwrites) {
            if (overwrites[propName]) {
                base[propName] = overwrites[propName];
            }
        }
        return base;
    }

    /**
     * Loads the iframe preview on the specified side
     * @param preview_side
     */
    function loadPreview(preview_side) {
        report_form.preview_side.value = preview_side;

        // store dynamic data
        const contentPreviewForm = $('#content_preview_form');
        const leftElements = getFormValues(contentPreviewForm);

        const contentForm = $('#content_form');
        const rightElements = getFormValues(contentForm);

        const hiddenInput = $('input[name=resolvedDynamicData]');
        let dynamicData = hiddenInput.val() ? JSON.parse(hiddenInput.val()) : {};
        dynamicData = objectReplace(dynamicData, leftElements);
        dynamicData = objectReplace(dynamicData, rightElements);
        hiddenInput.val(JSON.stringify(dynamicData));

        const iframe = $('#preview_frame');
        if (iframe.length) {
            // reload using ajax
            fetchReportPreview(function(path) {
                preview_path = path;
                iframe.attr('src', path);
            });
        } else {
            // submit
            const mainForm = $('#report_form');
            mainForm.submit();
        }
    }

    /**
     * Downloads the previewed file with the given name
     * @param name
     */
    function downloadUrl(name) {
        const link = document.createElement("a");
        link.download = name;
        link.href = preview_path;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    /**
     * Opens the previewed file in a new tab
     */
    function openInNewTab() {
        open(preview_path, '_blank');
    }

    /**
     * Archives the previewed file
     * @param button
     */
    function reportArchive(button) {
        limbasWaitsymbol(null, true);

        $('#reportOutput').val(2);
        const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
        const callback = function(result) {
            limbasWaitsymbol(null, true, true);

            if (ajaxEvalScript(result)) {
                return; // error
            }
            const fileID = parseInt(result);
            if (!fileID) {
                showAlert(jsvar['lng_56']);
                return;
            }

            $(button).prop('disabled', true); // disable button
            $(report_form.report_rename).prop('disabled', true); // disable name field
            $(button).after("<i class='lmb-icon lmb-aktiv'></i>"); // show check mark
        };
        ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
    }

    /**
     * Generates a pdf preview with ajax
     * @param callbackOuter function(filePath)
     */
    function fetchReportPreview(callbackOuter) {
        limbasWaitsymbol(null, true);

        $('#reportOutput').val(5);
        const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
        const callback = function(result) {
            limbasWaitsymbol(null, true, true);

            if (ajaxEvalScript(result)) {
                return; // error
            }
            const path = result.trim();
            if (!path) {
                showAlert(jsvar['lng_56']);
                return;
            }
            callbackOuter(path);
        };
        ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
    }

    /**
     * Regenerates and prints the previewed pdf
     */
    function reportPrint() {
        limbasWaitsymbol(null, true);

        $('#reportOutput').val(4);
        const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
        const callback = function(result) {
            limbasWaitsymbol(null, true, true);
            ajaxEvalScript(result);
        };
        ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
    }

    /**
     * Gets all set values of inputs, selects and textareas in the given html container
     * @param $container jQuery
     */
    function getFormValues($container) {
        const elements = $container.find('input[name],select[name],textarea[name]');
        const keyVal = {};
        elements.each(function() {
            keyVal[$(this).attr('name')] = $(this).val();
        });
        return keyVal;
    }

    /**
     * The current generated file path. Will be updated once preview is requested again
     * @type {string}
     */
    preview_path = '<?= $path ?>';

    // on load
    $(function() {
        // set correct medium
        report_form.report_medium.value= '<?= $report_medium ?>';

        // update left/right inputs if right/left changed
        $('input[name],select[name],textarea[name]').change(function() {
            const changedEl = $(this);
            const otherEl = $('[name="' + changedEl.attr('name') + '"]').not(changedEl);
            if (otherEl.length === 1) {
                otherEl.val(changedEl.val());
            }
        });
    });
</script>
<form id="report_form" method="POST">
    <input type="hidden" name="action" value="report_preview">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="report_id" value="<?= $report_id ?>">
    <input type="hidden" name="ID" value="<?= $ID ?>">
    <input type="hidden" name="use_record" value="<?= $use_record ?>">
    <input type="hidden" name="resolvedTemplateGroups" value="<?= htmlentities(json_encode($resolvedTemplateGroups)) ?>">
    <input type="hidden" name="resolvedDynamicData" value="<?= htmlentities(json_encode($resolvedDynamicData)) ?>">
    <input type="hidden" name="preview_side" value="<?= $preview_side ?>">
    <input type="hidden" name="report_output" id="reportOutput" value="0">

    <div id="container" class="lmbPositionContainerMain">
        <div id="header">
            <h1><?= $greportlist[$gtabid]['name'][$report_id] ?></h1>

            <?php
            echo '<label>', $lang[2502], "\n";
            echo '<select name="report_medium" onchange="report_form.submit();">';
            # use either pdf or tcpdf in background, dependent on report configuration
            if ($report_defformat === 'pdf' or $report_defformat === 'tcpdf') {
                echo "<option value=\"{$report_defformat}\">pdf</option>";
            }
            echo '<option value="xml">xml</option>';
            if ($greportlist[$gtabid]['odt_template'][$report_id]) {
                echo '<option value="odt">text</option>';
            }
            if ($greportlist[$gtabid]['ods_template'][$report_id]) {
                echo '<option value="ods">spreadsheet</option>';
            }
            echo '</select>';
            echo '</label>';
            ?>

        </div>
        <div id="content">
            <?php
            if ($dynamicDataPlaceholdersExist) {
                // left
                echo '<div id="content_left" class="content-vertical">';
                if ($preview_side === 'left') {
                    renderPreview($path);
                    renderPreviewFooter($report_name);
                } else {
                    renderDynamicDataHtmlPreview($reportTemplateElements);
                    renderDynamicDataFooter($preview_side, 'left');
                }
                echo '</div>';

                // right
                echo '<div id="content_right" class="content-vertical">';
                if ($preview_side === 'right') {
                    renderPreview($path);
                    renderPreviewFooter($report_name);
                } else {
                    renderDynamicDataInputs($reportTemplateElements);
                    renderDynamicDataFooter($preview_side, 'right');
                }
                echo '</div>';
            } else {
                echo '<div id="content_both" class="content-vertical">';
                renderPreview($path);
                renderPreviewFooter($report_name);
                echo '</div>';
            }
            ?>
        </div>
    </div>
</form>
