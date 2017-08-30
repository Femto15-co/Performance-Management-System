@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>
            @section('add_title')
                Add new Project
            @show
        </h1>
        <hr/>
        <form action="{{route('project.store')}}" method="post">

            {{csrf_field()}}
            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" name="name" required><br><br>
                <label for="desc">Description</label>
                <textarea class="form-control" name="desc" required></textarea><br><br>
                <label for="status">Status</label>
                <select class="form-control" name="status">
                    <option value="1" selected>Active</option>
                    <option value="0">Inactive</option>
                </select><br><br>
            </div>
            <input type="submit" value="Add" class="btn btn-success"><br>

        </form>
        <br><br>

    </div>
@endsection