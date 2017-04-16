<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
<script type="text/javascript" src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('input.date').datepicker({
			format: "mm-yyyy",
		    startView: "months", 
		    minViewMode: "months",
		    autoclose:true,
		});
	});
</script>