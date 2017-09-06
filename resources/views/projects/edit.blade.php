@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('projects.edit_project')}}</h1>
    <hr/>
    <form action="{{route('project.update', $id)}}" method="post">

        {{csrf_field()}}

        <input type="hidden" name="_method" value="put">

        <div class="form-group {{($errors->has('name'))?'has-error':''}}">
            <label for="name">{{ trans('projects.project_name') }}</label>
            <input class="form-control" name="name" value="{{ $project->name }}" required>
            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('desc'))?'has-error':''}}">
            <label for="desc">{{ trans('projects.project_description') }}</label>
            <textarea class="form-control" name="desc" required>{{ $project->description }}</textarea>
            {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('status'))?'has-error':''}}">
            <label for="status">{{ trans('projects.project_status') }}</label>
            <select class="form-control" name="status">
                <option value="1" {{($project->status == 1)? "selected":"" }}>{{ trans('projects.active') }}</option>
                <option value="0" {{($project->status == 0)? "selected":"" }}>{{ trans('projects.inactive') }}</option>
            </select>
            {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.update')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection