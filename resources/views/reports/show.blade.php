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
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
@endsection