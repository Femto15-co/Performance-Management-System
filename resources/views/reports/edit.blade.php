@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('reports.edit_report', ['id'=>$id])}}</h1>
    <hr/>
    <form class="" action="{{route('report.update', $id)}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="_method" value="put">

        @foreach($ruleScores as $ruleScore)
            <div class="row margin-bottom-md rule-block">
                <div class="form-group {{ $errors->has('scores.'.$counter) ? 'has-error' : ''}} clearfix no-margin-bottom">
                    <label for="employee" class="col-sm-9 control-label text-left">{{$ruleScore->rule}}</label>
                    <input type="hidden" name="rules[]" value="{{$ruleScore->id}}"/>
                    <div class="col-sm-3">
                        <input type="number" name="scores[]" value="{{$ruleScore->pivot->score}}"/>
                        {!! $errors->first('scores.'.$counter++, '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="rule-description col-sm-9 clearfix">
                    {{$ruleScore->desc}}
                </div>
            </div>
        @endforeach

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.update')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection