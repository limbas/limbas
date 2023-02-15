<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
require_once( COREPATH . 'extra/dashboard/dashboard.dao');
?>

<div id="lmb-dashboard" data-id="<?=$dashboardID?>">
    <div class="collapse" id="lmb-dashboard-options">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-3 pr-0">
                        <button class="btn btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#collapseWidgets" aria-expanded="false" aria-controls="collapseWidgets">Widget hinzufügen</button>
                        </div>
                        <div class="col-md-7">
                            <div class="collapse" id="collapseWidgets">
    
                                <?php foreach ($widgets as $widget): ?>
                                    <?= $widget->getPlaceholder() ?>
                                <?php endforeach; ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-8">
                            <?php if (count($dashboards)>1) : ?>
                                <select class="form-select" id="select-dashboard">
                                    <option value="" selected>Dashboard wechseln</option>
                                    <?php foreach($dashboards as $dashid => $name) :
                                        if ($dashid == $dashboardID) {
                                            continue;
                                        }
                                        ?>
                                        <option value="<?=$dashid?>"><?=$name?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if (!empty($dashboards) && !empty($dashboardID)) : ?>
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDashboardModal"><i class="lmb-icon lmb-trash"></i></button>
                            <?php endif; ?>
                            <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addDashboardModal"><i class="lmb-icon lmb-plus"></i></button>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-end">
        <div class="border bg-white border-top-0 p-2 d-inline-block dashboard-options" data-bs-toggle="collapse" data-bs-target="#lmb-dashboard-options" aria-expanded="false" aria-controls="dashboardOptions">
            <i class="lmb-icon lmb-cog"></i>
        </div>
    </div>
    
    
    <div class="px-3">
        <div class="grid-stack">
            <?php foreach($dashboard['widgets'] as $widget) : ?>
                <?=$widget->render(); ?>
            <?php endforeach;?>
        </div>
    </div>
</div>


<div class="modal fade" id="addDashboardModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <input type="hidden" name="action" value="intro">
                <input type="hidden" name="addDashboard" value="1">
                <div class="modal-header">
                    <h5 class="modal-title">Dashboard hinzufügen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="input-new-dashboard" class="col-sm-2 col-form-label">Bezeichnung</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="input-new-dashboard" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteDashboardModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-danger">
            <div class="modal-header">
                <h5 class="modal-title">Dashboard löschen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Sind Sie sicher, dass Sie das aktuelle Dashboard löschen möchten?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-danger" id="btn-delete-dashboard">Löschen</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteWidgetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-danger">
            <div class="modal-header">
                <h5 class="modal-title">Widget löschen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Sind Sie sicher, dass Sie das ausgewählte Widget löschen möchten?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-danger" id="btn-delete-widget">Löschen</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editWidgetOptionsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Widget Optionen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="widget-options-container">
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary" id="btn-save-widget-options">Speichern</button>
            </div>
        </div>
    </div>
</div>
