@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>
            @section('add_title')
                Edit Project
            @show
        </h1>
        <hr/>
        <form action="{{route('project.update', $id)}}" method="post">

            {{csrf_field()}}

            <input type="hidden" name="_method" value="put">

            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" name="name" value="{{ $project->name }}" required><br><br>
                <label for="desc">Description</label>
                <textarea class="form-control" name="desc" required>{{ $project->description }}</textarea><br><br>
                <label for="status">Status</label>
                <select class="form-control" name="status">
                    @if ($project->status)
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    @else
                        <option value="1">Active</option>
                        <option value="0" selected>Inactive</option>
                    @endif
                </select><br><br>
            </div>
            <input type="submit" value="Save" class="btn btn-success"><br>

        </form>
        <br><br>

    </div>
@endsection