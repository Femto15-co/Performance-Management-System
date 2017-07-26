@extends('layouts.app')
@section('extra-css')
<link rel="stylesheet" href="{{asset('/css/star-rating.min.css')}}">
@stop
@section('content')
<div class="container">

    <h1>
        @section('add_title')
            {{isset($title)? $title: trans('reports.step2_add_report')}}
        @show
    </h1>
    <hr/>
    <form class="" action="{{isset($route)? $route: route('report.store')}}" method="post">
        {{csrf_field()}}

        @if(isset($method_field))
            {{$method_field}}
        @endif

        @if(isset($employee))
            <input type="hidden" name="employee" value="{{$employee}}">
        @endif

        @foreach($performanceRules as $rule)
            <div class="row margin-bottom-md rule-block">
                <div class="form-group {{ $errors->has('scores.'.$counter) ? 'has-error' : ''}} clearfix no-margin-bottom">
                    <label for="employee" class="col-xs-12 col-md-7 control-label text-left">{{$rule->rule}}</label>
                    <input type="hidden" name="rules[]" value="{{$rule->id}}"/>
                    <div class="col-xs-12  col-md-5">
                        <input type="number" name="scores[]" value="" class="stars" data-show-clear="false"/>
                        {!! $errors->first('scores.'.$counter++, '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="rule-description col-xs-12 col-sm-7 clearfix">
                    {{$rule->desc}}
                </div>
            </div>
        @endforeach
        @role ('admin')
            <div class="form-group">
                <label for="comment">{{ trans('general.write_comment') }}</label>
                <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
            </div>
        @endrole
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{isset($buttonText)? $buttonText: trans('general.create')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection
@section('extra-js')
    <script type="text/javascript" src="{{asset('/js/star-rating.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/report.js')}}"></script>
@stop