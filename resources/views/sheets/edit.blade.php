@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('sheets.edit_sheet', ['id'=>$id])}}</h1>
    <hr/>
    <form action="{{route('sheet.update', $id)}}" method="post">

        {{csrf_field()}}

        <input type="hidden" name="_method" value="put">

        <div class="form-group {{($errors->has('date'))?'has-error':''}}">
            <label for="date">{{trans('general.date')}}</label>
            <input class="form-control" name="date" value="{{ $sheet->date }}" required>
            {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('duration'))?'has-error':''}}">
            <label for="duration">{{trans('sheets.sheet_duration')}}</label>
            <input class="form-control" name="duration" value="{{ $sheet->duration }}" required>
            {!! $errors->first('duration', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('desc'))?'has-error':''}}">
            <label for="desc">{{trans('sheets.description')}}</label>
            <textarea class="form-control" name="desc">{{ $sheet->description }}</textarea>
            {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('project'))?'has-error':''}}">
            <label for="project">{{trans('projects.project_name')}}</label>
            <select class="form-control" name="project">
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{($sheet->project_id == $project->id)? "selected": ""}}>{{ $project->name }}</option>
                @endforeach
            </select>
            {!! $errors->first('project', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.update')}}</button>
            </div>
        </div>
    </form>


</div>
@endsection