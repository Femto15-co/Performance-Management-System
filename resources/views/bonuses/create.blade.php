@extends('layouts.app')
@section('content')
<div class="container">
	<h1>{{trans('general.create')}} {{trans('bonuses.title')}}</h1>
    <hr/>
	<form action="{{route('bonus.store',[$userId])}}" method="POST" role="form">
	{{csrf_field()}}
		<div class="form-group {{($errors->has('description'))?'has-error':''}}">
			<label for="">{{trans('general.description')}}:</label>
			<textarea name="description" class="form-control">{{old('description')}}</textarea>
			{!! $errors->first('description', '<p class="help-block">:message</p>') !!}
		</div>
		<div class="form-group {{($errors->has('value'))?'has-error':''}}">
			<label for="">{{trans('general.value')}}:</label>
			<input type="number" name="value" class="form-control" value="{{old('value')}}">
			{!! $errors->first('value', '<p class="help-block">:message</p>') !!}
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop