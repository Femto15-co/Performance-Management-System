@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>{{ trans('rules.rule') }}</h1>
                <hr/>
            </div>
        </div>

        <div class="row margin-bottom-md">
            <div class="col-xs-12">
                @role ('admin')
                <!-- Add New Room Button -->
                <a href="{{ route('rule.create') }}">
                    <button type="button" class="btn btn-primary" >
                        <span class="glyphicon glyphicon-plus"></span>{{ trans('rules.add_new_rule') }}
                    </button>
                </a>
                @endrole
            </div>
        </div>



        <!-- The which display the all data of Expenses -->
        <table id="data" class="table direction table-bordered table-striped dataTable text-center">
            <thead>
            <tr>
                <th >{{ trans('rules.rule_id') }}</th>
                <th >{{ trans('rules.rule_name') }}</th>
                <th >{{ trans('rules.rule_weight') }}</th>
                <th >{{ trans('rules.employee_type') }}</th>
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
