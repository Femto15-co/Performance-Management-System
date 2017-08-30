@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>
            @section('add_title')
                Add new Sheet
            @show
        </h1>
        <hr/>
        <form action="{{route('sheet.store')}}" method="post">

            {{csrf_field()}}
            <div class="form-group">
                <label for="date">Date</label>
                <input class="form-control" id="datepicker" name="date" required><br><br>
                <label for="duration">Duration</label>
                <input class="form-control" name="duration" required><br><br>
                <label for="desc">Description</label>
                <textarea class="form-control" name="desc"></textarea><br><br>
                <label for="project">Project</label>
                <select class="form-control" name="project">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select><br><br>
            </div>
            <input type="submit" value="Add" class="btn btn-success"><br>

        </form>
        <br><br>

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

