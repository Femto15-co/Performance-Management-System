var from = $("#datepicker1").datepicker({dateFormat: 'yy-mm-dd', maxDate: "D"});
var to = $("#datepicker2").datepicker({dateFormat: 'yy-mm-dd', maxDate: "D"});
var dateFormat = "yy-mm-dd";

from.on('change', function () {
    to.datepicker("option", "minDate", getDate(this));
    filter();
});

to.on('change', function () {
    from.datepicker("option", "maxDate", getDate(this));
    filter();
});

$('#project_name').on('change', function () {
    filter();
});

$('#user_name').on('change', function () {
    filter();
});

function filter() {
    datatables.ajax.url(dataTableRoute +"?" +$("form").serialize()).load();
}

function getDate(element) {
    var date;
    try {
        date = $.datepicker.parseDate(dateFormat, element.value);
    } catch (error) {
        date = null;
    }

    return date;
}

$('#data').on('xhr.dt', function ( e, settings, json, xhr ) {
    $('#total').html("Total spent Time: " + json.total_duration + " hr");
    });