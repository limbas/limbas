<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $LINK;
global $gprinter;
global $greportlist;
global $gtabid;
global $report_id;
global $ID;
global $use_record;
require_once(COREPATH . 'extra/report/report.dao');
require_once(COREPATH . 'extra/report/report_dyns.php');

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

if (!isset($preview)) {
    $preview = 0;
}

// check where to display preview as iframe
if (!$dynamicDataPlaceholdersExist) {
    $preview=1;
}
if ($preview) {

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
            $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" value=\"{$value}\" class=\"form-control\">";
            break;

        case 'number':
            $onchange = "checktyp(16,'{$placeholder->getIdentifier()}','{$placeholder->getDescription()}',null,null,this.value,null,null,null,null,null);";
            $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" onchange=\"$onchange\" value=\"{$value}\" class=\"form-control\">";
            break;

        case 'date':
            $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" value=\"{$value}\" class=\"form-control\">";
            $html[] = "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" TITLE=\"{$lang[700]}\" style=\"cursor:pointer;vertical-align:top;\" Onclick=\"lmb_datepicker(event,this,this.previousElementSibling,'','" . dateStringToDatepicker(setDateFormat(1, 1)) . "')\"></i>";
            break;

        case 'select':
            if (!$options['select_id']) {
                lmb_log::error('Dynamic data: "select_id" not set', $lang[56]);
                break;
            }
            require_once(COREPATH . 'gtab/sql/add_select.dao');
            $values = pool_select_list_simple($options['select_id'], $options['is_attribute']);

            $html[] = "<select name=\"{$placeholder->getIdentifier()}\" class=\"form-select\">";
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
            $html[] = $funcName($placeholder->getIdentifier(), $placeholder->getDescription(), $options, $value);
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

    if($GLOBALS['greportlist'][$gtabid]["defformat"][$report_id] != 'tcpdf' && $GLOBALS['greportlist'][$gtabid]["defformat"][$report_id] != 'mpdf'){ #REPTYPE#
        return array(array(),false,false);
    }

    require_once(COREPATH . 'extra/template/report/ReportTemplateConfig.php');
    require_once(COREPATH . 'extra/template/report/ReportTemplateResolver.php');

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

    require_once(COREPATH . 'gtab/gtab.lib');

    // return values
    $reportTemplateElements = [];
    $dynamicDataPlaceholdersExist = false;
    $unresolvedDynamicDataPlaceholdersExist = false;

    // get report
    $report = get_report($report_id, 0);
    $pageWidth = intval($report['page_style'][0]);
    
    if ($pageWidth <= 0) {
        $pageWidth = 1;
    }

    // for each template element in report
    if ($report['defformat'] == 'mpdf') { #REPTYPE#
        $content = ReportTemplateResolver::getTemplateElementContentById($report['root_template'],$report['root_template_id']);

        $elements = [
            'typ' => ['templ']
        ];

        $report['pic_typ'] = [];
        $report['pic_typ'][0] = $report['root_template'];
        $report['value'] = [];
        $report['value'][0] = $content;
        $report['parameter'] = [];
        $report['parameter'][0] = null;
        $report['posx'] = [];
        $report['posx'][0] = 0;
        $report['posy'] = [];
        $report['posy'][0] = 0;
        $report['width'] = [];
        $report['width'][0] = $pageWidth;
        
    } else {
        $elements = element_list(null, $report, 'all');
    }
    
    
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
        $reportParams = $_ = array('referenz_tab' => $gtabid, 'listmode' => $report['listmode']);
        TemplateConfig::$instance = new SplitScreenTemplateConfig($reportParams, $parameter, $datid);
        TemplateConfig::$instance->resolvedTemplateGroups = $resolvedTemplateGroups;
        TemplateConfig::$instance->resolvedDynamicData = $resolvedDynamicData;

        // create initial element and build tree
        $baseElement = new ReportTemplateElement($templGtabid, 'lmbBaseElement', $content);
        $baseElement->resolve(intval($datid));

        $dynamicDataPlaceholders = $baseElement->getAllDynamicDataPlaceholders();
        if ($dynamicDataPlaceholders) {
            $dynamicDataPlaceholdersExist = true;
        }
        $unresolvedDynamicDataPlaceholders = $baseElement->getUnresolvedDynamicDataPlaceholders();
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
function renderDynamicDataHtmlPreview(&$reportTemplateElements, $report_defformat) {
    // group by yAbs s.t. elements that are on same y can be displayed next to each other
    $listByY = array();
    foreach ($reportTemplateElements as &$element) {
        if (!$listByY[$element['yAbs']]) {
            $listByY[$element['yAbs']] = array(&$element);
        } else {
            $listByY[$element['yAbs']][] = &$element;
        }
    }

    $mpdf = ($report_defformat == 'mpdf');
    
    if ($mpdf) {
        #echo '<div>';
    }
    
    foreach ($listByY as &$elementList) {
        echo $mpdf ? '':'<div>';
        $xSum = 0;
        foreach ($elementList as &$element) {
            // remove first element's x & width from following elements x
            $x = $element['xPercent'] - $xSum;
            echo $mpdf ? '': "<div style=\"display:inline-block;vertical-align:top;margin-left: {$x}%;width: {$element['widthPercent']}%;\">";
            echo $element['html'];
            echo $mpdf ? '': '</div>';
            $xSum += $element['xPercent'] + $element['widthPercent'];
        }
        echo $mpdf ? '':'</div>';
    }

    if ($mpdf) {
        #echo '<div>';
    }
}


//output custom css in editor
$extcss = $umgvar['path'] . $greportlist[$gtabid]['css'][$report_id];
if (file_exists($extcss)) {
    $reportCss = file_get_contents($extcss);
    
    $scss = new ScssPhp\ScssPhp\Compiler();

    $reportCss = $scss->compileString(
            '#report_preview {' . $reportCss . '}'
    )->getCss();
    
    echo '<style>' . $reportCss . '</style>';
}


?>


<script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar["version"]?>"></script>
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
     */
    function loadPreview() {
        report_form.preview.value = 1;

        // store dynamic data
        const contentPreviewForm = $('#report_preview_form');
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
                if(!$(button).hasClass("btn-success") || !result) {
                    $('#report_Archive').addClass("btn btn-danger");
                    $('#report_Archive').html($('#report_Archive').html() + " <i class='lmb-icon lmb-exclamation'></i>");
                }
                return; // error
            }
            if(!$(button).hasClass("btn-success")) {
                $(button).prop('disabled', true); // disable button
                $(report_form.report_rename).prop('disabled', true); // disable name field
                $(button).addClass("btn btn-success");
                $(button).html($(button).html() + " <i class='lmb-icon lmb-check'></i>");
                document.getElementById("report_source").src = result;
            }
        };
        ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
    }

    /**
     * Regenerates and prints the previewed pdf
     */
    function reportPrint(button) {
        limbasWaitsymbol(null, true);

        $('#reportOutput').val(4);
        const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
        const callback = function(result) {

            limbasWaitsymbol(null, true, true);
            if (ajaxEvalScript(result) || !result) {
                if(!$('#report_Archive').hasClass("btn-danger")) {
                    $('#report_Archive').addClass("btn btn-danger");
                    $('#report_Archive').html($('#report_Archive').html() + " <i class='lmb-icon lmb-exclamation'></i>");
                }
                return; // error
            }
            $(button).prop('disabled', true); // disable button
            $(report_form.report_rename).prop('disabled', true); // disable name field
            $(button).addClass("btn btn-success");
            $(button).html($(button).html() + " <i class='lmb-icon lmb-check'></i>");

            <?php if($umgvar['printer_cache']){?>
            if(!$('#report_Archive').hasClass("btn-success")) {
                $('#report_Archive').prop('disabled', true); // disable button
                $('#report_Archive').addClass("btn btn-success");
                $('#report_Archive').html($('#report_Archive').html() + " <i class='lmb-icon lmb-check'></i>");
                document.getElementById("report_source").src = result;
            }
            <?php }?>
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
        //report_form.report_medium.value= '<?= $report_medium ?>';

        // update left/right inputs if right/left changed
        $('input[name],select[name],textarea[name]').change(function() {
            const changedEl = $(this);
            const otherEl = $('[name="' + changedEl.attr('name') + '"]').not(changedEl);
            if (otherEl.length === 1) {
                otherEl.val(changedEl.val());
            }
        })

        //move all footer elements to the bottom
        $('htmlpagefooter').appendTo($('#report_preview_form'))
    });
</script>
<form id="report_form" method="POST" class="h-100">
    <input type="hidden" name="action" value="report_preview">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="report_id" value="<?= $report_id ?>">
    <input type="hidden" name="ID" value="<?= $ID ?>">
    <input type="hidden" name="use_record" value="<?= $use_record ?>">
    <input type="hidden" name="resolvedTemplateGroups" value="<?= htmlentities(json_encode($resolvedTemplateGroups)) ?>">
    <input type="hidden" name="resolvedDynamicData" value="<?= htmlentities(json_encode($resolvedDynamicData)) ?>">
    <input type="hidden" name="preview" value="<?= $preview ?>">
    <input type="hidden" name="report_output" id="reportOutput" value="0">

       
    
    <div class="container-fluid h-100">
            

                <?php if ($dynamicDataPlaceholdersExist): ?>
                    <div class="row h-100">
                    <div class="col-md-6">

                        <div class="row">
                            <div class="col-sm-8">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="vis-tab" data-bs-toggle="tab" href="#vis" role="tab" aria-controls="vis" aria-selected="true">Visuelle Dateneingabe</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="data-tab" data-bs-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="false">Dateneingabe</a>
                                    </li>
                                </ul>
                            </div>
                            <?php if($path != null): ?>
                                <div class="col-sm-4 text-end">
                                    <button type="button" class="btn btn-outline-dark" onclick="loadPreview()"><?=$lang[33]?></button>
                                </div>
                            <?php endif; ?>
                        </div>
    
                        <div class="tab-content">
                            <div class="tab-pane show active" id="vis" role="tabpanel" aria-labelledby="vis-tab">
                                <div id="report_preview_wrapper">
                                    <div id="report_preview">
                                        <div id="report_preview_form">
                                <?php
                                renderDynamicDataHtmlPreview($reportTemplateElements, $report_defformat);
                                ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($path != null): ?>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-outline-dark" onclick="loadPreview()"><?=$lang[33]?></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane" id="data" role="tabpanel" aria-labelledby="data-tab">
    
    
                                
    
                                <div id="content_form" class="p-3">
                                    
                                    <?php
                                    foreach ($reportTemplateElements as &$reportTemplateElement):
                                        foreach ($reportTemplateElement['allDynamicDataPlaceholders'] as $placeholder): ?>
    
                                            <div class="mb-3 row">
                                                <label for="<?=$placeholder->getIdentifier()?>" class="col-sm-3 col-form-label"><?=$placeholder->getDescription()?></label>
                                                <div class="col-sm-9">
                                                    <?=report_formatDynamicDataPlaceholder($placeholder)?>
                                                </div>
                                            </div>
                                    
                                    <?php
                                        endforeach;
                                    endforeach;
                                    ?>
                                    
                                 </div>
    
                                <?php if($path != null): ?>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-dark" onclick="loadPreview()"><?=$lang[33]?></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
    
                    </div>
                <?php endif; ?>
                
                
                <?php if($dynamicDataPlaceholdersExist): ?>
                <div class="col-md-6">
                <?php endif; ?>
                
                    

                <?php if($path != null): ?>
                    <div class="row">
                            <div class="col-md-6 mb-2 pt-2">
                                <div class="row row-cols-lg-auto g-3">
                                    <?php if ($LINK[304] and $gprinter): ?>
                                        <button type="button" id="report_Print" class="btn btn-outline-dark ms-2 me-2" onclick="reportPrint(this)"><?=$lang[391]?></button>
                                    <?php endif; ?>
                                    <button type="button" id="report_Archive" class="btn btn-outline-dark me-4" onclick="reportArchive(this)"><?=$lang[1787]?></button>
                                    <button type="button" class="btn btn-outline-dark me-2" onclick="openInNewTab()"><?=$lang[2321]?></button>
                                    <?php /*<button type="button" class="btn btn-outline-dark me-2" onclick="downloadUrl('<?=$report_name?>')"><?=$lang[1612]?></button> */?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2 pt-2">
                                <div class="row row-cols-lg-auto g-3 justify-content-end">

                                    <?php if ($LINK[304] and $gprinter): ?>
                                        <div>
                                            <label><?=$lang[2935]?>
                                            <select name="report_printer" class="form-select form-select-sm me-2">
                                                <?php foreach ($gprinter as $id => &$printer): ?>
                                                    <option value="<?=$id?>" <?=($greportlist[$gtabid]["printer"][$report_id] == $id) ? 'selected' : ''?>><?=$printer['name']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            </label>
                                        </div>
                                    <?php endif; ?>

                                    <?php /*
                                    <div>
                                        <label><?=$lang[2502]?>
                                            <select name="report_medium" onchange="report_form.submit();" class="form-select form-select-sm me-2">
                                                <?php
                                                if ($report_defformat === 'pdf' || $report_defformat === 'tcpdf' || $report_defformat === 'mpdf') { #REPTYPE#
                                                    echo "<option value=\"{$report_defformat}\">pdf</option>";
                                                }
                                                echo '<option value="xml">xml</option>';
                                                if ($greportlist[$gtabid]['odt_template'][$report_id]) {
                                                    echo '<option value="odt">text</option>';
                                                }
                                                if ($greportlist[$gtabid]['ods_template'][$report_id]) {
                                                    echo '<option value="ods">spreadsheet</option>';
                                                }
                                                ?>
                                            </select>
                                        </label>
                                    </div>
                                    */
                                    ?>

                                    <div>
                                        <label><?=$lang[4]?><input type="text" name="report_rename" class="form-control form-control-sm me-2" value="<?=$report_name?>"></label>
                                    </div>
                                    
                                </div>
                            </div>
                    </div>

                    <iframe id="report_source" src="<?=$path?>" class="h-100 w-100"></iframe>
                <?php else: ?>

                    <input type="hidden" name="report_medium" id="report_medium" value="<?=$report_medium?>">
                
                    <div class="text-center pt-5">
                        <button type="button" class="btn btn-outline-dark" onclick="loadPreview()"><?=$lang[1500]?></button>
                    </div>


                <?php endif; ?>

                <?php if($dynamicDataPlaceholdersExist): ?>
                </div>
                </div>
                <?php endif; ?>
                    
            
    </div>
    
    
</form>
