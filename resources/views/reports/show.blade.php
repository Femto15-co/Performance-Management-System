@extends('layouts.app')

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
                            <th>
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
                            <td>{{isset($reviewersScores[$rule->id][$reviewer->id])?$reviewersScores[$rule->id][$reviewer->id]:trans('general.not_applicable')}}</td>
                        @endforeach
                        {{--Show final score if ready--}}
                        @if($avgScores)
                            <td>{{number_format($avgScores[$rule->id][0]->avg_score,2)}}</td>
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
@endsection