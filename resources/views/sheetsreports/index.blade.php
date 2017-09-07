@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>{{ trans('sheets.reports') }}</h1>
                <hr/>
            </div>
        </div>

        <div class="row margin-bottom-md">
            <form >
            <div class="col-xs-3 form-group">
                <label for="from">{{trans('sheets.from')}}</label>
                <input class="form-control" id="datepicker1" name="from">
            </div>

            <div class="col-xs-3 form-group">
                <label for="to">{{trans('sheets.to')}}</label>
                <input class="form-control" id="datepicker2" name="to">
            </div>

            <div class="col-xs-3 form-group">
                <label for="project_name">{{trans('projects.project_name')}}</label>
                <select class="form-control" id="project_name" name="project_name">
                    <option selected value> -- select an option --</option>
                    @foreach($projects as $project)
                        <option value="{{$project->id}}">{{$project->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xs-3 form-group">
                <label for="user_name">{{trans('users.employee_name')}}</label>
                <select class="form-control" id="user_name" name="user_name">
                    <option selected value> -- select an option --</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            </form>
        </div>

        <!-- The which display the all data of Expenses -->
        <table id="data" class="table direction table-bordered table-striped dataTable text-center">
            <thead>
            <tr>
                <th style="width: 15%">{{ trans('general.date') }}</th>
                <th style="width: 20%">{{ trans('users.employee_name') }}</th>
                <th style="width: 10%">{{ trans('sheets.sheet_duration') }}</th>
                <th style="width: 25%">{{ trans('projects.project_name') }}</th>
                <th style="width: 10%" data-sortable="false"
                    data-searchable="false">{{ trans('projects.project_status') }}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <p id="total"></p>

        <!-- Back to Master page Button -->
        <a href="/">
            <button type="button" class="btn btn-primary"><span
                        class="glyphicon glyphicon-home"></span>{{ trans('general.main_page') }} </button>
        </a>


    </div>

@endsection

@section('extra-css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection


@section('extra-js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="{{asset('js/timeSheets.js')}}"></script>
@endsection

