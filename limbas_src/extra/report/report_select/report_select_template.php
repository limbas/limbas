<style>
    /*
    Bootstrap Style polyfill //TODO: remove when bootstrap is implemented
     */
    #lmb-report-select .form-control {
        display: block;
        width: 100%;
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    #lmb-report-select .form-control-sm {
        height: calc(1.5em + .5rem + 2px);
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    #lmb-report-select .mb-3, .my-3 {
        margin-bottom: 1rem !important;
    }

    #lmb-report-select .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
    }

    #lmb-report-select .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }
    #lmb-report-select .table td, .table th {
        padding: .5rem .75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    #lmb-report-select .table th {
        text-align: inherit;
        text-align: -webkit-match-parent;
    }
    #lmb-report-select .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: rgba(0,0,0,.05);
    }
    #lmb-report-select .table-hover tbody tr:hover {
        color: #212529;
        background-color: rgba(0,0,0,.075);
    }

    #lmb-report-select .d-none {
        display:none !important;
    }

    #lmb-report-select .input-group {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -ms-flex-align: stretch;
        align-items: stretch;
        width: 100%;
    }

    #lmb-report-select .input-group:not(.has-validation) > .custom-file:not(:last-child) .custom-file-label::after,
    #lmb-report-select .input-group:not(.has-validation) > .custom-select:not(:last-child),
    #lmb-report-select .input-group:not(.has-validation) > .form-control:not(:last-child),
    #lmb-report-select .input-group > .input-group-append > .btn,
    #lmb-report-select .input-group > .input-group-append > .input-group-text,
    #lmb-report-select .input-group > .input-group-prepend:first-child > .btn:not(:first-child),
    #lmb-report-select .input-group > .input-group-prepend:first-child > .input-group-text:not(:first-child),
    #lmb-report-select .input-group > .input-group-prepend:not(:first-child) > .btn,
    #lmb-report-select .input-group > .input-group-prepend:not(:first-child) > .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    #lmb-report-select .input-group > .custom-file, .input-group > .custom-select, .input-group > .form-control, .input-group > .form-control-plaintext {
        position: relative;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        margin-bottom: 0;
    }

    #lmb-report-select .input-group-append {
        margin-left: -1px;
    }
    #lmb-report-select .input-group-append, .input-group-prepend {
        display: -ms-flexbox;
        display: flex;
    }

    #lmb-report-select .input-group-text {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding: .375rem .75rem;
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        text-align: center;
        white-space: nowrap;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

    #lmb-report-select .input-group-sm > .custom-select,
    #lmb-report-select .input-group-sm > .form-control,
    #lmb-report-select .input-group-sm > .input-group-append > .btn,
    #lmb-report-select .input-group-sm > .input-group-append > .input-group-text,
    #lmb-report-select .input-group-sm > .input-group-prepend > .btn,
    #lmb-report-select .input-group-sm > .input-group-prepend > .input-group-text {
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    #lmb-report-select .btn {
        display: inline-block;
        font-weight: 400;
        color: #212529;
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

    #lmb-report-select .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    #lmb-report-select .btn:not(:disabled):not(.disabled) {
        cursor: pointer;
    }
    #lmb-report-select .btn-outline-secondary:hover {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    #lmb-report-select .btn:hover {
        color: #212529;
        text-decoration: none;
    }

    #lmb-report-select [type="button"]:not(:disabled),
    #lmb-report-select [type="reset"]:not(:disabled),
    #lmb-report-select [type="submit"]:not(:disabled),
    #lmb-report-select button:not(:disabled) {
        cursor: pointer;
    }



    #lmb-report-select [type="button"],
    #lmb-report-select [type="reset"],
    #lmb-report-select [type="submit"], button {
        -webkit-appearance: button;
    }

    #lmb-report-select .btn-block {
        display: block;
        width: 100%;
    }

    #lmb-report-select .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }


    #lmb-report-select .col-2, .col-10 {
        position: relative;
        width: 100%;
    }

    #lmb-report-select .col-9 {
        -ms-flex: 0 0 75%;
        flex: 0 0 75%;
        max-width: 75%;
    }

    #lmb-report-select .col-3 {
        -ms-flex: 0 0 25%;
        flex: 0 0 25%;
        max-width: 25%;
    }
    #lmb-report-select .col-form-label {
        padding-top: calc(.375rem + 1px);
        padding-bottom: calc(.375rem + 1px);
        margin-bottom: 0;
        font-size: inherit;
        line-height: 1.5;
    }

    #lmb-report-select .pagination {
        display: -ms-flexbox;
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: .25rem;
    }
    #lmb-report-select .page-link {
        position: relative;
        display: block;
        padding: .25rem .5rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    #lmb-report-select .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }
    #lmb-report-select .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
    }
    #lmb-report-select .page-item:last-child .page-link {
        border-top-right-radius: .25rem;
        border-bottom-right-radius: .25rem;
    }
    #lmb-report-select .justify-content-center {
        -ms-flex-pack: center !important;
        justify-content: center !important;
    }

    #lmb-report-select hr {
        margin: 1rem 0;
        color: inherit;
        background-color: currentColor;
        border: 0;
        opacity: .25;
    }
</style>

<style>
    #lmb-report-select-table tr,
    #lmb-report-select-table td {
        cursor: pointer;
    }
</style>

<script src="<?=$umgvar['url']?>/extra/report/report_select/report_select.js?v=<?=$umgvar['version']?>"></script>

<div id="lmb-report-select" data-gtabid="<?=$gtabid?>" title="<?=$lang[1502]?>" style="display: none">

    <div id="lmb-report-select-list">

        <div class="input-group input-group-sm mb-3">
            <input type="text" class="form-control form-control-sm" id="lmb-report-select-search">
            <div class="input-group-append d-none">
                <span class="input-group-text"><i class="lmb-icon lmb-undo" id="lmb-report-select-search-reset"></i></span>
            </div>
        </div>
    
        <div class="table-responsive" style="min-height: 315px">
            <table class="table table-striped table-hover" id="lmb-report-select-table">
                
                <?php foreach ($reportlist as $report) : ?>
                
                <?php include ('report_select_row.php'); ?>
                
                <?php endforeach; ?>
    
            </table>
        </div>

        <div id="lmb-report-select-pagination">
            <?php include('report_select_pagination.php'); ?>
        </div>
        
    </div>

    <div id="lmb-report-select-single" class="d-none">

        <div class="row">
            <div class="col-9">
                <a href="#" id="btn-back-to-report-list"><?=$lang[3086]?></a>
            </div>
            <div class="col-3">
                <label><input type="checkbox" value="1" id="lmb-ckb-report-preview"> <?=$lang[1500]?></label>
            </div>
        </div>
        
        <h3 id="lmb-report-select-name"></h3>

        <div id="lmb-report-resolve">

        </div>
        
    </div>
    

</div>
