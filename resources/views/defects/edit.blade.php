@extends('layouts.app')
@section('content')
<div class="container">
	<h1>{{trans('general.edit')}} {{trans('defects.title')}}</h1>
    <hr/>
	<form action="{{route('defect.update',[$userId,$defectAttachmentId])}}" method="POST" role="form">
	{{csrf_field()}}
	{{ method_field('PUT') }}
		<div class="form-group">
			<label for="">Defect Type:</label>
			<select class="form-control" id="defect" name="defect">
				@foreach ($defects as $defect)
				<option value="{{$defect->id}}" {{($defect->id==$selectedDefect)?'selected':''}}>{{$defect->title}}</option>
				@endforeach
			</select>
			{!! $errors->first('defect', '<p class="help-block">:message</p>') !!}
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop