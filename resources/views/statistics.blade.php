@extends('layouts.app')
@section('content')
<div class="container">
	<h1>{{trans('statistics.monthly')}} {{trans('statistics.title')}}</h1>
    <hr/>
	<div class="form-group">
		<label for="">{{trans('general.month')}}:</label>
		<input type="text" name="month" id="month" class="form-control date" value="{{date('m-Y')}}" required="required" title="">
	</div>
	<table class="table table-condensed table-bordered table-striped table-hover text-center">
		<thead>
			<tr>
				<th class="text-lg">{{trans('bonuses.title')}}</th>
				<th class="text-lg">{{trans('defects.title')}}</th>
				<th class="text-lg">{{trans('statistics.performance_score')}}</th>
			</tr>

		</thead>
		<tbody>
			<tr>
				<td id="bonus" class="text-success text-lg"></td>
				<td id="defect" class="text-danger text-lg"></td>
				<td id="performance" class="text-primary text-lg"></td>
			</tr>
		</tbody>
	</table>
    
</div>
@stop
@section('extra-js')
	<script type="text/javascript">
		getStatisticsUrl="{{route('statistics.get')}}";
	</script>

	<script type="text/javascript" src="{{asset('js/statistics.js')}}"></script>
@stop
@section('packages')
	@include('packages.datepicker')	
@stop