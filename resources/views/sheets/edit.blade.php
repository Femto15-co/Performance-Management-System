@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>
            @section('add_title')
                Edit Sheet
            @show
        </h1>
        <hr/>
        <form action="{{route('sheet.update', $id)}}" method="post">

            {{csrf_field()}}

            <input type="hidden" name="_method" value="put">

            <div class="form-group">
                <label for="date">Date</label>
                <input class="form-control" name="date" value="{{ $sheet->date }}" required><br><br>
                <label for="duration">Duration</label>
                <input class="form-control" name="duration" value="{{ $sheet->duration }}" required><br><br>
                <label for="desc">Description</label>
                <textarea class="form-control" name="desc">{{ $sheet->description }}</textarea><br><br>
                <label for="project">Project</label>
                <select class="form-control" name="project">
                    @foreach($projects as $project)
                        @if($sheet->project_id == $project->id)
                        <option value="{{ $project->id }}" selected>{{ $project->name }}</option>
                        @else
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endif
                    @endforeach
                </select><br><br>
            </div>
            <input type="submit" value="Add" class="btn btn-success"><br>

        </form>
        <br><br>

    </div>
@endsection