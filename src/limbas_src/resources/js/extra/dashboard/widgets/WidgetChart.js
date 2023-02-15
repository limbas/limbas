/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

class WidgetChart extends Widget {
    constructor(widgetElement) {
        super(widgetElement);
    }


    init() {

        this.$canvas = $('#dash-chart-' + this.widgetId);
        this.canvas = this.$canvas.get(0);

        this.chartid = this.$canvas.data('chartid');

        this.load();

    }

    reload(options) {
        this.chartid = options['chartid'];

        let chartjs = this.$canvas.data('chartjs');

        if (chartjs) {
            chartjs.destroy();
        } else {
            this.$canvas.removeClass('d-none');
            this.$widgetElement.find('.dash-uninitialized').remove();
        }

        this.load();
    }


    load() {
        if (!this.chartid) {
            return;
        }


        let element = this.canvas;
        let $element = this.$canvas;

        $.ajax({
            type: 'GET',
            url: 'main_dyns.php?actid=getDiag&chartjs=1&diag_id=' + this.chartid,
            dataType: 'json',
            cache: false,
            success: function (data) {
                let chart = new Chart(element, {
                    type: data['type'],
                    data: {
                        labels: data['labels'],
                        datasets: data['datasets']
                    },
                    options: data['options']
                });
                $element.data('chartjs', chart);
            },
            error: function () {
                $element.replaceWith($('<div class="text-center pt-2">Error loading chart.</div>'));
            }
        });

    }

    handleOptionsSave(options) {
        this.reload(options);
    }
}

window.WidgetChart = WidgetChart;
