<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\wysiwyg;

use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;

class Wysiwyg
{


    /**
     * Returns b from strings of form a_b
     * @param $str
     * @return mixed
     */
    private function removePrefix($str): mixed
    {
        return explode('_', $str, 2)[1];
    }

    /**
     * Renders a selection of all available functions
     * @param $params
     */
    public function functionSelection($params): void
    {
        global $gLmbExt;
        global $umgvar;
        global $lang;
        global $session;

        // require all extensions that are available when report/form is rendered
        require_once(COREPATH . 'extra/report/report.dao');
        if ($gLmbExt["ext_main.inc"]) {
            foreach ($gLmbExt["ext_main.inc"] as $key => $extfile) {
                require_once($extfile);
            }
        }

        // include bootstrap
        echo '<link rel="stylesheet" href="assets/css/' . $session['css'] . '?v=' . $umgvar["version"] . '">';
        echo "<script src=\"assets/vendor/jquery/jquery.min.js?v={$umgvar["version"]}\"></script>";
        echo "<script src=\"assets/vendor/bootstrap/bootstrap.bundle.min.js?v={$umgvar["version"]}\"></script>";
        echo <<<EOD
    <script>
    $(function() {
        $('#search')
            .on("keyup", function() {
                const value = $(this).val().toLowerCase();
                $(".tab-content li").each(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            })
            .focus();
    });
    </script>
EOD;


        // tabulator items
        echo '<ul class="nav nav-tabs pt-2" role="tablist">';
        echo '  <li class="nav-item">';
        echo '    <a class="nav-link active" data-bs-toggle="tab" href="#show_both" role="tab">', ucfirst($lang[3056]), '</a>';
        echo '  </li>';
        echo '  <li class="nav-item">';
        echo '    <a class="nav-link" data-bs-toggle="tab" href="#show_report" role="tab">', ucfirst($lang[3057]), '</a>';
        echo '  </li>';
        echo '  <li class="nav-item">';
        echo '    <a class="nav-link" data-bs-toggle="tab" href="#show_form" role="tab">', ucfirst($lang[828]), '</a>';
        echo '  </li>';
        echo '  <li class="nav-item ms-auto">';
        echo '    <input id="search" type="search" class="form-control" placeholder="', $lang[1626], '...">';
        echo '  </li>';
        echo '  <li class="nav-item ms-auto">';
        echo '    <a class="nav-link" data-bs-toggle="tab" href="#show_lmb" role="tab">Limbas</a>';
        echo '  </li>';
        echo '</ul>';

        // differ between report/form functions
        $lmbFunctions = array('pagebreak', 'background', 'tablerowdata', 'colindex', 'index');
        $config = array(
            'show_both' => function ($name) use ($lmbFunctions) {
                if (!str_starts_with($name, 'report_')) {
                    return false;
                }
                return function_exists('form_' . $this->removePrefix($name)) && !in_array($this->removePrefix($name), $lmbFunctions);
            },
            'show_report' => function ($name) use ($lmbFunctions) {
                return str_starts_with($name, 'report_') && !in_array($this->removePrefix($name), $lmbFunctions);
            },
            'show_form' => function ($name) use ($lmbFunctions) {
                return str_starts_with($name, 'form_') && !in_array($this->removePrefix($name), $lmbFunctions);
            },
            'show_lmb' => function ($name) use ($lmbFunctions) {
                if (!str_starts_with($name, 'report_')) {
                    return false;
                }
                return in_array($this->removePrefix($name), $lmbFunctions);
            },
        );

        // collect functions
        $functions = get_defined_functions();

        echo '<div class="tab-content">';
        foreach ($config as $id => &$filterFunc) {
            // get functions of that config
            $filteredFunctions = array_filter($functions['user'], $filterFunc);

            // list functions
            $active = $id === 'show_both' ? 'show active' : '';
            echo "<div class=\"tab-pane fade {$active}\" id=\"$id\" role=\"tabpanel\">";
            echo '<ul class="list-group list-group-flush">';
            foreach ($filteredFunctions as $function) {
                try {
                    $f = new ReflectionFunction($function);
                    $params = $f->getParameters();
                    $paramCount = 0;
                    if (is_array($params)) {
                        $paramCount = lmb_count($params);
                    }

                    $data = "{functionName: '" . $this->removePrefix($f->name) . "', numParams: {$paramCount}}";
                    $onclick = "window.parent.postMessage({ mceAction: 'lmbFunctionSelected', data : $data }, '*');";
                    echo "<li class=\"list-group-item list-group-item-action\" onclick=\"$onclick\">";

                    // doc comment
                    if ($f->getDocComment()) {
                        echo '<pre class="mb-0 text-secondary">', $f->getDocComment(), '</pre>';
                    }

                    // function name
                    echo '<b><code>', $this->removePrefix($f->name), '</code></b>&nbsp;';

                    // params
                    echo '<small>(';
                    $paramNames = array_map(function (ReflectionParameter $p) {
                        return "<code>\${$p->name}</code>";
                    }, $f->getParameters());
                    echo implode(',&nbsp;',$paramNames);
                    echo ')</small>';

                    echo '</li>';
                } catch (ReflectionException $e) {
                }
            }
            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Renders a selection of all available function params
     * @param $params array('functionName' => ..., 'windowId' => ...)
     */
    public function paramSelection($params): void
    {
        global $gLmbExt;
        global $lang;
        global $umgvar;
        global $session;

        // require all extensions that are available when report/form is rendered
        require_once(COREPATH . 'extra/report/report.dao');
        if ($gLmbExt["ext_main.inc"]) {
            foreach ($gLmbExt["ext_main.inc"] as $key => $extfile) {
                require_once($extfile);
            }
        }

        // check function name param
        $functionName = strtolower($params['functionName']);
        if (!$functionName) {
            return;
        }

        // include bootstrap
        echo '<link rel="stylesheet" href="assets/css/' . $session['css'] . '?v=' . $umgvar["version"] . '">';
        echo "<script src=\"assets/vendor/jquery/jquery.min.js?v={$umgvar["version"]}\"></script>";
        echo "<script src=\"assets/vendor/bootstrap/bootstrap.bundle.min.js?v={$umgvar["version"]}\"></script>";

        // collect functions
        $functions = get_defined_functions();
        $filteredFunctions = array_filter($functions['user'], function ($f) use ($functionName) {
            return $f === "report_{$functionName}" || $f === "form_{$functionName}";
        });
        $function = array_shift($filteredFunctions); // get first found function
        if (!$function) {
            return;
        }

        // js
        echo <<<EOD
<script>
/**
 * Holds the given window id s.t. the opener window can send messages to exactly this window
 * @type int
 */
const thisWindowId = {$params['windowId']};

/**
 * Holds the currently selected params
 * @type {Array}
 */
const params = [];

/**
 * Deactivates all buttons in the specified row, then activates the specified button
 * @param row jquery li
 * @param button jquery to activate
 */
function setActiveButton(row, button) {
    row.find('.btn-outline-primary').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    button.removeClass('btn-outline-secondary').addClass('btn-outline-primary');
}

// button listeners
$(function() {
    // "Empty" button
    $('.js-param-empty').click(function() {
        const row = $(this).closest('li');
        setActiveButton(row, $(this));
        
        const valInput = row.find('.js-value');
        valInput.val('').prop('disabled', true);
        
        params[row.data('paramIndex')] = {
            type: 'empty'
        };
    });
    
    // "Value" button
    $('.js-param-value').click(function() {
        const row = $(this).closest('li');
        setActiveButton(row, $(this));

        const valInput = row.find('.js-value');
        valInput.prop('disabled', false);
        
        params[row.data('paramIndex')] = {
            type: 'value',
            value: valInput.val(),
        };
    });
    
    // Input changed
    $('.js-value').on('input', function() {
        const row = $(this).closest('li');
        params[row.data('paramIndex')] = {
            type: 'value',
            value: $(this).val(),
        };
    });
    
    // "Data" button -> ask opener to select data
    $('.js-param-data').click(function() {
        const row = $(this).closest('li');
        const index = row.data('paramIndex');
        window.parent.postMessage({ mceAction: 'lmbDataParam', data: index, windowId: thisWindowId }, '*');
    });
    
    // "Function" button -> ask opener to select function and params
    $('.js-param-func').click(function() {
        const row = $(this).closest('li');
        const index = row.data('paramIndex');
        window.parent.postMessage({ mceAction: 'lmbFuncParam', data: index, windowId: thisWindowId }, '*');
    });
});

window.addEventListener('message', function (event) {
    const data = event.data;
    if (!data.type) {
        return;
    }
    
    // messages are published to all windows. This checks if this window is the one that is talked to
    if (data.windowId !== thisWindowId) {
        return;
    }
    
    switch (data.type) {
        // received result of "Data" button click
        case 'data': {
            const row = $(`li:nth-child(\${data.paramIndex + 1})`);
            setActiveButton(row, row.find('.js-param-data'));
            
            const valInput = row.find('.js-value');
            valInput.val(data.arrowStr).prop('disabled', true);
            
            params[row.data('paramIndex')] = {
                type: 'data',
                arrowStr: data.arrowStr,
            };
            break;
        }
        
        // received result of "Function" button click
        case 'func': {
            const row = $(`li:nth-child(\${data.paramIndex + 1})`);
            setActiveButton(row, row.find('.js-param-func'));
            
            const valInput = row.find('.js-value');
            valInput.val(data.description).prop('disabled', true);
            
            params[row.data('paramIndex')] = {
                type: 'func',
                params: data.params,
                description: data.description,
                functionName: data.functionName,
            };
            break;
        }
        
        // submit requested -> send params
        case 'submit': {
            window.parent.postMessage({
                mceAction: 'lmbSubmit',
                data: params,
                windowId: thisWindowId,
            }, '*');
            break;
        }
    }
});
</script>
EOD;


        echo '<div class="container-fluid">';
        try {
            $f = new ReflectionFunction($function);

            // doc comment
            if ($f->getDocComment()) {
                echo '<pre class="mb-0 text-secondary">', $f->getDocComment(), '</pre>';
            }

            // function name
            echo '<b><code>', $this->removePrefix($f->name), '</code></b>&nbsp;';

            // for all parameters
            echo '<ul class="list-group list-group-flush">';
            $i = 0;
            foreach ($f->getParameters() as $p) {
                echo "<li class=\"list-group-item d-flex align-items-center\" data-param-index=\"$i\"'>";
                $defaultValue = '';
                $bracketLeft = '&nbsp;';
                $bracketRight = '&nbsp;&nbsp;';
                $referenceAmpersand = '&nbsp;';
                if ($p->isOptional()) {
                    $defaultValue = $p->getDefaultValue();
                    $bracketLeft = '[';
                    $bracketRight = '&nbsp;]';
                }
                if ($p->isPassedByReference()) {
                    $referenceAmpersand = '&';
                }

                echo '<code>', $bracketLeft, $referenceAmpersand, '$', $p->name, $bracketRight, '</code>';

                echo '<div class="input-group ms-5">';
                echo '    <button class="btn btn-outline-secondary js-param-empty" type="button" id="button-addon1">', ucfirst($lang[3058]), '</button>';
                echo '    <button class="btn btn-outline-primary js-param-value" type="button" id="button-addon1">', ucfirst($lang[29]), '</button>';
                echo '  <input type="text" class="form-control js-value" placeholder="', $defaultValue, '">';
                echo '    <button class="btn btn-outline-secondary js-param-data" type="button">', ucfirst($lang[3059]), '</button>';
                echo '    <button class="btn btn-outline-secondary js-param-func" type="button">', ucfirst($lang[3060]), '</button>';
                echo '</div>';

                echo '</li>';
                $i++;
            }

            echo '</ul>';
        } catch (ReflectionException) {
        }
        echo '</div>';
    }

    /**
     * Returns available template groups as JSON
     * @param $params array('templateTable' => ...)
     */
    public function getTemplateGroups($params): void
    {
        global $gtab, $gfield;

        $templateTable = $params['templateTable'];
        if (!$templateTable) {
            return;
        }

        if ($gtab['typ'][$templateTable] != 8 /* templates */) {
            return;
        }

        require_once(COREPATH . 'gtab/sql/add_select.dao');
        $poolID = $gfield[$templateTable]['select_pool'][$gfield[$templateTable]['argresult_name']['GROUPS']];
        $selectValues = pool_select_list_simple($poolID);
        echo json_encode($selectValues['wert']);
    }

    /**
     * Lists all tables of the specified tabgroup
     * @param $parentTabGroupId
     */
    private function forTableSelectionTabgroup($parentTabGroupId): void
    {
        global $tabgroup, $gtab;

        # render tables
        if ($parentTabGroupId != 0) {
            foreach ($gtab['tab_id'] as $tableKey => $tableId) {
                # skip tables that are not in the parent tab group
                if ($gtab['tab_group'][$tableKey] != $parentTabGroupId) {
                    continue;
                }

                echo "<a href=\"#\" class=\"list-group-item list-group-item-action\" onclick=\"window.parent.postMessage({
                mceAction: 'lmbTableSelected',
                data : '{$tableId}'
            }, '*');\">";
                echo '<i class="lmb-icon-8 lmb-table me-2"></i>';
                echo $gtab['desc'][$tableId];
                echo '</a>';
            }
        }

        # for every sub-group
        foreach ($tabgroup['id'] as $tabGroupKey => $tabGroupId) {
            # skip tab groups, that arent a sub group of the parent tab group
            if ($tabgroup['level'][$tabGroupKey] != $parentTabGroupId) {
                continue;
            }

            echo '<a href="#tabgroup_', $tabGroupId, '" class="list-group-item list-group-item-action" data-bs-toggle="collapse">';
            echo '<i class="lmb-icon lmb-angle-down"></i>';
            echo $tabgroup['name'][$tabGroupKey];
            echo '</a>';

            echo '<div class="list-group list-group-flush collapse ps-4" id="tabgroup_', $tabGroupId, '">';
            $this->forTableSelectionTabgroup($tabGroupId);
            echo '</div>';
        }
    }

    /**
     * Lists all tables by tab group
     * @param $_params
     */
    public function forTableSelection($params): void
    {
        global $umgvar;
        global $session;
        // include bootstrap
        echo '<link rel="stylesheet" href="assets/css/' . $session['css'] . '?v=' . $umgvar["version"] . '">';
        echo "<script src=\"assets/vendor/jquery/jquery.min.js?v={$umgvar["version"]}\"></script>";
        echo "<script src=\"assets/vendor/bootstrap/bootstrap.bundle.min.js?v={$umgvar["version"]}\"></script>";

        // list all tables by tab groups
        echo '<div class="list-group list-group-flush">';
        $this->forTableSelectionTabgroup(0);
        echo '</div>';
    }

    /**
     * Renders a selection of all fields of the specified table, can track relations
     * @param $params array('forTable' => ..., 'relationFieldsOnly' => ...)
     */
    public function dataFieldSelection($params): void
    {
        global $gtab, $gfield, $lang, $umgvar, $session;

        // include bootstrap
        echo '<link rel="stylesheet" href="assets/css/' . $session['css'] . '?v=' . $umgvar["version"] . '">';
        echo "<script src=\"assets/vendor/jquery/jquery.min.js?v={$umgvar["version"]}\"></script>";
        echo "<script src=\"assets/vendor/bootstrap/bootstrap.bundle.min.js?v={$umgvar["version"]}\"></script>";
        echo <<<EOD
    <style>
    .hidden {
        display:none !important;
    }
    </style>
    <script>
    $(function() {
        $('#search')
            .on("keyup", function() {
                const value = $(this).val().toLowerCase();
                $(".tab-content li").each(function() {
                    // display:none must have !important in order to work with relations (display: flex)
                    if ($(this).text().toLowerCase().indexOf(value) > -1) {
                        $(this).removeClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            })
            .focus();
    });
    </script>
EOD;


        $forTable = intval($params['forTable']);
        $relationFieldsOnly = boolval($params['relationFieldsOnly']);
        $allowListSelection = boolval($params['allowListSelection']);

        // tabulator
        echo '<ul class="nav nav-tabs pt-2" role="tablist">';
        // 1:1 relation tables
        foreach ($gtab['raverkn'][$forTable] as $tableID) {
            echo '<li class="nav-item">';
            $class = ($tableID === $forTable) ? 'active' : '';
            echo "<a class=\"nav-link {$class}\" data-bs-toggle=\"tab\" href=\"#show_{$tableID}\" role=\"tab\">{$gtab['desc'][$tableID]}</a>";
            echo '</li>';
        }

        // search bar
        echo '<li class="nav-item ms-auto">';
        echo '<input id="search" type="search" class="form-control" placeholder="', $lang[1626], '...">';
        echo '</li>';

        // "change table"-tab
        echo '<li class="nav-item ms-auto">';
        $onclick = "window.parent.postMessage({ mceAction: 'lmbChangeTable'}, '*')";
        echo "<a class=\"nav-link\" href=\"#\" onclick=\"$onclick\">{$lang[3061]}</a>";
        echo '</li>';
        echo '</ul>';

        echo '<div class="tab-content">';
        foreach ($gtab['raverkn'][$forTable] as $tableID) {
            $class = ($tableID === $forTable) ? 'show active' : '';
            echo "<div class=\"tab-pane fade {$class}\" id=\"show_{$tableID}\" role=\"tabpanel\">";
            echo '<ul class="list-group list-group-flush">';

            // allow selection of the base table (for list mode TableRows)
            if ($relationFieldsOnly && $tableID === $forTable && $allowListSelection) {
                echo '<li class="list-group-item d-flex align-items-center flex-row p-0">';

                // select field (left side)
                $selfArrow = '=>' . ucfirst(lmb_strtolower($gtab['table'][$tableID]));
                $onclick = "window.parent.postMessage({ mceAction: 'lmbFieldSelected', data : { tableID: {$tableID}, arrow: '{$selfArrow}' }}, '*')";
                echo "<div class=\"p-3 flex-grow-1 list-group-item-action\" onclick=\"$onclick\">";
                echo "{$lang[366]} ({$gtab['desc'][$tableID]})";
                echo '</div>';

                echo '</li>';
            }

            foreach ($gfield[$tableID]['beschreibung'] as $fieldID => $fieldDesc) {
                // exclude sparte etc.
                if ($gfield[$tableID]['field_type'][$fieldID] >= 100){
                    continue;
                }
                // exclude non-relation fields
                if ($relationFieldsOnly && $gfield[$tableID]['field_type'][$fieldID] != 11) {
                    continue;
                }

                $relationArrow = '';
                if ($tableID != $forTable) {
                    $relationArrow = '=>' . ucfirst(lmb_strtolower($gtab['table'][$tableID]));
                }
                $arrow = $relationArrow . '->' . ucfirst(lmb_strtolower($gfield[$tableID]['field_name'][$fieldID]));

                if ($relationFieldsOnly) {
                    $relationTableID = $gfield[$tableID]['verkntabid'][$fieldID];

                    echo '<li class="list-group-item d-flex align-items-center flex-row p-0">';

                    // select field (left side)
                    $onclick = "window.parent.postMessage({ mceAction: 'lmbFieldSelected', data : { tableID: {$tableID}, fieldID: {$fieldID}, arrow: '{$arrow}' }}, '*')";
                    echo "<div class=\"p-3 flex-grow-1 list-group-item-action\" onclick=\"$onclick\">";
                    echo '<i class="lmb-icon lmb-icon-8 lmb-table"></i>';
                    echo $fieldDesc;
                    echo '</div>';

                    // open relation (right side)
                    $data = "{ tableID: {$tableID}, fieldID: {$fieldID}, relationTableID: {$relationTableID}, arrow: '{$arrow}' }";
                    $onclick = "window.parent.postMessage({ mceAction: 'lmbOpenRelation', data : $data}, '*')";
                    echo "<div class=\"p-3 list-group-item-action text-end\" onclick=\"$onclick\">";
                    echo $fieldDesc;
                    echo '<i class="lmb-icon lmb-caret-right"></i>';
                    echo '</div>';

                    echo '</li>';
                } else if ($gfield[$tableID]['field_type'][$fieldID] == 11) {
                    $relationTableID = $gfield[$tableID]['verkntabid'][$fieldID];
                    $data = "{ tableID: {$tableID}, fieldID: {$fieldID}, relationTableID: {$relationTableID}, arrow: '{$arrow}' }";
                    $onclick = "window.parent.postMessage({ mceAction: 'lmbOpenRelation', data : $data}, '*')";
                    echo "<li class=\"list-group-item list-group-item-action d-flex align-items-center flex-row pe-2\" onclick=\"$onclick\">";
                    echo '<div><i class="lmb-icon lmb-icon-8 lmb-table"></i></div>';
                    echo '<div class="flex-grow-1">', $fieldDesc, '</div>';
                    echo '<div><i class="lmb-icon lmb-caret-right"></i></div>';
                    echo '</li>';
                } else {
                    $onclick = "window.parent.postMessage({ mceAction: 'lmbFieldSelected', data : { tableID: {$tableID}, fieldID: {$fieldID}, arrow: '{$arrow}' }}, '*')";
                    echo "<li class=\"list-group-item list-group-item-action\" onclick=\"$onclick\">";
                    echo $fieldDesc;
                    echo '</li>';
                }
            }
            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
    }

}
