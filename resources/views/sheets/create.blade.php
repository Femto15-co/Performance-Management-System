@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('sheets.add_new_sheet')}}</h1>
    <hr/>
    <form action="{{route('sheet.store')}}" method="post">

        {{csrf_field()}}

        <div class="form-group {{($errors->has('date'))?'has-error':''}}">
            <label for="date">{{trans('general.date')}}</label>
            <input class="form-control" id="datepicker" name="date" value="{{old('date')}}" required>
            {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('duration'))?'has-error':''}}">
            <label for="duration">{{trans('sheets.sheet_duration')}}</label>
            <input class="form-control" name="duration" value="{{old('duration')}}" required>
            {!! $errors->first('duration', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('desc'))?'has-error':''}}">
            <label for="desc">{{trans('sheets.sheet_description')}}</label>
            <textarea class="form-control" name="desc">{{old('desc')}}</textarea>
            {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('project'))?'has-error':''}}">
            <label for="project">{{trans('projects.project_name')}}</label>
            <select class="form-control" name="project">
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{(old('project') == $project->id)? "selected": ""}}>{{ $project->name }}</option>
                @endforeach
            </select>
            {!! $errors->first('project', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.create')}}</button>
            </div>
        </div>

    </form>


</div>
@endsection

@section('extra-css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection


@section('extra-js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
        } );
    </script>
@endsection

