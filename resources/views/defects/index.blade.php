@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h1>@role ('admin')
        {{$user->name}}'s 
        @endrole{{ trans('defects.title') }}</h1>
        <hr/>

    </div>

    <div class="row margin-bottom-md">
        @role ('admin')
        <!-- Add New Room Button -->
        <a href="{{route('defect.create',['userId'=>$user->id])}}">
            <button type="button" class="btn btn-primary" >
                <span class="glyphicon glyphicon-plus"></span>{{ trans('defects.add_new_defect') }}
            </button>
        </a>
        @endrole
    </div>



    <!-- The which display the all data of Expenses -->
    <div class="row margin-bottom-md">
        <table id="data" width="100%"  class="table direction table-bordered table-striped dataTable text-center">
            <thead>
            <tr>
                <th >{{ trans('defects.defect_id') }}</th>
                <th >{{ trans('users.employee_name') }}</th>
                <th >{{ trans('defects.score') }}</th>
                <th >{{ trans('general.date') }}</th>
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



</div>
@endsection