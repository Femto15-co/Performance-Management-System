//Initialize datatables globally
var datatables;
$(document).ready(function() {

    //Datatables config object
    var dataTableConfig = {
        processing: true,
        serverSide: true,
        ajax: {
            "url": dataTableRoute
        },
        fixedHeader: true,
        responsive: true,
        "order": [
            [0, "desc"]
        ],
        "pageLength": 10,
        "responsive": true,
    };

    //Init datatables
    datatables=$('.dataTable').DataTable(dataTableConfig);

    //Throw errors instead of displaying them.
    $.fn.dataTable.ext.errMode = 'throw';

});