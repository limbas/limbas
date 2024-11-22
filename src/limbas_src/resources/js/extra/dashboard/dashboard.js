/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

let grid;

$(function(){
    
    GridStack.setupDragIn(
        '.newWidget',
        { appendTo: 'body', helper: 'clone' }
    );
    
    grid = GridStack.init(
        {
            acceptWidgets: '.newWidget', 
            disableOneColumnMode: true, 
            float: true
        }
    );

    grid.on('added', dashboardWidgetAdded);
    grid.on('change', dashboardWidgetChanged);
    
    $('#select-dashboard').on('change',function(){
        window.location.href = 'main.php?action=intro&dashboardID=' + $(this).val();
    });

    $('#btn-delete-dashboard').on('click', deleteDashboard);
    
    $('#btn-delete-widget').on('click', deleteWidget);
    
    $('[data-delete-widget]').on('click', confirmDeleteWidget);
    
    $('[data-widget-options]').on('click', loadWidgetsOptions);

    $('#btn-save-widget-options').on('click', saveWidgetsOptions);

    init_widgets_js();
});


function init_widgets_js() {
    $( '[data-widget-class]' ).each(function(  ) {
        init_widget_js(this);
    });
}


function init_widget_js(element) {
    let $this = $(element);
    let classname = $this.data('widget-class');

    if (classname) {
        let widgetObject = new window[classname](element);
        widgetObject.init();

        $this.data('widget',widgetObject);
    }
}



function dashboardWidgetAdded(e,items) {
    let data = {};

    let widget;
    let $item;
    
    items.forEach(function(item) {
        widget = item;
        $item = $(item.el);
        
        if (item.width < $item.attr('gs-min-w')) {
            item.width = $item.attr('gs-min-w');
        }

        if (item.height < $item.attr('gs-min-h')) {
            item.height = $item.attr('gs-min-h');
        }
        
        data = 
            {
                id: $item.data('id'),
                type: $item.data('type'),
                dashboard: $('#lmb-dashboard').data('id'),
                x: item.x,
                y: item.y,
                height: item.height,
                width: item.width
            };
    });

    if (!$item.hasClass('newWidget')) {
        return;
    }

    $.ajax({
        type: "POST",
        url: "main_dyns.php?actid=dashboard&action=addwidget",
        data: data,
        dataType: 'json'
    }).done(function (data) {

        grid.removeWidget(widget.el);
        
        let $html = $(data['html']);

        $html.find('[data-delete-widget]').on('click', confirmDeleteWidget);
        $html.find('[data-widget-options]').on('click', loadWidgetsOptions);
        
        $(grid.el).append($html);

        init_widget_js($('#gs-item-'+data['id']).get(0));
        
        grid.makeWidget('#gs-item-'+data['id']);
    });
}


function dashboardWidgetChanged(e,items) {
    let data = [];

    items.forEach(function(item) {
        let $item = $(item.el);
        
        data.push( 
            {
                id: $item.data('id'),
                x: item.x,
                y: item.y,
                height: $item.attr('gs-h'),
                width: $item.attr('gs-w')
            });
    });

    $.ajax({
        type: "POST",
        url: "main_dyns.php?actid=dashboard&action=updatewidget",
        data: {
            widgets: data
        },
        dataType: 'json'
    }).done(function (data) {
        
    });
}

function confirmDeleteWidget() {
    
    $('#btn-delete-widget').data('widget-id',$(this).data('delete-widget'));
    
    $('#deleteWidgetModal').modal('show');    
    
}

function deleteWidget() {
    let id = $(this).data('widget-id');
    
    $.ajax({
        type: "POST",
        url: "main_dyns.php?actid=dashboard&action=deletewidget",
        data: {
            id:id
        },
        dataType: 'json'
    }).done(function () {
        $('#gs-item-'+id).remove();
    });

    $('#deleteWidgetModal').modal('hide');
}


function deleteDashboard() {
    let id = $('#lmb-dashboard').data('id');

    $.ajax({
        type: "POST",
        url: "main_dyns.php?actid=dashboard&action=deletedashboard",
        data: {
            id:id
        },
        dataType: 'json'
    }).done(function () {
        window.location.reload();
    });

    $('#deleteDashboardModal').modal('hide');
}



function loadWidgetsOptions() {
    let id = $(this).data('widget-options');

    $.ajax({
        type: "GET",
        url: "main_dyns.php?actid=dashboard&action=loadwidgetoptions",
        data: {
            id:id
        },
        dataType: 'json'
    }).done(function (data) {
        $('#btn-save-widget-options').data('id',id);
        $('#widget-options-container').html(data['html']);
        $('#editWidgetOptionsModal').modal('show');
    });
}


function saveWidgetsOptions() {
    let id = $(this).data('id');

    let arr = $('#widget-options-container').serializeArray();
    let len = arr.length;
    let options = {};
    for (let i=0; i<len; i++) {
        options[arr[i].name] = arr[i].value;
    }
    
    $.ajax({
        type: "GET",
        url: "main_dyns.php?actid=dashboard&action=savewidgetoptions",
        data: {
            id:id,
            options: options
        },
        dataType: 'json'
    }).done(function () {

        let wObj = $('#gs-item-'+id).data('widget');
        if (wObj) {
            wObj.handleOptionsSave(options);
        }
        
        $('#editWidgetOptionsModal').modal('hide');
    });
}











