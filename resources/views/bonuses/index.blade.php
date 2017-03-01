@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h1>@role ('admin')
        {{$user->name}}'s 
        @endrole{{ trans('bonuses.title') }}</h1>
        <hr/>

    </div>

    <div class="row margin-bottom-md">
    @role ('admin')
        <!-- Add New Room Button -->
        <a href="{{route('bonus.create',['userId'=>$user->id])}}">
            <button type="button" class="btn btn-primary" >
                <span class="glyphicon glyphicon-plus"></span>{{ trans('bonuses.add_new') }}
            </button>
        </a>
    @endrole
    </div>



    <!-- The which display the all data of Expenses -->
    <div class="row margin-bottom-md">
        <table id="data" width="100%"  class="table direction table-bordered table-striped dataTable text-center">
            <thead>
            <tr>
                <th >{{ trans('general.id') }}</th>
                <th >{{ trans('general.description') }}</th>
                <th >{{ trans('general.value') }}</th>
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