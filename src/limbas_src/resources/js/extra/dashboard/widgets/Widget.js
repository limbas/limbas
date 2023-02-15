/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

class Widget {

    constructor(widgetElement) {

        this.widgetElement = widgetElement;
        this.$widgetElement = $(this.widgetElement);
        this.widgetId = this.$widgetElement.data('id');

        if (new.target === Widget) {
            throw new TypeError("Cannot construct Widget instances directly");
        }

        if (this.init === undefined) {
            throw new TypeError("Must override method");
        }

        if (this.reload === undefined) {
            throw new TypeError("Must override method");
        }

        if (this.load === undefined) {
            throw new TypeError("Must override method");
        }

        if (this.handleOptionsSave === undefined) {
            throw new TypeError("Must override method");
        }
    }
}

window.Widget = Widget;
