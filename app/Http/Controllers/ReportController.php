<?php

namespace App\Http\Controllers;

use App\PerformanceRule;
use Illuminate\Http\Request;
use App\User;
use App\Report;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //Include DataTable
        $includeDataTable = true;

        //DataTable ajax route
        $dataTableRoute = route('report.list');

        return view('reports.index', compact('includeDataTable', 'dataTableRoute'));
    }

    /**
     * Show step1 form for creating a new report.
     *
     * @return \Illuminate\Http\Response HTML view
     */
    public function create()
    {
        // Step 1 for Admin only, Choose employee to rate
        //Get All users with role employee
        $employees = User::whereHas('roles', function($q){
            $q->where('name','=','employee');
        })->get();

        if($employees->isEmpty())
        {
            //in no employees, redirect to reports index and show error message
            Session::flash('alert',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        return view('reports.step1', compact(['employees']));
    }

    /**
     * Show step2 form for creating a new report.
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response HTML View
     */

    public function createStepTwo($id)
    {
        //if employee not passed from step1, abort
        if(!$id)
        {
            //redirect to reports index and show no error message
            return redirect(route('report.create.step1'));
        }

        //Get employee type
        $employee = User::find($id);
        if(!$employee)
        {
            //in no employees, redirect to reports index and show error message
            Session::flash('alert',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        //load rules based on employee type
        $performanceRules = PerformanceRule::where('employee_type', $employee->employee_type)->get();

        if($performanceRules->isEmpty())
        {
            //in no rules, redirect to reports index and show error message
            Session::flash('alert',trans('reports.no_rules'));
            return redirect(route('report.index'));
        }

        //Add employee to session to be merge to step2 request
        Session::put('employee_id', $employee->id);

        //Pass Counter to view
        $counter = 0;
        return view('reports.step2', compact(['performanceRules','counter']))->with('employee',$employee->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate Request
        $this->validateReport($request);

        //if session has employer id passed proceed, otherwise abort
        if(!Session::has('employee_id'))
        {
            //redirect to reports index and show no error message
            return redirect(route('report.index'));
        }

        //Get employee
        $employee = User::find(Session::get('employee_id'));

        //Destroy session
        Session::forget('employee_id');

        if(!$employee)
        {
            //in no employees, redirect to reports index and show error message
            Session::flash('alert',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        //Create Report
        $report = Report::create(['user_id'=>$employee->id]);
        if(!$report)
        {
            //if not created, redirect to reports index and show error message
            Session::flash('alert',trans('reports.not_created'));
            return redirect(route('report.index'));
        }

        $scores = $request->input('scores');
        $i = 0;

        foreach($request->input('rules') as $ruleId)
        {
            //If no score is paired with that rule, abort
            if(!isset($scores[$i]))
                break;

            //Check first that not record contains same reportId, reviewerId and ruleId
            $foundDuplicate = $report->scores()->where('rule_id',$ruleId)->where('reviewer_id', \Auth::user()->id)->count();
            if(!$foundDuplicate)
            {
                $report->scores()->attach([$ruleId => ['reviewer_id'=>\Auth::user()->id, 'score'=>$scores[$i++]]]);
            }
        }

        //redirect to reports index and show no error message
        //TODO Redirect to view
        Session::flash('success',trans('reports.created_first'));
        return redirect(route('report.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Validate input request
     * @param Request $request
     */

    public function validateReport(Request $request)
    {
        // Some defined rules that has to be achieved
        $rules = [
            'scores.*' => 'required|digits_between:1,10',
            'rules.*'  => 'required|exists:performance_rules,id'
        ];

        // Run the validator on request data
        $this->validate($request, $rules);
    }

    /**
     * Returns reports data to DataTable
     *
     * @return JSON
     */
    public function listData()
    {
      $reports = Report::join('users', 'reports.user_id', '=', 'users.id')
            ->select(['reports.id', 'users.name', 'reports.overall_score', 'reports.created_at']);

        return Datatables::of($reports)
            ->addColumn('action', function ($reports) {

                $formHead = "<form class='form-horizontal main_form' method='POST' action='".route('report.destroy',$reports->id)."'>".csrf_field();
                $viewLink = "<a href=".route('report.show',$reports->id)." class='btn btn-xs btn-success'><i class='glyphicon glyphicon-view'></i> ".trans('general.show')."</a>&nbsp;";
                $editLink = "<a href=".route('report.edit',$reports->id)." class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>".trans('general.edit')."</a>";
                $deleteForm =
                    "  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-delete'></i> ".trans('general.delete')."
                        </button>
                    </form>";

                return $formHead . $viewLink . $editLink . $deleteForm;

            }) //Change the Format of report date
            ->editColumn('created_at', function ($reports) {
                return date('d M Y', strtotime($reports->created_at));
            }) // To Update the Offdays Section and Convert it to String
            ->make();
    }

}
