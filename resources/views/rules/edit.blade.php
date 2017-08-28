@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>
            @section('add_title')
                Edit Performance Rule
            @show
        </h1>
        <hr/>
        <form action="{{route('rule.update', $id)}}" method="post">

            {{csrf_field()}}

            <input type="hidden" name="_method" value="put">

            <div class="form-group">
                <label for="rule">Rule</label>
                <input type="text" class="form-control" name="rule" value="{{ $rule->rule }}" required><br><br>
                <label for="desc">Description</label>
                <textarea class="form-control" name="desc" required>{{ $rule->desc }}</textarea><br><br>
                <label for="weight">Weight</label>
                <select class="form-control" name="weight">
                    @for ($i = 1; $i <= 10; $i++)
                        @if ($rule->weight == $i)
                            <option value="{{ $i }}" selected>{{ $i }}</option>
                        @else
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endif
                    @endfor
                </select><br><br>
                <label for="etype">Employee type</label>
                <select class="form-control" name="etype">
                    @foreach($employeeTypes as $type)
                        @if ($rule->employee_type == $type->id)
                            <option value="{{ $type->id }}" selected>{{ $type->type }}</option>
                        @else
                            <option value="{{ $type->id }}">{{ $type->type }}</option>
                        @endif
                    @endforeach
                </select><br><br>
            </div>
            <input type="submit" value="Save" class="btn btn-success"><br>

        </form>
        <br><br>

    </div>
@endsection