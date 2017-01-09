@extends('layouts.app')

@section('content')
<div class="container">

    <h1>{{trans('reports.step1_add_report')}}</h1>
    <hr/>
    <form class="form-horizontal" >
        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
            <label for="employee" class="col-sm-3 control-label">{{trans('reports.step1_choose_employee')}}</label>
            <div class="col-sm-6">
                <select name="employee" id="select-employee" class="form-control">
                    @foreach($employees as $employee)
                        <option value="{{$employee->id}}">{{$employee->name}}</option>
                    @endforeach
                </select>
                {!! $errors->first('employee', '<p class="help-block">:message</p>') !!}
            </div>
        </div>



        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                <button type="submit" id="submit-employee" class="btn btn-primary form-control">{{trans('general.create')}}</button>
            </div>
        </div>
    </form>



</div>
@endsection

@section('extra-js')
    <script>
        $('#submit-employee').click(function(e){
            //Stop form submission
            e.preventDefault();

            //Get selected employee
            var selectEmployee = $('#select-employee').val();
            //TODO Raise error if not employee selected

            //Redirect to step 2
            var stepTwoURL = "{{route('report.create.step2', ':id')}}";
            window.location.href = stepTwoURL.replace(':id', selectEmployee);

        });
    </script>

@stop