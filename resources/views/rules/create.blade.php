@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>
            @section('add_title')
                Add new Performance rule
            @show
        </h1>
        <hr/>
        <form action="{{route('rule.store')}}" method="post">

            {{csrf_field()}}
            <div class="form-group">
                <label for="rule">Rule</label>
                <input type="text" class="form-control" name="rule" required><br><br>
                <label for="desc">Description</label>
                <textarea class="form-control" name="desc" required></textarea><br><br>
                <label for="weight">Weight</label>
                <select class="form-control" name="weight">
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select><br><br>
                <label for="etype">Employee type</label>
                <select class="form-control" name="etype">
                    @foreach($employeeTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                    @endforeach
                </select><br><br>
            </div>
            <input type="submit" value="Add" class="btn btn-success"><br>

        </form>
        <br><br>

    </div>
@endsection