@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('rules.add_new_rule')}}</h1>
    <hr/>
    <form action="{{route('rule.store')}}" method="post">

        {{csrf_field()}}

        <div class="form-group {{($errors->has('rule'))?'has-error':''}}">
            <label for="rule">{{ trans('rules.rule_name') }}</label>
            <input type="text" class="form-control" name="rule" value="{{old('rule')}}" required>
            {!! $errors->first('rule', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('desc'))?'has-error':''}}">
            <label for="desc">{{ trans('rules.rule_description') }}</label>
            <textarea class="form-control" name="desc" required>{{old('desc')}}</textarea>
            {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('weight'))?'has-error':''}}">
            <label for="weight">{{ trans('rules.rule_weight') }}</label>
            <select class="form-control" name="weight">
                @for ($i = 1; $i <= 10; $i++)
                    @if(old('weight') == $i)
                        <option value="{{ $i }}" selected>{{ $i }}</option>
                    @else
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endif
                @endfor
            </select>
            {!! $errors->first('weight', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group {{($errors->has('etype'))?'has-error':''}}">
            <label for="etype">{{ trans('rules.employee_type') }}</label>
            <select class="form-control" name="etype">
                @foreach($employeeTypes as $type)
                    @if(old('etype') == $type->id)
                        <option value="{{ $type->id }}" selected>{{ $type->type }}</option>
                    @else
                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                    @endif
                @endforeach
            </select>
            {!! $errors->first('etype', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.create')}}</button>
            </div>
        </div>

    </form>


</div>
@endsection