<?php

namespace App\Http\Controllers;

use App\PerformanceRule;
use Illuminate\Http\Request;
use App\User;
use App\Report;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index($userId=null)
    {
        //Include DataTable
        $includeDataTable = true;

        //DataTable ajax route
        $dataTableRoute = ($userId)?route('report.list',[$userId]):route('report.list');

        return view('reports.index', compact('includeDataTable', 'dataTableRoute','userId'));
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
        if(!$employee->hasRole('employee'))
        {
            //if not an employee, redirect to index
            Session::flash('error',trans('reports.no_employee'));
            return redirect(route('report.index'));
        }
        //Get the max possible score
        $maxScore=PerformanceRule::select(DB::raw('SUM(weight)*10 as final'))->where('employee_type',$employee->employee_type)->first();
        //Create Report
        $report = Report::create(['user_id'=>$employee->id,'max_score'=>intval($maxScore->final)]);
        if(!$report)
        {
            //if not created, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_created'));
            return redirect(route('report.index'));
        }

        $scores = $request->input('scores');
        $rules = $request->input('rules');

        //Attach scores to report
        $this->addScores($scores, $rules, $report);

        Session::flash('flash_message',trans('reports.created_first'));
        return redirect(route('report.index'));
    }

    /**
     * Return a from that allows users to participate in his reviewal process
     * @param $id report id
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getParticipate($id)
    {
        //Find report by ID
        $report = Report::find($id);
        if(!$report )
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //Admin and user being evaluated can participate
        if(!Auth::user()->hasRole('admin') && $report->user_id != Auth::id() )
        {
            Session::flash('error',trans('reports.no_participation'));
            return redirect(route('report.index'));
        }

        //returns true if reviewer participated in the evaluation process
        $reviewerParticipated = $report->scores()->where('reviewer_id', Auth::id())->exists();

        //Allow participation if reviewer has not participated in the evaluation process yet ||
        //If report overall score is set, no longer participation is allowed
        if($report->overall_score || $reviewerParticipated)
        {
            Session::flash('error',trans('reports.no_participation'));
            return redirect(route('report.index'));
        }

        if(!$employee = $report->employee()->first())
        {
            Session::flash('error',trans('reports.no_participation'));
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
        return view('reports.participate', compact(['performanceRules','counter', 'report']));
    }

    /**
     * Return a from that allows users to participate in his reviewal process
     * @param $id Report ID
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function putParticipate(Request $request, $id)
    {
        //Validate Request
        $this->validateReport($request);

        //Get Report by ID
        $report = Report::find($id);

        if(!$report)
        {
            //if no employees, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //Admin and user being evaluated can participate
        if(!Auth::user()->hasRole('admin') && $report->user_id != Auth::id() )
        {
            Session::flash('error',trans('reports.no_participation'));
            return redirect(route('report.index'));
        }

        //returns true if reviewer participated in the evaluation process
        $reviewerParticipated = $report->scores()->where('reviewer_id', Auth::id())->exists();

        //Allow participation if reviewer has not participated in the evaluation process yet ||
        //If report overall score is set, no longer participation is allowed
        if($report->overall_score || $reviewerParticipated)
        {
            Session::flash('error',trans('reports.no_participation'));
            return redirect(route('report.index'));
        }

        $scores = $request->input('scores');
        $rules = $request->input('rules');

        //Attach scores to report
        $this->addScores($scores, $rules, $report);

        if(!$employee = $report->employee()->first()) {
            //report not found, redirect to reports index and show error message
            Session::flash('error', trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //If employee is evaluating himself, calculate overall score and prevent further participation
        if($employee->id == Auth::id())
        {
            //Calculate average scores and overall score by averaging all scores for that report excluding user's own review
            $avgScores = DB::table('scores')->select('rule_id',DB::raw( 'AVG(score) as avg_score' ))->where('report_id',$report->id)
                ->where('reviewer_id', '!=', Auth::id())->groupBy('rule_id')->get();

            $overallScore = 0;

            foreach($avgScores as $ruleScore)
            {
                $rule=PerformanceRule::find($ruleScore->rule_id);
                if (!$rule)
                {
                    continue;
                }
                $overallScore += $ruleScore->avg_score*$rule->weight;
            }


            //Update report with the overall Score
            $report->overall_score = $overallScore;
            //Commit changes to database
            $report->save();

            Session::flash('flash_message',trans('reports.created_first'));
            return redirect(route('report.show', $report->id));
        }

        Session::flash('flash_message',trans('reports.created_first'));
        return redirect(route('report.index', $report->id));
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

        //Allow report view if overall score is set
        $report = Report::find($id);
        if(!$report || !$report->overall_score)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //Trying to see other users reports
        if (!Auth::user()->hasRole('admin')&&$report->user_id!=Auth::id())
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

        //Get avg score if overall score is set
        $avgScores = null;
        if($report->overall_score)
        {
            $avgScores = DB::table('scores')->select('rule_id',DB::raw( 'AVG(score) as avg_score' ))->where('report_id',$report->id)
                ->where('reviewer_id', '!=', $report->user_id)->groupBy('rule_id')->get()->groupBy('rule_id')->toArray();
        }


        //if no rules, reviewers and reviwersScores register, abort
        if(empty($reviewersScores) && empty($rules) && empty($rules))
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        return view('reports.show', compact('reviewersScores', 'reviewers', 'rules', 'id', 'avgScores','report'));
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

        //Do not allow report modification if overall score is set
        if(!$report || $report->overall_score)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        //Get scores recorded by authenticated user who attempted edit
        $ruleScores = $report->scores()->where('reviewer_id', Auth::id())->get();

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

        //Do not allow report modification if overall score is set
        if(!$report || $report->overall_score)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error',trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        $scores = $request->input('scores');
        $i = 0;

        //Get employee being evaluated
        if(!$employee = $report->employee()->first()) {
            //report not found, redirect to reports index and show error message
            Session::flash('error', trans('reports.not_found'));
            return redirect(route('report.index'));
        }

        foreach($request->input('rules') as $ruleId)
        {
            //If no score is paired with that rule, abort
            if(!isset($scores[$i]))
                break;

            //If rule doesn't belong to selected employees related rules, ignore and continue
            $validRule = PerformanceRule::where('employee_type', $employee->employee_type)->where('id', $ruleId)->exists();

            if(!$validRule)
                continue;

            //Update pivot with new scores with their ordering
            $report->scores()->where('rule_id', $ruleId)->where('reviewer_id', Auth::id())
                ->update(['score'=>$scores[$i++]]);
        }

        Session::flash('flash_message',trans('reports.updated'));
        return redirect(route('report.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id Report ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete report and all corresponding stuff
        if(!Report::destroy($id))
        {
            Session::flash('error',trans('reports.not_deleted'));
            return redirect(route('report.index', $id));
        }

        Session::flash('flash_message',trans('reports.deleted'));
        return redirect(route('report.index', $id));

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
    public function listData(Request $request,$userId=null)
    {
        $reports = Report::join('users', 'reports.user_id', '=', 'users.id')
            ->select(['reports.id', 'users.name', 'reports.overall_score', 'reports.max_score', 'reports.created_at']);

        

        //If user is not admin, load users reports only
        if(!Auth::user()->hasRole('admin'))
        {
            $reports = $reports->where('reports.user_id',Auth::id());
        }
        //Consider the user id if we got one
        //To display user's related reports only.
        elseif($userId)
        {
            $reports=$reports->where('reports.user_id',$userId);
        }


        return Datatables::of($reports)
            ->addColumn('action', function ($report) {
                //Current user

                //returns true if reviewer participated in the evaluation process
                $reviewerParticipated = $report->scores()->where('reviewer_id', Auth::id())->exists();

                //Show link, show only if overall score is defined
                $viewLink = "";
                if($report->overall_score){
                    $viewLink = "<a href=".route('report.show',$report->id)." class='btn btn-xs btn-success'>
                    <i class='glyphicon glyphicon-eye-open'></i> ".trans('reports.final_report')."</a>&nbsp;";
                }

                //Participate link, show while overall score is not defined and reviewer has not participated in the evaluation process yet
                $participateLink = "";
                if(!$report->overall_score && !$reviewerParticipated)
                {
                    $participateLink = "<a href=".route('report.getParticipate',$report->id)." class='btn btn-xs btn-success'>
                <i class='glyphicon glyphicon-pencil'></i> ".trans('reports.participate')."</a>&nbsp;";
                }

                //Edit link, show while overall score is not defined, admin and reviewer has participated in the evaluation process
                $editLink = "";
                if(!$report->overall_score && $reviewerParticipated && Auth::user()->hasRole('admin'))
                {
                    $editLink = "<a href=".route('report.edit',$report->id)." class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>".trans('general.edit')."</a>";
                }

                //Delete form, show if admin
                $deleteForm = "";
                $formHead = "";
                if(Auth::user()->hasRole('admin'))
                {
                    $formHead = "<form class='delete-form' method='POST' action='".route('report.destroy',$report->id)."'>".csrf_field();
                    $deleteForm =
                        "  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> ".trans('general.delete')."
                        </button>
                    </form>";
                }


                return $formHead . $viewLink . $editLink . $participateLink. $deleteForm;

            }) //Change the Format of report date
            ->editColumn('created_at', function ($reports) {
                return date('d M Y', strtotime($reports->created_at));
            })->editColumn('overall_score',function($report)
            {
                return ($report->overall_score==0)?trans('general.not_ready'):$report->overall_score.' '.trans('of').' '.$report->max_score;
            })
            //Remove max_score
            ->removeColumn('max_score')
            ->make();
    }

    /**
     * Attach scores to report, one reviewer at a time
     * @param Array $scores set of scores
     * @param Array $rules set of rules to be scored
     * @param Report $report Report Model instance
     * @return bool true on success
     */
    private function addScores($scores, $rules, $report)
    {
        $i = 0;

        //Get employee being evaluated
        if(!$employee = $report->employee()->first())
        {
            return false;
        }

        foreach($rules as $ruleId)
        {
            //If no score is paired with that rule, abort
            if(!isset($scores[$i]))
                break;


            //If rule doesn't belong to selected employees related rules, ignore and continue
            $validRule = PerformanceRule::where('employee_type', $employee->employee_type)->where('id', $ruleId)->exists();
            if(!$validRule)
                continue;

            //Check first that no record contains same reportId, reviewerId and ruleId
            $foundDuplicate = $report->scores()->where('rule_id',$ruleId)->where('reviewer_id', Auth::id())->count();
            if(!$foundDuplicate)
            {
                $report->scores()->attach([$ruleId => ['reviewer_id'=>Auth::id(), 'score'=>$scores[$i++]]]);
            }
        }

        return true;
    }

}
