@extends('layouts.app')
@section('extra-css')
<link rel="stylesheet" href="{{asset('/css/star-rating.min.css')}}">
@stop
@section('content')
<div class="container">

    <h1>{{trans('reports.edit_report', ['id'=>$id])}}</h1>
    <hr/>
    <form class="" action="{{route('report.update', $id)}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="_method" value="put">

        @foreach($reportWithScores->scores as $ruleScore)
            <div class="row margin-bottom-md rule-block">
                <div class="form-group {{ $errors->has('scores.'.$counter) ? 'has-error' : ''}} clearfix no-margin-bottom">
                    <label for="employee" class="col-xs-12 col-md-7 control-label text-left">{{$ruleScore->rule}}</label>
                    <input type="hidden" name="rules[]" value="{{$ruleScore->id}}"/>
                    <div class="col-xs-12  col-md-5">
                        <input type="number" name="scores[]" value="{{$ruleScore->pivot->score}}" class="stars" data-show-clear="false"/>
                        {!! $errors->first('scores.'.$counter++, '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="rule-description col-xs-12 col-md-7 clearfix">
                    {{$ruleScore->desc}}
                </div>
            </div>
        @endforeach
        <div class="form-group">
            <label for="comment">{{ trans('general.write_comment') }}</label>
            <textarea name="comment" id="comment" class="form-control" rows="3">{{ 
                $comment ? $comment : "" }}</textarea>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.update')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection
@section('extra-js')
    <script type="text/javascript" src="{{asset('/js/star-rating.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/report.js')}}"></script>
@stop