
$(function () {
    $('.accordion-collapse').on('show.bs.collapse', onAccordionOpen);
});

function onAccordionOpen() {

    let $this = $(this);
    
    if($this.data('loaded')) {
        return;
    }

    $this.data('loaded', true);

    let id = $(this).data('id');

    $('#table' + id).DataTable({
        processing: true,
        serverSide: true,
        ajax: 'main_dyns_admin.php?actid=manageDataSync&action=getCache&id=' + id,
        paging: true,
        searching: false,
        ordering:  false
    });
    
}
