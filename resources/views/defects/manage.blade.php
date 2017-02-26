@extends('layouts.app')
@section('content')
<div class="container">
	<form action="{{route('defect.store',[$userId])}}" method="POST" role="form">
	{{csrf_field()}}
		<div class="form-group">
			<label for="">Defect Type:</label>
			<select class="form-control" id="defect" name="defect">
				@foreach ($defects as $defect)
				<option value="{{$defect->id}}">{{$defect->title}}</option>
				@endforeach
			</select>
			{!! $errors->first('defect', '<p class="help-block">:message</p>') !!}
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop