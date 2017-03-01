$('input.date').on('changeDate', function() {
		$('td#bonus').text('Loading...');
        $('td#defect').text('Loading...');
        $('td#performance').text('Loading...');
        $.post(getStatisticsUrl, {month: $('input.date').datepicker('getFormattedDate'),_token:window.Laravel.csrfToken}, function(data) {
        	//Update statistics
        	$('td#bonus').text(data[0]);
        	$('td#defect').text(data[1]);
        	$('td#performance').text(data[2]);
        });
});