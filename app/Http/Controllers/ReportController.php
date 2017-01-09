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
            Session::flash('error',trans('reports.no_employee'));
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
        //Get employee type
        $employee = User::find($id);
        if(!$employee)
        {
            //in no employees, redirect to reports index and show error message
            Session::flash('error',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        //Ensure that selected employee has employee rule
        if(strcmp($employee->roles()->first()->name,'employee') != 0)
        {
            //if not an employee, redirect to index
            Session::flash('error',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        //load rules based on employee type
        $performanceRules = PerformanceRule::where('employee_type', $employee->employee_type)->get();

        if($performanceRules->isEmpty())
        {
            //in no rules, redirect to reports index and show error message
            Session::flash('error',trans('reports.no_rules'));
            return redirect(route('report.index'));
        }

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

        //Get employee
        $employee = User::find($request->input('employee'));

        if(!$employee)
        {
            //if no employees, redirect to reports index and show error message
            Session::flash('error',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        //Ensure that selected employee has employee rule
        if(strcmp($employee->roles()->first()->name,'employee') != 0)
        {
            //if not an employee, redirect to index
            Session::flash('error',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }

        //Create Report
        $report = Report::create(['user_id'=>$employee->id]);
        if(!$report)
        {
            //if not created, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_created'));
            return redirect(route('report.index'));
        }

        $scores = $request->input('scores');
        $i = 0;

        foreach($request->input('rules') as $ruleId)
        {
            //If no score is paired with that rule, abort
            if(!isset($scores[$i]))
                break;

            //If no doesn't belong to selected employees related rules, ignore and conitue
            $validRule = PerformanceRule::where('employee_type', \Auth::user()->employee_type)->where('id', $ruleId)->exists();

            if(!$validRule)
                continue;

            //Check first that no record contains same reportId, reviewerId and ruleId
            $foundDuplicate = $report->scores()->where('rule_id',$ruleId)->where('reviewer_id', \Auth::user()->id)->count();
            if(!$foundDuplicate)
            {
                $report->scores()->attach([$ruleId => ['reviewer_id'=>\Auth::user()->id, 'score'=>$scores[$i++]]]);
            }
        }

        Session::flash('success',trans('reports.created_first'));
        return redirect(route('report.show', $report->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Report array
        $reviewersScores = array();

        //Unique reviewers
        $reviewers = array();

        //Unique Rules
        $rules = array();

        $report = Report::find($id);
        if(!$report)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //----- List Data in rules by reviewer matrix -----//
        $scores = $report->scores()->get();
        foreach($scores as $score)
        {
            $reviewersScores[$score->pivot->rule_id][$score->pivot->reviewer_id] = $score->pivot->score;

            //List all unique reviewers
            $reviewers[$score->pivot->reviewer_id] = User::find($score->pivot->reviewer_id);

            //List all unique rules
            $rules[$score->pivot->rule_id] = PerformanceRule::find($score->pivot->rule_id);
        }

        //if no rules, reviewers and reviwersScores register, abort
        if(empty($reviewersScores) && empty($rules) && empty($rules))
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        return view('reports.show', compact('reviewersScores', 'reviewers', 'rules', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = Report::find($id);

        if(!$report)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //Get scores recorded by authenticated user who attempted edit
        $ruleScores = $report->scores()->where('reviewer_id', \Auth::user()->id)->get();

        if($ruleScores->isEmpty())
        {
            //no scores recorded by user, redirect to reports index and show error message
            Session::flash('error',trans('reports.no_scores_recorded'));
            return redirect(route('report.index'));
        }
        
        //Pass Counter to view
        $counter = 0;
        return view('reports.edit', compact(['ruleScores', 'id', 'counter']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id Report ID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate Request
        $this->validateReport($request);

        $report = Report::find($id);

        if(!$report)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        $scores = $request->input('scores');
        $i = 0;

        foreach($request->input('rules') as $ruleId)
        {
            //If no score is paired with that rule, abort
            if(!isset($scores[$i]))
                break;

            //Update pivot with new scores with their ordering
            $report->scores()->where('rule_id', $ruleId)->where('reviewer_id', \Auth::user()->id)
                ->update(['score'=>$scores[$i++]]);
        }

        Session::flash('success',trans('reports.updated'));
        return redirect(route('report.show', $id));
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
            'rules.*'  => 'required|exists:performance_rules,id',
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
