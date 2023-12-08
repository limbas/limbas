/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Tinymce extension for user-friendly adding/changing of template tags
 * @author Peter Greth
 */
tinymce.PluginManager.add('lmbTemplate', function (editor, url) {
    //region Translations
    tinymce.addI18n('de', {
        'Name is empty!': 'Name ist leer!',
        'Description is empty!': 'Beschreibung ist leer!',
        'Function name is empty!': 'Funktionsname ist leer!',
        'Condition not set!': 'Bedingung nicht gesetzt!',
        'Select data table': 'Daten-Tabelle wählen',
        'Save this table as data table?': 'Soll die Tabelle als neue Daten-Tabelle gespeichert werden?',
        'Select field': 'Feld wählen',
        'Select function': 'Funktion wählen',
        'Set parameters': 'Parameter setzen',
        'remove': 'entfernen',
        'Everywhere': 'Überall',
        'Only in form': 'Nur im Formular',
        'Only in report': 'Nur im Bericht',
        'Add data element': 'Datenelement hinzufügen',
        'Select data source': 'Datenquelle auswählen',
        'Data field': 'Daten-Feld',
        'Alternative text': 'Alternativ-Text',
        'Options': 'Optionen',
        'add': 'Hinzufügen',
        'Add subtemplate element': 'Unterelement hinzufügen',
        'Add dynamic data element': 'Dynamisches Datenelement hinzufügen',
        'Description': 'Beschreibung',
        'Add template group element': 'Template-Gruppen-Element hinzufügen',
        'Group name': 'Gruppenname',
        'Add function call': 'Funktionsaufruf hinzufügen',
        'Function call': 'Funktionsaufruf',
        'Change': 'Ändern',
        'Add if-element': 'If-Element hinzufügen',
        'Condition': 'Bedingung',
        'Data': 'Dateninhalt',
        'Function': 'Funktion',
        'Subelement': 'Unter-Element',
        'Dynamic data': 'Dynamische Dateninhalte',
        'Template group': 'Template-Gruppe',
        'Select ID': 'Pool-ID',
        'Type': 'Typ',
        'number': 'Zahl',
        'date': 'Datum',
        'extension': 'Erweiterung',
        'Writable (form)': 'Schreibbar (Formular)',
        'Data row': 'Wdh. Zeile',
        'New value': 'Neuer Wert',
        'Data row filter': 'Wdh. Zeile-Filter',
        'Header/Footer': 'Kopf-/Fußzeile',
        'Background Image': 'Hintergrundbild',
        'Add a header/footer element': 'Kopf-/Fußzeile hinzufügen',
        'Template element name': 'Name des Templateelements',
        'Use only on first page': 'Nur auf erster Seite',
        'Path to file or DMS ID': 'Dateipfad oder DMS-ID',
        'Image resize': 'Bild-Größe',
        'Repeat background': 'Hintergrundbild wiederholen',
        'Use LIMBAS DMS': 'Limbas DMS benutzen',
        'ID of source table': 'ID Quelltabelle'
    });
    //endregion
    //region Helper functions
    /**
     * Each opened urlDialog has a unique ID associated to determine if messages are sent to that dialog.
     * This counter is incremented for every instantiated urlDialog
     * @type {number}
     */
    let uniqueWindowIdCounter = 1;

    let gtabid = jsvar.gtabid ? jsvar.gtabid : form1.gtabid.value;
    let ID = jsvar.ID ? jsvar.ID : form1.ID.value;

    // fix for contenteditable="false" not reacting to styling in editor
    editor.on('init', function () {
        const $ = tinymce.dom.DomQuery;
        const nonEditableClass = editor.getParam('noneditable_noneditable_class', 'mceNonEditable');
        // Register a event before certain commands run that will turn contenteditable off temporarilly on noneditable fields
        editor.on('BeforeExecCommand', function (e) {
            // The commands we want to permit formatting noneditable items for
            const textFormatCommands = [
                'mceToggleFormat',
                'mceApplyTextcolor',
                'mceRemoveTextcolor'
            ];
            if (textFormatCommands.indexOf(e.command) !== -1) {
                // Find all elements in the editor body that have the noneditable class on them
                //  and turn contenteditable off
                $(editor.getBody()).find('.' + nonEditableClass).attr('contenteditable', null);
            }
        });
        // Turn the contenteditable attribute back to false after the command has executed
        editor.on('ExecCommand', function (e) {
            // Find all elements in the editor body that have the noneditable class on them
            //  and turn contenteditable back to false
            $(editor.getBody()).find('.' + nonEditableClass).attr('contenteditable', false);
        });
    });

    /**
     * Trick tinymce to not translate the given string by setting its translation for the current lang to itself
     * @param str
     * @returns str
     */
    function noTranslate(str) {
        tinymce.addI18n(tinymce.i18n.getCode(), { [str]: str });
        return str;
    }

    /**
     * Shows an error message in a new dialog
     * @param message
     * @returns {*}
     */
    function showErrorMessage(message) {
        return editor.windowManager.open({
            title: 'Error',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'alertbanner',
                        level: 'error',
                        text: message,
                        icon: 'warning'
                    }
                ],
            },
            buttons: [
                {
                    type: 'cancel',
                    text: 'Close',
                    primary: true
                }
            ]
        });
    }

    /**
     * Asks the user to enter some input
     * @param title dialog title
     * @param value original value, shown as placeholder in input
     * @returns {Promise<string>}
     */
    function getInput(title, value) {
        return new Promise(((resolve, reject) => {
            editor.windowManager.open({
                title: title,
                body: {
                    type: 'panel',
                    items: [
                        {
                            type: 'input',
                            name: 'answer',
                            inputMode: 'text',
                            label: '',
                            placeholder: value,
                            maximized: true,
                        }
                    ],
                },
                buttons: [
                    {
                        type: 'submit',
                        text: 'Submit',
                        primary: true
                    }
                ],
                onSubmit: function(api) {
                    resolve(api.getData().answer);
                    api.close();
                },
                onClose: function() {
                    reject();
                }
            });
        }));
    }

    /**
     * Sets the given table as the data source table for the currently opened dataset using ajax
     * @param forTable int
     */
    function setDataTable(forTable) {
        $.ajax({
            method: 'PATCH',
            url: `main_rest.php/${gtabid}/${ID}`,
            data: {
                data: {
                    fortable: forTable
                }
            },
        });
    }

    /**
     * Checks whether the current template dataset has a data table assigned. If not, asks the user to select one
     * @param ignoreSetTable
     * @param askOverwriteTable
     * @returns {Promise<int>}
     */
    function requireTableSet(ignoreSetTable=false, askOverwriteTable=true) {
        return new Promise(function(resolve, reject) {
            // get table from dataset
            $.ajax({
                url: `main_rest.php/${gtabid}/${ID}`,
                data: {
                    '$fields': 'forTable'
                },
                success: function (response) {
                    // table already set?
                    const existingTable = response.data.attributes.fortable;
                    if (!ignoreSetTable && existingTable) {
                        resolve(existingTable);
                        return;
                    }

                    let buttons = [];
                    if (ignoreSetTable) {
                        buttons = [{
                            type: 'custom',
                            text: 'back',
                            name: 'back',
                            align: 'start'
                        }];
                    }

                    // require user to set table
                    const newWin = editor.windowManager.openUrl({
                        title: 'Select data table',
                        url: 'main_dyns.php?actid=manageTemplates&action=wysiwyg&taction=forTableSelection',
                        onMessage: function (api, details) {
                            if (details.mceAction === 'lmbTableSelected') {
                                const forTable = details.data;
                                newWin.close();
                                if (!existingTable) {
                                    // no table set -> set table
                                    setDataTable(forTable);
                                    resolve(forTable);
                                } else if (existingTable != forTable) {
                                    // table set -> ask user if table should be overwritten
                                    if (askOverwriteTable) {
                                        tinymce.activeEditor.windowManager.confirm('Save this table as data table?', function (yes) {
                                            if (yes) {
                                                setDataTable(forTable);
                                            }
                                            resolve(forTable);
                                        });
                                    } else {
                                        resolve(forTable);
                                    }
                                } else {
                                    // table set but equal to selected table
                                    resolve(forTable);
                                }
                            }
                        },
                        onCancel: () => reject(),
                        buttons: buttons,
                        onAction: function(api, details) {
                            if (details.name === 'back') {
                                newWin.close();
                                resolve(existingTable);
                            }
                        },
                    });
                },
                error: () => reject()
            });
        });
    }

    /**
     * Asks the user to select a field
     * @param {string} forTable
     * @param {boolean} relationFieldsOnly
     * @returns {Promise<string>}
     */
    function selectField(forTable, relationFieldsOnly=false) {
        return new Promise(function(resolve, reject) {
            const tableIDArr = [forTable];
            const arrowArr = [];
            const openFieldSelection = function(tableID, allowListSelection) {
                let buttons = [];
                if (arrowArr.length > 0) {
                    buttons = [{
                        type: 'custom',
                        text: 'back',
                        name: 'back',
                        align: 'start'
                    }];
                }
                const newWin = editor.windowManager.openUrl({
                    title: 'Select field',
                    url: 'main_dyns.php?' + $.param({
                        actid: 'manageTemplates',
                        action: 'wysiwyg',
                        taction: 'dataFieldSelection',
                        forTable: tableID,
                        relationFieldsOnly: relationFieldsOnly ? true : null,
                        allowListSelection: allowListSelection ? true : null,
                    }),
                    onMessage: function(api, details) {
                        if (details.mceAction === 'lmbFieldSelected') {
                            arrowArr.push(details.data.arrow);
                            newWin.close();
                            resolve(arrowArr.join(''));
                        } else if (details.mceAction === 'lmbOpenRelation') {
                            newWin.close();
                            tableIDArr.push(details.data.relationTableID);
                            arrowArr.push(details.data.arrow);
                            openFieldSelection(details.data.relationTableID, false);
                        } else if (details.mceAction === 'lmbChangeTable') {
                            newWin.close();
                            requireTableSet(true)
                                .then(tableID => selectField(tableID, relationFieldsOnly))
                                .then(fieldID => resolve(fieldID))
                                .catch(() => reject());
                        }
                    },
                    buttons: buttons,
                    onAction: function(api, details) {
                        if (details.name === 'back') {
                            newWin.close();
                            tableIDArr.pop();
                            arrowArr.pop();
                            const lastTableID = tableIDArr[tableIDArr.length - 1];
                            openFieldSelection(lastTableID, relationFieldsOnly && arrowArr.length === 0);
                        }
                    },
                    onCancel: () => reject()
                });
            };
            // select field
            openFieldSelection(forTable, relationFieldsOnly);
        });
    }

    /**
     * Asks the user to select a function
     * @returns {Promise<obj>}
     */
    function selectFunction() {
        return new Promise(function(resolve, reject) {
            const newWin = editor.windowManager.openUrl({
                title: 'Select function',
                url: 'main_dyns.php?actid=manageTemplates&action=wysiwyg&taction=functionSelection',
                onMessage: function (api, details) {
                    if (details.mceAction === 'lmbFunctionSelected') {
                        newWin.close();
                        resolve(event.data.data);
                    }
                },
                onCancel: () => reject()
            });
        });
    }

    /**
     * Asks the user to select params for a given function
     * @param functionName
     * @returns {Promise<obj>}
     */
    function selectParams(functionName) {
        const thisWindowId = uniqueWindowIdCounter++;
        return new Promise(function (resolve, reject) {
            const dialog = editor.windowManager.openUrl({
                title: 'Set parameters',
                url: `main_dyns.php?actid=manageTemplates&action=wysiwyg&taction=paramSelection&functionName=${functionName}&windowId=${thisWindowId}`,
                buttons: [
                    {
                        type: 'custom',
                        text: 'Paste',
                        name: 'saveParams',
                        primary: true,
                    },
                ],
                onAction: function(api, details) {
                    if (details.name === 'saveParams') {
                        dialog.sendMessage({
                            type: 'submit',
                            windowId: thisWindowId,
                        });
                    }
                },
                onMessage: function (api, details) {
                    // if multiple selectParams windows are open, all would react to all incoming messages
                    //  this checks if the message comes from the current window
                    if (details.windowId != thisWindowId) {
                        return;
                    }
                    switch (details.mceAction) {
                        case 'lmbDataParam':
                            requireTableSet()
                                .then((forTable) => selectField(forTable))
                                .then((arrowStr) => {
                                    dialog.sendMessage({
                                        paramIndex: details.data,
                                        type: 'data',
                                        arrowStr: arrowStr,
                                        windowId: thisWindowId,
                                    });
                                })
                                .catch(() => reject());
                            break;

                        case 'lmbFuncParam':
                            selectFunctionParams()
                                .then((data) => {
                                    dialog.sendMessage({
                                        paramIndex: details.data,
                                        type: 'func',
                                        params: data.params,
                                        functionName: data.functionName,
                                        description: data.description,
                                        windowId: thisWindowId,
                                    });
                                })
                                .catch(() => reject());
                            break;

                        case 'lmbSubmit':
                            dialog.close();
                            resolve(details.data);
                            break;
                    }
                },
                onCancel: () => reject(),
            });
        });
    }

    /**
     * Asks the user to select function and its params directly
     * @returns {Promise<obj>}
     */
    function selectFunctionParams() {
        return selectFunction()
            .then((data) => {
                if (data.numParams === 0) {
                    return Promise.resolve({
                        functionName: data.functionName,
                        params: [],
                        description: `=${data.functionName}()`,
                    });
                }
                return selectParams(data.functionName)
                    .then((params) => {
                        const paramDesc = params.map(p => {
                            switch (p.type) {
                                case 'value':
                                    return `"${p.value}"`;
                                case 'data':
                                    return p.arrowStr;
                                case 'func':
                                    return p.description;
                            }
                        }).join(', ');

                        return {
                            functionName: data.functionName,
                            params: params,
                            description: `=${data.functionName}(${paramDesc})`,
                        };
                    });
            });
    }

    /**
     * For each lmb type, returns the function responsible for entering all data.
     * Used when an existing element is changed
     * @param type
     * @returns {function}
     */
    function getOpenerFunctionFromType(type) {
        switch(type) {
            case 'data':
                return openDataElementSelection;
            case 'template':
                return openSubTemplateElementSelection;
            case 'dynamicData':
                return openDynamicDataElementSelection;
            case 'group':
                return openTemplateGroupElementSelection;
            case 'func':
                return openFunctionElementSelection;
            case 'if':
                return openIfElementSelectionWrapper('if');
            case 'elseif':
                return openIfElementSelectionWrapper('elseif');
            case 'header':
            case 'footer':
                return openHeaderFooterSelection;
            case 'background':
                return openBackgroundSelection;
            default:
                throw new Error(`Type ${type} not handled!`);
        }
    }

    /**
     * Recursively transforms a structure of parameters to a structure of tinymce Nodes
     * @param param
     * @returns {tinymce.html.Node}
     */
    function paramToEl(param) {
        const paramAttrs = {};
        if (param.type === 'empty') {
            return tinymce.html.Node.create('lmb', {
                param: 'value',
                class: 'mceNonEditable',
            });
        } else if (param.type === 'value') {
            return tinymce.html.Node.create('lmb', {
                param: 'value',
                value: param.value,
                class: 'mceNonEditable',
            });
        } else if (param.type === 'data') {
            return tinymce.html.Node.create('lmb', {
                param: 'data',
                src: param.arrowStr,
                class: 'mceNonEditable',
            });
        } else if (param.type === 'func') {
            const paramEl = tinymce.html.Node.create('lmb', {
                param: 'func',
                name: param.functionName,
                class: 'mceNonEditable',
            });
            for (const subParam of param.params) {
                paramEl.append(paramToEl(subParam));
            }
            return paramEl;
        }
    }

    /**
     * Transforms all data-a="b" attributes to strKeys { key_0: "a", val_0: "b" } for tinymce inputs
     * Used to retrieve custom data when changing an existing dataElement
     * @see strKeysToDataObj
     * @param attrs
     * @returns {obj}
     */
    function dataToStrKeys(attrs) {
        let i = 0;
        for (const key in attrs) {
            if (!key.startsWith('data-')) {
                continue;
            }

            const keyName = key.substr(5);
            if (keyName.startsWith('mce')) {
                continue;
            }
            attrs['key_' + i] = keyName;
            attrs['val_' + i] = attrs[key];
            i++;
        }
        return attrs;
    }

    /**
     * Transforms all key_0=a, val_0=b, key_1=c, val_1=d, ... entries to an object which is returned.
     * Keeps all other entries in the given object
     * @param obj
     * @returns object: {data: { a: b, c: d }}
     */
    function strKeysToDataObj(obj) {
        const newObj = { data: {} };
        const idxToKey = {};
        for (const key in obj) {
            const parts = key.split('_', 2);
            if (parts[0] === 'key') {
                newObj.data[obj[key]] = true;
                const idx = parseInt(parts[1]);
                idxToKey[idx] = obj[key];
            }
        }
        for (const key in obj) {
            const parts = key.split('_', 2);
            if (parts[0] === 'key') {
                // already handled
            } else if (parts[0] === 'val') {
                const idx = parseInt(parts[1]);
                newObj.data[idxToKey[idx]] = obj[key];
            } else {
                newObj[key] = obj[key];
            }
        }
        Object.assign(newObj.data, obj.data);
        return newObj;
    }

    /**
     * Inverse of
     * @see strKeysToDataObj
     * @param obj
     * @returns {{...}}
     */
    function dataObjToStrKeys(obj) {
        let i = 0;
        for (const key in obj.data) {
            obj[`key_${i}`] = key;
            obj[`val_${i}`] = obj.data[key];
            i++;
        }
        delete obj.data;
        return obj;
    }

    /**
     * Creates key|val|remove rows from the key_i, val_i entries in given initialData
     * @param initialData
     * @param ignoreAttrs list of attributes to not return
     * @returns array array of Basic dialog components
     */
    function createDataInputRows(initialData, ignoreAttrs=[]) {
        const initialDataObj = strKeysToDataObj(initialData).data;
        //for (const attr of ignoreAttrs) {
        //    delete initialDataObj[attr];
        //}
        return Object.keys(initialDataObj).map((key, i) => ({
            type: 'bar',
            items: [
                {
                    type: 'input',
                    name: 'key_' + i,
                    maximized: true,
                },
                {
                    type: 'input',
                    name: 'val_' + i,
                    maximized: true,
                },
                {
                    type: 'button',
                    name: 'remove_'+key,
                    text: 'remove',
                    icon: 'remove'
                }
            ]
        }));
    }

    /**
     * Shurtcut for the target selection dropdown, as it is used in many opener functions
     * @type {{type: string, name: string, label: string, items: *[]}}
     */
    const targetSelection = {
        type: 'selectbox',
        name: 'target',
        label: 'Target',
        items: [
            {value: '', text: 'Everywhere'},
            {value: 'form', text: 'Only in form'},
            {value: 'report', text: 'Only in report'}
        ]
    };

    /**
     * Inserts the specified tinymce Node at the cursor position
     * @param node
     * @param keepNewChildren
     */
    function insertElement(node, keepNewChildren=false) {
        // prevent writing a child into a lmb node
        if (editor.selection.getNode().nodeName.toLowerCase() === 'lmb') {
            editor.selection.collapse();
        }

        // add zero-width space as text to prevent tinymce from discarding strong etc. in texts like
        // <strong><lmb /></strong> due to no content being present
        const text = tinymce.html.Node.create('#text');
        text.value = "\u200b";
        node.append(text);

        editor.insertContent(new tinymce.html.Serializer().serialize(node));
    }

    /**
     * Replaces the selected Node with the specified Node
     * @param node
     * @param keepNewChildren
     */
    function changeElement(node, keepNewChildren=false) {
        if (keepNewChildren) {
            // add zero-width space as text to prevent tinymce from discarding strong etc. in texts like
            // <strong><lmb /></strong> due to no content being present
            const text = tinymce.html.Node.create('#text');
            text.value = "\u200b";
            node.append(text);

            editor.selection.setContent(new tinymce.html.Serializer().serialize(node));
        } else {
            const selectedEl = editor.selection.getNode();

            // remove all present attributes
            while(selectedEl.attributes.length > 0)
                selectedEl.removeAttribute(selectedEl.attributes[0].name);

            // add all new attributes
            for (const key in node.attributes.map) {
                selectedEl.setAttribute(key, node.attributes.map[key]);
            }
        }
    }

    /**
     * Sets the table id and record id
     * @param new_gtabid
     * @param new_id
     */
    function _setLmbIDs(new_gtabid, new_id) {
        gtabid = new_gtabid;
        ID = new_id;
    }
    //endregion

    //region Opener Functions
    function openDataElementSelection(initialData, onSubmit) {

        /**
         * Returns the config for the dataElementSelection dialog
         * @param initialData
         * @see tinymce.WindowManager.open
         * @returns {{}}
         */
        function getConfig(initialData) {
            if ('data-w' in initialData && initialData['data-w'] === 'true') {
                initialData['data-w'] = true;
            }
            return {
                title: 'Add data element',
                body: {
                    type: 'tabpanel',
                    tabs: [
                        {
                            name: 'selectSrc',
                            title: 'Select data source',
                            items: [
                                { type: 'input', name: 'src', label: 'Data field' },
                                { type: 'button', name: 'selectField', text: 'Edit' }
                            ]
                        },
                        {
                            name: 'advanced',
                            title: 'Advanced',
                            items: [
                                targetSelection,
                                { type: 'input', name: 'alt', label: 'Alternative text' },
                                { type: 'checkbox', name: 'data-w', label: 'Writable (form)' },
                                { type: 'label', label: 'Options', items: createDataInputRows(initialData, ['w']) },
                                {
                                    type: 'bar',
                                    items: [
                                        {
                                            type: 'input',
                                            name: 'newKey',
                                            placeholder: 'Key'
                                        },
                                        {
                                            type: 'button',
                                            name: 'addKeyVal',
                                            text: 'add',
                                            disabled: !initialData.newKey || (initialData.newKey in initialData),
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                onAction: function(api, details) {
                    if (details.name === 'addKeyVal') {
                        // add key/val option
                        const data = strKeysToDataObj(api.getData());
                        data.data[data.newKey] = '';
                        const addedKey = Object.keys(data.data).indexOf(data.newKey);
                        data.newKey = '';
                        api.redial(getConfig(dataObjToStrKeys(data)));
                        api.showTab('advanced');
                        api.focus('val_'+addedKey); // focus new value element
                    } else if (details.name.startsWith('remove_')) {
                        // remove option
                        const parts = details.name.split('_', 2);
                        const keyToRemove = parts[1];

                        const data = strKeysToDataObj(api.getData());
                        delete data.data[keyToRemove];
                        api.redial(getConfig(dataObjToStrKeys(data)));
                        api.showTab('advanced');
                    } else if (details.name === 'selectField') {
                        // change field
                        const previousData = api.getData();
                        requireTableSet()
                            .then((forTable) => selectField(forTable))
                            .then((arrowField) => {
                                api.close();
                                previousData.src = arrowField;
                                editor.windowManager.open(getConfig(previousData));
                            })
                            .catch(() => {}); // ignore, continue in this window
                    }
                },
                onChange: function(dialogApi, details) {
                    // reload due to newKey change
                    if (details.name === 'newKey') {
                        dialogApi.redial(getConfig(dialogApi.getData()));
                        dialogApi.showTab('advanced');
                        dialogApi.focus(details.name);
                    } else if (details.name === 'src') {
                        dialogApi.redial(getConfig(dialogApi.getData()));
                        dialogApi.showTab('selectSrc');
                        dialogApi.focus(details.name);
                    }
                },
                initialData: initialData,
                buttons: [
                    {
                        type: 'submit',
                        text: 'Paste',
                        primary: true,
                        disabled: !!initialData.newKey || !initialData.src, // prevent user from submitting before new option was added
                    },
                ],
                onSubmit: function (api) {
                    let data = strKeysToDataObj(api.getData());

                    // <lmb type="data" src="->Kunde->Name" alt="Kein Kunde verknüpft!" />
                    const attrs = {};
                    attrs['src'] = data.src;
                    if (data.alt) {
                        attrs['alt'] = data.alt;
                    }
                    if (data.target) {
                        attrs['target'] = data.target;
                    }
                    attrs['type'] = 'data';
                    attrs['class'] = 'mceNonEditable';
                    if (data['data-w']) {
                        attrs['data-w'] = 'true';
                    }
                    for (const key in data.data) {
                        attrs['data-' + key] = data.data[key] === true ? '' : data.data[key];
                    }

                    const el = tinymce.html.Node.create('lmb', attrs);
                    onSubmit(el);
                    api.close();
                }
            };
        }

        if (initialData && initialData.src) {
            // src already set -> open tinymce dialog
            editor.windowManager.open(getConfig(initialData));
        } else {
            // src not set -> open table/field selection
            requireTableSet()
                .then((forTable) => selectField(forTable))
                .then((arrowField) => {
                    editor.windowManager.open(getConfig({
                        src: arrowField
                    }));
                })
                .catch(() => {});
        }
    }
    function openSubTemplateElementSelection(initialData, onSubmit) {
        // when loading from existing html
        if (initialData['data-gtabid'] && initialData['data-datid']) {
            initialData['useSpecificDataset'] = true;
        }

        function getConfig(initialData) {
            let datasetControls = [];
            if (initialData.useSpecificDataset) {
                datasetControls = [
                    {
                        type: 'bar',
                        items: [
                            {
                                type: 'input',
                                name: 'data-gtabid',
                                inputMode: 'text',
                                label: 'Tabellen-ID',
                                disabled: true,
                            },
                            {
                                type: 'input',
                                name: 'data-datid',
                                inputMode: 'text',
                                label: 'Datensatz-ID',
                            }
                        ]
                    },
                ];
            }
            return {
                title: 'Add subtemplate element',
                body: {
                    type: 'tabpanel',
                    tabs: [
                        {
                            name: 'general',
                            title: 'General',
                            items: [{type: 'input', name: 'name', label: 'Name'}]
                        },
                        {
                            name: 'advanced',
                            title: 'Advanced',
                            items: [
                                targetSelection,
                                { type: 'input', name: 'data-tabid', label: 'ID of source table' },
                                {
                                    type: 'checkbox',
                                    name: 'useSpecificDataset',
                                    label: 'Datensatz festlegen',
                                },
                                ...datasetControls
                            ]
                        }
                    ]
                },
                initialData: initialData,
                buttons: [{type: 'submit', text: 'Paste', primary: true}],
                onChange: function (api, details) {
                    if (details.name === 'useSpecificDataset') {
                        const data = api.getData();
                        if (data.useSpecificDataset) {
                            requireTableSet(true, false).then((tabID) => {
                                data['data-gtabid'] = tabID;
                                api.redial(getConfig(data));
                                api.showTab('advanced');
                                api.focus('data-datid');
                            });
                        } else {
                            api.redial(getConfig(data));
                            api.showTab('advanced');
                        }
                    }
                },
                onSubmit: function (api) {
                    let data = api.getData();
                    if (!data.name) {
                        showErrorMessage('Name is empty!');
                        return;
                    }
                    if (!data.target) {
                        delete data.target;
                    }
                    if (data.useSpecificDataset && !data['data-gtabid']) {
                        showErrorMessage('Table ID is empty!');
                        return;
                    }
                    if (data.useSpecificDataset && !data['data-datid']) {
                        showErrorMessage('Dataset ID is empty!');
                        return;
                    }
                    delete data.useSpecificDataset;

                    //  <lmb type="template name="Einleitung" target="form" />
                    const attrs = data;
                    attrs['type'] = 'template';
                    attrs['class'] = 'mceNonEditable';

                    const el = tinymce.html.Node.create('lmb', attrs);
                    onSubmit(el);
                    api.close();
                }
            }
        }
        editor.windowManager.open(getConfig(initialData)).focus('name')
    }

    function openDynamicDataElementSelection(initialData, onSubmit) {
        function getConfig(initialData) {
            let typeItems = [];
            if (initialData['data-type'] === 'select') {
                typeItems = [
                    { type: 'input', name: 'data-select_id', placeholder: 'Select ID' },
                ];
            }
            if (initialData['data-type'] === 'extension') {
                typeItems = [
                    { type: 'input', name: 'data-function', placeholder: '', disabled: true },
                    {
                        type: 'button',
                        name: 'selectTypeFunction',
                        text: 'Function',
                    }
                ];
            }

            return {
                title: 'Add dynamic data element',
                body: {
                    type: 'tabpanel',
                    tabs: [
                        {
                            name: 'general',
                            title: 'General',
                            items: [{type: 'input', name: 'title', label: 'Description'}]
                        },
                        {
                            name: 'advanced',
                            title: 'Advanced',
                            items: [
                                {type: 'label', label: 'Type', items: [{
                                    type: 'bar',
                                    items: [
                                        {
                                            type: 'selectbox',
                                            name: 'data-type',
                                            label: null,
                                            size: 1,
                                            items: [
                                                { value: 'text', text: 'text' },
                                                { value: 'number', text: 'number' },
                                                { value: 'date', text: 'date' },
                                                { value: 'select', text: 'selection' },
                                                { value: 'extension', text: 'extension' },
                                            ]
                                        },
                                        ...typeItems
                                    ]
                                }]},
                                {type: 'label', label: 'Options', items: createDataInputRows(initialData, ['type', 'function', 'select_id'])},
                                {
                                    type: 'bar',
                                    items: [
                                        { type: 'input', name: 'newKey', placeholder: 'Key' },
                                        {
                                            type: 'button',
                                            name: 'addKeyVal',
                                            text: 'add',
                                            disabled: !initialData.newKey || (initialData.newKey in initialData),
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                initialData: initialData,
                buttons: [{type: 'submit', text: 'Paste', primary: true}],
                onAction: function (api, details) {
                    if (details.name === 'addKeyVal') {
                        // add key/val option
                        const data = strKeysToDataObj(api.getData());
                        data.data[data.newKey] = '';
                        const addedKey = Object.keys(data.data).indexOf(data.newKey);
                        data.newKey = '';
                        api.redial(getConfig(dataObjToStrKeys(data)));
                        api.showTab('advanced');
                        api.focus('val_' + addedKey); // focus new value element
                    } else if (details.name.startsWith('remove_')) {
                        // remove option
                        const parts = details.name.split('_', 2);
                        const keyToRemove = parts[1];

                        const data = strKeysToDataObj(api.getData());
                        delete data.data[keyToRemove];
                        api.redial(getConfig(dataObjToStrKeys(data)));
                        api.showTab('advanced');
                    } else if (details.name === 'selectTypeFunction') {
                        selectFunction().then((data) => {
                            const newData = {...api.getData(), 'data-function': data.functionName};
                            api.redial(getConfig(newData));
                            api.showTab('advanced');
                        });
                    }
                },
                onChange: function (dialogApi, details) {
                    // reload due to newKey/type change
                    if (details.name === 'newKey' || details.name === 'data-type') {
                        dialogApi.redial(getConfig(dialogApi.getData()));
                        dialogApi.showTab('advanced');
                        dialogApi.focus(details.name);
                    }
                },
                onSubmit: function (api) {
                    let data = strKeysToDataObj(api.getData());
                    if (!data.title) {
                        showErrorMessage('Description is empty!');
                        return;
                    }

                    //  <lmb type="dynamicdata" title="Text einfügen:" />
                    const attrs = {};
                    attrs['type'] = 'dynamicData';
                    attrs['class'] = 'mceNonEditable';
                    attrs['title'] = data.title;
                    attrs['data-type'] = data['data-type'];
                    if (data['data-type'] === 'select') {
                        attrs['data-select_id'] = data['data-select_id'];
                    }
                    if (data['data-type'] === 'extension') {
                        attrs['data-function'] = data['data-function'];
                    }

                    for (const key in data.data) {
                        attrs['data-' + key] = data.data[key] === true ? '' : data.data[key];
                    }

                    const el = tinymce.html.Node.create('lmb', attrs);
                    onSubmit(el);
                    api.close();
                }
            };
        }
        editor.windowManager.open(getConfig(initialData)).focus('title');
    }
    function openTemplateGroupElementSelection(initialData, onSubmit) {

        /**
         * Returns the config for the templateGroupElementSelection dialog
         * @param initialData
         * @see tinymce.WindowManager.open
         * @returns {{...}}
         */
        function getConfig(initialData) {
            return {
                title: 'Add template group element',
                body: {
                    type: 'tabpanel',
                    tabs: [{
                        name: 'general',
                        title: 'General',
                        items: [
                            {
                                type: 'selectbox',
                                name: 'name',
                                label: 'Group name',
                                items: initialData.availableTemplateGroups
                                    ? initialData.availableTemplateGroups.map(g => ({ value: g, text: noTranslate(g) }))
                                    : []
                            }
                        ]
                    }, {
                        name: 'advanced',
                        title: 'Advanced',
                        items: [
                            { type: 'input', name: 'title', label: 'Description' },
                            { type: 'input', name: 'id', label: noTranslate('ID') },
                            { type: 'input', name: 'data-tabid', label: 'ID of source table' }
                        ]
                    }]
                },
                initialData: initialData,
                buttons: [{ type: 'submit', text: 'Paste', primary: true }],
                onSubmit: function (api) {
                    let data = api.getData();
                    if (!data.id) {
                        delete data.id;
                    }
                    if (!data.title) {
                        delete data.title;
                    }

                    // <lmb type="group" name="Gruppe" title="Beschreibung" id="eindeutige_id" />
                    const attrs = data;
                    attrs['type'] = 'group';
                    attrs['class'] = 'mceNonEditable';

                    if (data.tabid) {
                        attrs['data-tabid'] = data.tabid;
                    }
                    

                    const el = tinymce.html.Node.create('lmb', attrs);
                    onSubmit(el);
                    api.close();
                }
            }
        }

        const newWin = editor.windowManager.open(getConfig(initialData));

        // load available template groups
        if (!('availableTemplateGroups' in initialData)) {
            ajaxGet(null, 'main_dyns.php', `manageTemplates&action=wysiwyg&taction=getTemplateGroups&templateTable=${gtabid}`, null, function (response) {
                initialData.availableTemplateGroups = JSON.parse(response.trim());
                newWin.redial(getConfig(initialData));
            });
        }
    }

    function openFunctionElementSelection(initialData, onSubmit) {

        /**
         * Returns the config for the functionElementSelection dialog
         * @param initialData
         * @see tinymce.WindowManager.open
         * @returns {{...}}
         */
        function getConfig(initialData) {
            return {
                title: 'Add function call',
                body: {
                    type: 'tabpanel',
                    tabs: [
                        {
                            name: 'selectFunction',
                            title: 'Function call',
                            items: [
                                {
                                    type: 'bar',
                                    items: [
                                        { type: 'input', name: 'description', disabled: true },
                                        { type: 'button', name: 'selectFunctionParams', text: 'Change' }
                                    ]
                                }
                            ]
                        },
                        {
                            name: 'advanced',
                            title: 'Advanced',
                            items: [ targetSelection ]
                        }
                    ]
                },
                onAction: function(api, details) {
                    if (details.name === 'selectFunctionParams') {
                        selectFunctionParams()
                            .then((data) => {
                                initialData.name = data.functionName;
                                initialData.params = data.params;
                                initialData.description = data.description;
                                api.redial(getConfig(initialData));
                            })
                            .catch(() => {});
                    }
                },
                initialData: initialData,
                buttons: [{ type: 'submit', text: 'Paste', primary: true }],
                onSubmit: function (api) {
                    const data = api.getData();
                    if (!initialData.name) {
                        showErrorMessage('Function name is empty!');
                        return;
                    }

                    // <lmb type="func" name="FunktionsName">
                    //     <lmb param="value" value="Test" />
                    //     <lmb param="data" src="->Kunde->Name" />
                    //     <lmb param="func" name="AndererFunktionsName">
                    // </lmb>
                    const attrs = {};
                    attrs['type'] = 'func';
                    attrs['name'] = initialData.name;
                    if (data.target) {
                        attrs['target'] = data.target;
                    }
                    attrs['class'] = 'mceNonEditable';

                    const el = tinymce.html.Node.create('lmb', attrs);
                    if (initialData.params) {
                        for (const param of initialData.params) {
                            el.append(paramToEl(param));
                        }
                    }
                    onSubmit(el, !!initialData.params);
                    api.close();
                }
            };
        }

        if (initialData.name) {
            // function name entered -> show config dialog

            // when opened from existing element, description is lost
            // could be recalculated if wanted
            if (!initialData.description) {
                initialData.description = `=${initialData.name}(...)`;
            }
            editor.windowManager.open(getConfig(initialData));
        } else {
            // no function name entered -> select function + params
            selectFunctionParams()
                .then((data) => {
                    initialData.name = data.functionName;
                    initialData.params = data.params;
                    initialData.description = data.description;
                    editor.windowManager.open(getConfig(initialData));
                })
                .catch(() => {});
        }
    }
    function openIfElementSelectionWrapper(elementType) {
        /**
         * Wrapper for if/elseif s.t. the function doesnt need to be duplicated. The only difference is the element name
         */
        return function(initialData, onSubmit) {
            /**
             * Returns the config for the ifElementSelection dialog
             * @param initialData
             * @param conditionEl tinymce Node representing the condition
             * @see tinymce.WindowManager.open
             * @returns {{...}}
             */
            function getConfig(initialData, conditionEl=null) {
                // dont show advanced tab for elseif
                let advancedTab = [];
                if (elementType === 'if') {
                    advancedTab = [{
                        name: 'advanced',
                        title: 'Advanced',
                        items: [targetSelection]
                    }];
                }
                return {
                    title: 'Add if-element',
                    body: {
                        type: 'tabpanel',
                        tabs: [
                            {
                                name: 'general',
                                title: 'General',
                                items: [
                                    {
                                        type: 'label',
                                        label: 'Condition',
                                        items: [
                                            {
                                                type: 'bar',
                                                items: [
                                                    {
                                                        type: 'button',
                                                        text: 'Data',
                                                        name: 'selectDataCondition',
                                                        primary: (initialData && initialData.conditionType === 'data'),
                                                    },
                                                    {
                                                        type: 'button',
                                                        text: 'Function',
                                                        name: 'selectFunctionCondition',
                                                        primary: (initialData && initialData.conditionType === 'func'),
                                                    },
                                                ]
                                            },
                                            { type: 'input', name: 'conditionStr', disabled: true, maximizied: true },
                                        ]
                                    }
                                ]
                            },
                            ...advancedTab
                        ]
                    },
                    onAction: function (api, details) {
                        if (details.name === 'selectDataCondition') {
                            requireTableSet()
                                .then((forTable) => selectField(forTable))
                                .then((fieldStr) => {
                                    initialData.conditionType = 'data';
                                    initialData.conditionStr = fieldStr;
                                    const conditionEl = tinymce.html.Node.create('lmb', {
                                        condition: 'data',
                                        src: fieldStr,
                                    });
                                    api.redial(getConfig(initialData, conditionEl));
                                })
                                .catch(() => {});
                        } else if (details.name === 'selectFunctionCondition') {
                            selectFunctionParams()
                                .then((data) => {
                                    const condEl = tinymce.html.Node.create('lmb', {
                                        condition: 'func',
                                        name: data.functionName,
                                    });
                                    if (data.params) {
                                        for (const param of data.params) {
                                            condEl.append(paramToEl(param));
                                        }
                                    }
                                    initialData.conditionType = 'func';
                                    initialData.conditionStr = data.description;
                                    api.redial(getConfig(initialData, condEl));
                                })
                                .catch(() => {});
                        }
                    },
                    initialData: initialData,
                    buttons: [{
                        type: 'submit',
                        text: 'Paste',
                        primary: true,
                        disabled: !initialData || !initialData.conditionType,
                    }],
                    onSubmit: function (api) {
                        let data = api.getData();
                        if (!data.conditionStr || !initialData.conditionType) {
                            showErrorMessage('Condition not set!');
                            return;
                        }
                        if (!data.target) {
                            delete data.target;
                        }

                        // <lmb type="if"> <lmb condition="data" src="->EinDatenFeld" /> </lmb>
                        // <lmb type="if"> <lmb condition="func" name="FunktionsName" /> </lmb>
                        const attrs = data;
                        attrs['type'] = elementType; // if or elseif
                        attrs['class'] = 'mceNonEditable';

                        const el = tinymce.html.Node.create('lmb', attrs);
                        if (conditionEl) {
                            el.append(conditionEl);
                        }

                        onSubmit(el, !!conditionEl);
                        api.close();
                    }
                };
            }

            // try to get condition from child node if exists
            if (initialData && !initialData.conditionType) {
                const selectedNode = editor.selection.getNode();
                if (selectedNode.hasChildNodes()) {
                    const conditionNode = selectedNode.childNodes[0];
                    if (conditionNode && conditionNode.getAttribute) {
                        if (conditionNode.getAttribute('condition') === 'data') {
                            initialData.conditionType = 'data';
                            initialData.conditionStr = conditionNode.getAttribute('src');
                        } else if (conditionNode.getAttribute('condition') === 'func') {
                            initialData.conditionType = 'func';
                            initialData.conditionStr = `=${conditionNode.getAttribute('name')}(...)`;
                        }
                    }
                }
            }

            editor.windowManager.open(getConfig(initialData));
        };
    }

    function openHeaderFooterSelection(initialData, onSubmit) {

        function getConfig(initialData) {
            if ('data-first-page' in initialData) {
                initialData['data-first-page'] = (initialData['data-first-page'] === 'true');
            }
            return {
                title: 'Add a header/footer element',
                body: {
                    type: 'tabpanel',
                    tabs: [
                        {
                            name: 'general',
                            title: 'General',
                            items: [
                                {
                                    type: 'input', 
                                    name: 'name', 
                                    label: 'Template element name'
                                },
                                {
                                    type: 'selectbox',
                                    name: 'type',
                                    label: 'Typ',
                                    items: [
                                        {value: 'header', text: 'Header'},
                                        {value: 'footer', text: 'Footer'}
                                    ]
                                }
                            ]
                        },
                        {
                            name: 'advanced',
                            title: 'Advanced',
                            items: [
                                {
                                    type: 'checkbox',
                                    name: 'data-first-page',
                                    label: 'Use only on first page'
                                },
                            ]
                        }
                    ]
                },
                initialData: initialData,
                buttons: [{type: 'submit', text: 'Paste', primary: true}],
                onSubmit: function (api) {
                    let data = api.getData();
                    if (!data.name) {
                        showErrorMessage('Name is empty!');
                        return;
                    }
                    if (!data.type) {
                        showErrorMessage('Type is empty!');
                        return;
                    }

                    //  <lmb type="template name="Einleitung" target="form" />
                    const attrs = data;
                    attrs['type'] = data['type'];
                    attrs['class'] = 'mceNonEditable';

                    attrs['data-first-page'] = data['data-first-page'] ? 'true' : 'false';

                    const el = tinymce.html.Node.create('lmb', attrs);
                    onSubmit(el);
                    api.close();
                }
            }
        }
        editor.windowManager.open(getConfig(initialData)).focus('name')
    }

    function openBackgroundSelection(initialData, onSubmit) {

        function getConfig(initialData) {
            if ('data-first-page' in initialData) {
                initialData['data-first-page'] = (initialData['data-first-page'] === 'true');
            }
            if ('data-repeat' in initialData) {
                initialData['data-repeat'] = (initialData['data-repeat'] === 'true');
            }
            if ('data-use-dms' in initialData) {
                initialData['data-use-dms'] = (initialData['data-use-dms'] === 'true');
            }
            return {
                title: 'Add subtemplate element',
                body: {
                    type: 'tabpanel',
                    tabs: [
                        {
                            name: 'general',
                            title: 'General',
                            items: [
                                {type: 'input', name: 'value', label: 'Path to file or DMS ID'},
                                {
                                    type: 'checkbox',
                                    name: 'data-use-dms',
                                    label: 'Use LIMBAS DMS'
                                }
                                ]
                        },
                        {            
                            name: 'advanced',
                            title: 'Advanced',
                            items: [
                                {
                                    type: 'checkbox',
                                    name: 'data-first-page',
                                    label: 'Use only on first page'
                                },
                                {
                                    type: 'selectbox',
                                    name: 'data-shrink-mode',
                                    label: 'Image resize',
                                    items: [
                                        {value: '6', text: 'Resize-to-fit w and h'},
                                        {value: '0', text: 'No resizing'},
                                        {value: '1', text: 'Shrink-to-fit w (keep aspect ratio)'},
                                        {value: '2', text: 'Shrink-to-fit h (keep aspect ratio)'},
                                        {value: '3', text: 'Shrink-to-fit w and/or h (keep aspect ratio)'},
                                        {value: '4', text: 'Resize-to-fit w (keep aspect ratio)'},
                                        {value: '5', text: 'Resize-to-fit h (keep aspect ratio)'}
                                    ]
                                },
                                {
                                    type: 'checkbox',
                                    name: 'data-repeat',
                                    label: 'Repeat background'
                                }
                            ]
                        }
                    ]
                },
                initialData: initialData,
                buttons: [{type: 'submit', text: 'Paste', primary: true}],
                onSubmit: function (api) {
                    let data = api.getData();
                    if (!data.value) {
                        showErrorMessage('Path is empty!');
                        return;
                    }

                    //  <lmb type="template name="Einleitung" target="form" />
                    const attrs = data;
                    attrs['type'] = 'background';
                    attrs['class'] = 'mceNonEditable';
                    attrs['data-shrink-mode'] = data['data-shrink-mode'];

                    attrs['data-first-page'] = data['data-first-page'] ? 'true' : 'false';
                    attrs['data-repeat'] = data['data-repeat'] ? 'true' : 'false';
                    attrs['data-use-dms'] = data['data-use-dms'] ? 'true' : 'false';

                    const el = tinymce.html.Node.create('lmb', attrs);
                    onSubmit(el);
                    api.close();
                }
            }
        }
        editor.windowManager.open(getConfig(initialData)).focus('name')
    }
    // endregion

    // region Tinymce Extensions
    // custom menu items
    editor.ui.registry.addMenuItem('lmbTemplateData', {
        text: 'Data',
        onAction: () => openDataElementSelection({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateSubTemplate', {
        text: 'Subelement',
        onAction: () => openSubTemplateElementSelection({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateDynamicData', {
        text: 'Dynamic data',
        onAction: () => openDynamicDataElementSelection({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateGroup', {
        text: 'Template group',
        onAction: () => openTemplateGroupElementSelection({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateFunction', {
        text: 'Function',
        onAction: () => openFunctionElementSelection({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateIf', {
        text: 'If',
        onAction: () => openIfElementSelectionWrapper('if')({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateElseIf', {
        text: 'ElseIf',
        onAction: () => openIfElementSelectionWrapper('elseif')({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateElse', {
        text: 'Else',
        onAction: () => insertElement(tinymce.html.Node.create('lmb', { type: 'else', class: 'mceNonEditable'})),
    });
    editor.ui.registry.addMenuItem('lmbTemplateEndif', {
        text: 'EndIf',
        onAction: () => insertElement(tinymce.html.Node.create('lmb', { type: 'endif', class: 'mceNonEditable'})),
    });
    editor.ui.registry.addMenuItem('lmbTemplateHeaderFooter', {
        text: 'Header/Footer',
        onAction: () => openHeaderFooterSelection({}, insertElement),
    });
    editor.ui.registry.addMenuItem('lmbTemplateBackground', {
        text: 'Background Image',
        onAction: () => openBackgroundSelection({}, insertElement),
    });

    // edit button that appears when clicked on lmb element
    editor.ui.registry.addContextToolbar('link-form', {
        predicate: (node) => node.nodeName.toLowerCase() === 'lmb'
            /* else and endif have no options and therefore no dialog */
            && node.getAttribute('type') !== 'else'
            && node.getAttribute('type') !== 'endif',
        position: 'node',
        items: 'lmbTemplateEdit'
    });
    editor.ui.registry.addButton('lmbTemplateEdit', {
        icon: 'preferences',
        onAction: function() {
            const node = editor.selection.getNode();

            const namedNodeMap = node.attributes;
            const attrs = {};
            for (const attr of namedNodeMap) {
                attrs[attr.name] = attr.value;
            }

            const type = attrs['type'];
            if (type) {
                const opener = getOpenerFunctionFromType(type);
                opener(dataToStrKeys(attrs), changeElement);
                return;
            }

            // change params of function
            const param = attrs['param'];
            if (param === 'value') {
                getInput('New value', attrs.value)
                    .then((newValue) => {
                        attrs.value = newValue;
                        editor.selection.setContent(new tinymce.html.Serializer().serialize(tinymce.html.Node.create('lmb', attrs)));
                    })
                    .catch(() => {});
            } else if (param === 'data') {
                    // change data param
                    requireTableSet()
                        .then((forTable) => selectField(forTable))
                        .then((arrowStr) => {
                            attrs.src = arrowStr;
                            attrs['data-mce-src'] = arrowStr;
                            editor.selection.setContent(new tinymce.html.Serializer().serialize(tinymce.html.Node.create('lmb', attrs)));
                        })
                        .catch(() => {});
            } else if (param === 'func') {
                selectFunctionParams()
                    .then((data) => {
                        attrs['name'] = data.functionName;
                        const el = tinymce.html.Node.create('lmb', attrs);
                        if (data.params) {
                            for (const param of data.params) {
                                el.append(paramToEl(param));
                            }
                        }
                        editor.selection.setContent(new tinymce.html.Serializer().serialize(el));
                    })
                    .catch(() => reject());
            }
        }
    });

    // plugin for repetated table rows
    let dataRowButtonDisabled = true;
    let dataRowButtonSet = false;
    let dataRowFilterSet = false;
    editor.ui.registry.addToggleMenuItem('lmb-data-row', {
        text: 'Data row',
        icon: 'duplicate-row',
        onAction: function() {
            const selectedNode = editor.selection.getNode();
            if (!selectedNode) {
                return;
            }
            const $el = $(selectedNode);
            const $tr = $el.closest('tr');
            if (dataRowButtonSet) {
                $tr.attr('data-lmb-data-row', null);
            } else {
                requireTableSet()
                    .then((forTable) => selectField(forTable, true))
                    .then((arrowStr) => {
                        $tr.attr('data-lmb-data-row', arrowStr);
                    })
                    .catch(() => {});
            }
        },
        onSetup: function(api) {
            api.setDisabled(dataRowButtonDisabled);
            api.setActive(dataRowButtonSet);
            return function() {};
        }
    });
    editor.ui.registry.addToggleMenuItem('lmb-data-row-filter', {
        text: 'Data row filter',
        icon: 'search',
        onAction: function() {
            const selectedNode = editor.selection.getNode();
            if (!selectedNode) {
                return;
            }
            const $el = $(selectedNode);
            const $tr = $el.closest('tr');
            if (dataRowFilterSet) {
                $tr.attr('data-lmb-data-row-filter', null);
            } else {
                selectFunctionParams()
                    .then((data) => $tr.attr('data-lmb-data-row-filter', data.description))
                    .catch(() => {});
            }
        },
        onSetup: function(api) {
            api.setDisabled(!dataRowButtonSet);
            api.setActive(dataRowFilterSet);
            return function() {};
        }
    });
    editor.on('NodeChange', function(data) {
        if (!data || !data.element) {
            return;
        }
        const $el = $(data.element);
        const $tr = $el.closest('tr');
        dataRowButtonDisabled = $tr.length === 0;
        dataRowButtonSet = !!$tr.attr('data-lmb-data-row');
        dataRowFilterSet = !!$tr.attr('data-lmb-data-row-filter');
    });

    // pagebreak button
    editor.ui.registry.addMenuItem('lmb-page-break', {
        text: 'Page break',
        icon: 'page-break',
        onAction: function() {
            editor.insertContent('<lmb type="func" name="pageBreak" class="mceNonEditable" contenteditable="false">​</lmb>');
        },
    });

    return {
        getMetadata: function () {
            return {
                name: 'Limbas Template Plugin',
                url: 'https://limbas.com'
            };
        },
        setLmbIDs: _setLmbIDs
    };
    // endregion
});
