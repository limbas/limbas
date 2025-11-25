<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<style>
    /*
    Bootstrap Style polyfill //TODO: remove when bootstrap is fully implemented
     */
    :root,
    [data-bs-theme="light"] {
        --legacy-modal-color: #495057;
        --legacy-modal-color-2: #212529;
        --legacy-modal-background-color: #fff;
        --legacy-modal-background-color-2: #e9ecef;
        --legacy-modal-grayed: #6c757d;
        --legacy-modal-highlight: #007bff;
    }
    [data-bs-theme="dark"] {
        --legacy-modal-color: #e9ecef;
        --legacy-modal-color-2: #fff;
        --legacy-modal-background-color: #000;
        --legacy-modal-background-color-2: #495057;
        --legacy-modal-grayed: #6c757d;
        --legacy-modal-highlight: #007bff;
    }

    #lmbTemplateSelect .form-control,
    #lmbTemplateSelect .form-select {
        display: block;
        width: 100%;
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--legacy-modal-color);
        background-color: var(--legacy-modal-background-color);
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    #lmbTemplateSelect .form-control-sm,
    #lmbTemplateSelect .form-select-sm{
        height: calc(1.5em + .5rem + 2px);
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    #lmbTemplateSelect .mb-3, .my-3 {
        margin-bottom: 1rem !important;
    }

    #lmbTemplateSelect .table {
        width: 100%;
        margin-bottom: 1rem;
        color: var(--legacy-modal-color-2);
        border-collapse: collapse;
    }

    #lmbTemplateSelect .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }
    #lmbTemplateSelect .table td, .table th {
        padding: .5rem .75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    #lmbTemplateSelect .table th {
        text-align: inherit;
        text-align: -webkit-match-parent;
    }
    #lmbTemplateSelect .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: rgba(0,0,0,.05);
    }
    #lmbTemplateSelect .table-hover tbody tr:hover {
        color: var(--legacy-modal-color-2);
        background-color: rgba(0,0,0,.075);
    }

    #lmbTemplateSelect .d-none {
        display:none !important;
    }

    #lmbTemplateSelect .input-group {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -ms-flex-align: stretch;
        align-items: stretch;
        width: 100%;
    }

    #lmbTemplateSelect .input-group:not(.has-validation) > .custom-file:not(:last-child) .custom-file-label::after,
    #lmbTemplateSelect .input-group:not(.has-validation) > .custom-select:not(:last-child),
    #lmbTemplateSelect .input-group:not(.has-validation) > .form-control:not(:last-child),
    #lmbTemplateSelect .input-group > .input-group-append > .btn,
    #lmbTemplateSelect .input-group > .input-group-append > .input-group-text,
    #lmbTemplateSelect .input-group > .input-group-prepend:first-child > .btn:not(:first-child),
    #lmbTemplateSelect .input-group > .input-group-prepend:first-child > .input-group-text:not(:first-child),
    #lmbTemplateSelect .input-group > .input-group-prepend:not(:first-child) > .btn,
    #lmbTemplateSelect .input-group > .input-group-prepend:not(:first-child) > .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    #lmbTemplateSelect .input-group > .custom-file, .input-group > .custom-select, .input-group > .form-control, .input-group > .form-control-plaintext {
        position: relative;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        margin-bottom: 0;
    }

    #lmbTemplateSelect .input-group-append {
        margin-left: -1px;
    }
    #lmbTemplateSelect .input-group-append, .input-group-prepend {
        display: -ms-flexbox;
        display: flex;
    }

    #lmbTemplateSelect .input-group-text {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding: .375rem .75rem;
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--legacy-modal-color);
        text-align: center;
        white-space: nowrap;
        background-color: var(--legacy-modal-background-color-2);
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

    #lmbTemplateSelect .input-group-sm > .custom-select,
    #lmbTemplateSelect .input-group-sm > .form-control,
    #lmbTemplateSelect .input-group-sm > .input-group-append > .btn,
    #lmbTemplateSelect .input-group-sm > .input-group-append > .input-group-text,
    #lmbTemplateSelect .input-group-sm > .input-group-prepend > .btn,
    #lmbTemplateSelect .input-group-sm > .input-group-prepend > .input-group-text {
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    #lmbTemplateSelect .btn {
        display: inline-block;
        font-weight: 400;
        color: var(--legacy-modal-color-2);
        text-align: center;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: .25rem;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }

    #lmbTemplateSelect .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    #lmbTemplateSelect .btn:not(:disabled):not(.disabled) {
        cursor: pointer;
    }
    #lmbTemplateSelect .btn-outline-secondary:hover {
        color: var(--legacy-modal-background-color);
        background-color: var(--legacy-modal-grayed);
        border-color: var(--legacy-modal-grayed);
    }
    #lmbTemplateSelect .btn:hover {
        color: var(--legacy-modal-color-2);
        text-decoration: none;
    }

    #lmbTemplateSelect [type="button"]:not(:disabled),
    #lmbTemplateSelect [type="reset"]:not(:disabled),
    #lmbTemplateSelect [type="submit"]:not(:disabled),
    #lmbTemplateSelect button:not(:disabled) {
        cursor: pointer;
    }



    #lmbTemplateSelect [type="button"],
    #lmbTemplateSelect [type="reset"],
    #lmbTemplateSelect [type="submit"], button {
        -webkit-appearance: button;
    }

    #lmbTemplateSelect .btn-block {
        display: block;
        width: 100%;
    }

    #lmbTemplateSelect .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }


    #lmbTemplateSelect .col-2, .col-10 {
        position: relative;
        width: 100%;
    }

    #lmbTemplateSelect .col-9 {
        -ms-flex: 0 0 75%;
        flex: 0 0 75%;
        max-width: 75%;
    }

    #lmbTemplateSelect .col-3 {
        -ms-flex: 0 0 25%;
        flex: 0 0 25%;
        max-width: 25%;
    }
    #lmbTemplateSelect .col-form-label {
        padding-top: calc(.375rem + 1px);
        padding-bottom: calc(.375rem + 1px);
        margin-bottom: 0;
        font-size: inherit;
        line-height: 1.5;
    }

    #lmbTemplateSelect .pagination {
        display: -ms-flexbox;
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: .25rem;
    }
    #lmbTemplateSelect .page-link {
        position: relative;
        display: block;
        padding: .25rem .5rem;
        margin-left: -1px;
        line-height: 1.25;
        color: var(--legacy-modal-highlight);
        background-color: var(--legacy-modal-background-color);
        border: 1px solid #dee2e6;
    }
    #lmbTemplateSelect .page-item.disabled .page-link {
        color: var(--legacy-modal-grayed);
        pointer-events: none;
        cursor: auto;
        background-color: var(--legacy-modal-background-color);
        border-color: #dee2e6;
    }
    #lmbTemplateSelect .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
    }
    #lmbTemplateSelect .page-item:last-child .page-link {
        border-top-right-radius: .25rem;
        border-bottom-right-radius: .25rem;
    }
    #lmbTemplateSelect .justify-content-center {
        -ms-flex-pack: center !important;
        justify-content: center !important;
    }

    #lmbTemplateSelect hr {
        margin: 1rem 0;
        color: inherit;
        background-color: currentColor;
        border: 0;
        opacity: .25;
    }
</style>

<style>
    #lmbTemplateSelect-table tr,
    #lmbTemplateSelect-table td {
        cursor: pointer;
    }
</style>

<script src="assets/js/extra/template/template_select.js?v=<?=$umgvar['version']?>"></script>

<div id="lmbTemplateSelect" data-gtabid="<?=$gtabid?>" data-type="" title="<?=$lang[1502]?>" style="display: none">

    <div id="lmbTemplateSelectLoader" class="pt-3 text-center">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
    </div>
    
    <div id="lmbTemplateSelect-list">

        <div class="input-group input-group-sm mb-3">
            <input type="text" class="form-control" id="lmbTemplateSelect-search">
            <span class="input-group-text"><i class="lmb-icon lmb-undo" id="lmbTemplateSelect-search-reset"></i></span>
        </div>
    
        <div class="table-responsive" style="min-height: 315px">
            <table class="table table-striped table-hover" id="lmbTemplateSelect-table">
    
            </table>
        </div>

        <div id="lmbTemplateSelect-pagination">
            
        </div>
        
    </div>

    <div id="lmbTemplateSelect-single" style="display: none">

        <p class="mb-3">
            <a href="#" id="btn-back-to-report-list"><?=$lang[3086]?></a>
        </p>
        
        <h3 id="lmbTemplateSelect-name"></h3>

        <div id="lmb-report-resolve">

        </div>
        
    </div>
    

</div>
