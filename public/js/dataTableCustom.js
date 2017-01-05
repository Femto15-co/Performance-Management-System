$(document).ready(function(){
    $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: dataTableRoute,
        fixedHeader:true,
        responsive: true,
        "order": [[ 0, "desc" ]],
        "pageLength": 10,
        "responsive": true,
    });

    //Throw errors instead of displaying them.
    $.fn.dataTable.ext.errMode = 'throw';
});