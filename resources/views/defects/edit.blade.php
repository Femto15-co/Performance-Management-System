@extends('layouts.app')
@section('extra-css')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@stop
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
		<div class="form-group">
                <label for="comment">{{ trans('general.write_comment') }}</label>
                <textarea name="comment" id="comment" class="form-control" rows="3">{{
                	$comment}}</textarea>
        </div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
@stop
@section('extra-js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script src="{{asset('js/select2.init.js')}}"></script>
@stop