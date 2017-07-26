@extends('layouts.app')
@section('content')
<div class="container">
	<h1>{{trans('general.create')}} {{trans('defects.title')}}</h1>
    <hr/>
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
		<div class="form-group">
                <label for="comment">{{ trans('general.write_comment') }}</label>
                <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
        </div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop

@section('packages')
	@include('packages.select2')
@stop