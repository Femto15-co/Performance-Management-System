@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('projects.add_new_project')}}</h1>
    <hr/>
    <form action="{{route('project.store')}}" method="post">

        {{csrf_field()}}

        <div class="form-group {{($errors->has('name'))?'has-error':''}}">
            <label for="name">{{ trans('projects.project_name') }}</label>
            <input class="form-control" name="name" value="{{old('name')}}" required>
            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('desc'))?'has-error':''}}">
            <label for="desc">{{ trans('projects.project_description') }}</label>
            <textarea class="form-control" name="desc" required>{{old('desc')}}</textarea>
            {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group" {{($errors->has('status'))?'has-error':''}}>
            <label for="status">{{ trans('projects.project_status') }}</label>
            <select class="form-control" name="status">
                <option value="1" {{(old('status') == 1)? "selected":"" }}>{{ trans('projects.active') }}</option>
                <option value="0" {{(old('status') == 0)? "selected":"" }}>{{ trans('projects.inactive') }}</option>
            </select>
            {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.create')}}</button>
            </div>
        </div>
    </form>

</div>
@endsection