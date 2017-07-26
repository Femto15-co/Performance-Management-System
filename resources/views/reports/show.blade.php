@extends('layouts.app')
@section('extra-css')
<link rel="stylesheet" href="{{asset('/css/star-rating.min.css')}}">
@stop
@section('content')
    <div class="container">

        <h1>{{trans('reports.view_report', ['id'=>$id])}}</h1>
        <hr/>

        <!-- Table which displays reviewers information -->
        <div class="table">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        {{--Get reviewers names--}}
                        <th>

                        </th>
                        @foreach($reviewers as $reviewer)
                            <th class="text-center">
                                {{$reviewer->name}}
                            </th>
                        @endforeach
                        {{--Show final score if ready--}}
                        @if($avgScores)
                            <th>
                                {{trans('reports.final_score')}}
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @foreach($rules as $rule)
                    <tr>
                        <th class="col-md-3">
                            {{$rule->rule}}
                            <span class="performance-desc">{{$rule->desc}}</span>
                        </th>
                        @foreach($reviewers as $reviewer)
                            <td class="text-center" style="vertical-align: middle;"><input class="starz" value="{{isset($reviewersScores[$rule->id][$reviewer->id])?$reviewersScores[$rule->id][$reviewer->id]:trans('general.not_applicable')}}"></input></td>
                        @endforeach
                        {{--Show final score if ready--}}
                        @if($avgScores)
                            <td class="text-center" style="vertical-align: middle;">{{number_format($avgScores[$rule->id][0]->avg_score,2)}}</td>
                        @endif
                    </tr>
                @endforeach
                <th class="col-md-3">
                    {{trans('reports.result')}}
                </th>
                <td colspan='3' class="text-center">{{trans('reports.scoreOf',['x'=>$report->overall_score,'y'=>$report->max_score])}}</td>

                </tbody>
            </table>
        </div>
        @if (count($comments) > 0)
            @foreach ($comments as $comment)
                <div class="form-group">
                        <p title="{{(new Carbon\Carbon($comment->created_at))
                            ->format('l, d M Y, h:i:s A')}}">
                            <strong>{{ $comment->user->name }}</strong> Wrote</p>
                        <blockquote>
                        <p class="lead">{{$comment->comment}}</p>
                        </blockquote>
                </div>
            @endforeach
        @endif
        

@endsection
@section('extra-js')
    <script type="text/javascript" src="{{asset('/js/star-rating.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/report.js')}}"></script>
@stop