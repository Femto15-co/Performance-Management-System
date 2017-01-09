@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('reports.step2_add_report')}}</h1>
    <hr/>
    <form class="" action="{{route('report.store')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="employee" value="{{$employee}}">
        @foreach($performanceRules as $rule)
            <div class="row margin-bottom-md rule-block">
                <div class="form-group {{ $errors->has('scores.'.$counter) ? 'has-error' : ''}} clearfix no-margin-bottom">
                    <label for="employee" class="col-sm-9 control-label text-left">{{$rule->rule}}</label>
                    <input type="hidden" name="rules[]" value="{{$rule->id}}"/>
                    <div class="col-sm-3">
                        <input type="number" name="scores[]" value=""/>
                        {!! $errors->first('scores.'.$counter++, '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="rule-description col-sm-9 clearfix">
                    {{$rule->desc}}
                </div>
            </div>
        @endforeach

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.create')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection