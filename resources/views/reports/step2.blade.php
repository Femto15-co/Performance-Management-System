@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Add New Report: Step 2</h1>
    <hr/>
    <form class="form-horizontal" action="">
        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
            <label for="employee" class="col-sm-3 control-label">{{trans('reports.step1_choose_employee', ['Choose Employee'])}}</label>
            <div class="col-sm-6">

                {!! $errors->first('employee', '<p class="help-block">:message</p>') !!}
            </div>
        </div>



        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" class="btn btn-primary form-control">{{trans('general.create')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection