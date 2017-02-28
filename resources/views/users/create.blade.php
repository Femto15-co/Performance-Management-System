@extends('layouts.app')
@section('content')
<div class="container">
	<h1>{{trans('general.create')}} {{trans('bonuses.title')}}</h1>
    <hr/>
	<form action="{{route('user.store')}}" method="POST" role="form">
	{{csrf_field()}}
		<div class="form-group {{($errors->has('name'))?'has-error':''}}">
			<label for="">{{trans('general.name')}}:</label>
			<input type="text" name="name" class="form-control" value="{{old('name')}}">
			{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
		</div>
		<div class="form-group {{($errors->has('email'))?'has-error':''}}">
			<label for="">{{trans('general.email')}}:</label>
			<input type="email" name="email" class="form-control" value="{{old('email')}}">
			{!! $errors->first('email', '<p class="help-block">:message</p>') !!}
		</div>
		<div class="form-group {{($errors->has('password'))?'has-error':''}}">
			<label for="">{{trans('general.password')}}:</label>
			<input type="password" name="password" class="form-control" value="{{old('password')}}">
			{!! $errors->first('password', '<p class="help-block">:message</p>') !!}
		</div>
		<div class="form-group {{($errors->has('role'))?'has-error':''}}">
			<label for="">{{trans('users.role')}}:</label>
			<select class="form-control" id="employee_type" name="employee_type">
				@foreach ($roles as $role)
				<option value="{{$role->id}}">{{$role->type}}</option>
				@endforeach
			</select>
			{!! $errors->first('role', '<p class="help-block">:message</p>') !!}
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop