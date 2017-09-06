@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>{{ trans('sheets.sheets') }}</h1>
                <hr/>
            </div>
        </div>

        <div class="row margin-bottom-md">
            <div class="col-xs-12">
                <!-- Add New Room Button -->
                <a href="{{ route('sheet.create') }}">
                    <button type="button" class="btn btn-primary" >
                        <span class="glyphicon glyphicon-plus"></span>{{ trans('sheets.add_new_sheet') }}
                    </button>
                </a>
            </div>
        </div>

        <!-- The which display the all data of Expenses -->
        <table id="data" class="table direction table-bordered table-striped dataTable text-center">
            <thead>
            <tr>
                <th >{{ trans('sheets.sheet_id') }}</th>
                <th >{{ trans('general.date') }}</th>
                @role ('admin')
                    <th >{{ trans('users.employee_name') }}</th>
                @endrole
                <th style="width: 10%">{{ trans('sheets.sheet_duration') }}</th>
                <th >{{ trans('projects.project_name') }}</th>
                <th data-sortable="false" data-searchable="false">{{ trans('general.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <!-- Back to Master page Button -->
        <a href="/">
            <button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-home"></span>{{ trans('general.main_page') }} </button>
        </a>




    </div>
@endsection
