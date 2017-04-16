@extends('layouts.app')
@section('content')
<div class="container">
	<h1>{{trans('general.edit')}} {{trans('bonuses.title')}}</h1>
    <hr/>
	<form action="{{route('bonus.update',[$userId,$bonus->id])}}" method="POST" role="form">
	{{csrf_field()}}
	{{method_field('PUT')}}
		<div class="form-group {{($errors->has('description'))?'has-error':''}}">
			<label for="">{{trans('general.description')}}:</label>

			<textarea name="description" class="form-control">@if (old('description')){{old('description')}}@elseif (isset($bonus->description)){{$bonus->description}}@endif</textarea>
			{!! $errors->first('description', '<p class="help-block">:message</p>') !!}
		</div>
		<div class="form-group {{($errors->has('value'))?'has-error':''}}">
			<label for="">{{trans('general.value')}}:</label>
			<input type="number" name="value" class="form-control" value="@if (old('value')){{old('value')}}@elseif (isset($bonus->value)){{$bonus->value}}@endif">
			{!! $errors->first('value', '<p class="help-block">:message</p>') !!}
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop