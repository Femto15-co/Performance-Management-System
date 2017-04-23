<?php

namespace App\Http\Controllers;

use App\PerformanceRule;
use App\Services\ReportService;
use App\Services\UserService;
use App\Services\PerformanceRuleService;
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
     * User Service
     * @var UserService
     */
    protected $userService;

    /**
     * PerformanceRule Service
     * @var PerformanceRuleService
     */
    protected $performanceRuleService;

    /**
     * Report Service
     * @var ReportService
     */
    protected $reportService;

    public function __construct(UserService $userService, PerformanceRuleService $performanceRuleService, ReportService $reportService)
    {
        /*
         * Initialize controller dependencies
         */
        $this->userService = $userService;
        $this->performanceRuleService = $performanceRuleService;
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the resource.
     * @param $userId
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
     * Step 1 for Admin only, Choose employee to evaluate
     * @return \Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function create()
    {
        try
        {
            $employees = $this->userService->userRepository->getAllEmployees();
        }
        catch(\Exception $e)
        {
            Session::flash('error',$e->getMessage());
            return redirect(route('report.index'));
        }

        return view('reports.step1', compact(['employees']));
    }

    /**
     * Show step2 form for creating a new report.
     * @param $id
     * @return \Illuminate\Routing\Redirector
     */
    public function createStepTwo($id)
    {
        try
        {
            $employee = $this->userService->userRepository->getUserById($id);

            $this->userService->onlyEmployee($employee);

            $performanceRules = $this->performanceRuleService->performanceRuleRepository->getRulesByType($employee->employee_type);
        }
        catch(\Exception $e)
        {
            Session::flash('error',$e->getMessage());
            return redirect(route('report.index'));
        }

        //Pass Counter to view
        $counter = 0;
        return view('reports.step2', compact(['performanceRules','counter']))->with('employee',$employee->id);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate Request
        $this->validateReport($request);

        try
        {
            $employee = $this->userService->userRepository->getUserById($request->employee);

            $this->userService->onlyEmployee($employee);

            $this->reportService->addReport($employee, $request->input('scores'), $request->input('rules'));
        }
        catch(\Exception $e)
        {
            //if not created, redirect to reports index and show error message
            Session::flash('error',$e->getMessage());
            return redirect(route('report.index'));
        }

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
        try
        {
            $report = $this->reportService->reportRepository->getReportById($id);

            //Can current user participate?
            $this->reportService->canParticipate($report, Auth::user());

            //Still open for participation?
            $this->reportService->openModification($report);

            //Load employee to be evaluated
            $employee = $this->reportService->getReportEmployee($report);

            //load rules based on employee type
            $performanceRules = $this->performanceRuleService->performanceRuleRepository->getRulesByType($employee->employee_type);
        }
        catch(\Exception $e)
        {
            Session::flash('error',$e->getMessage());
            return redirect(route('report.index'));
        }

        //Pass Counter to view
        $counter = 0;
        return view('reports.participate', compact(['performanceRules','counter', 'report']));
    }

    /**
     * Return from that allows users to participate in his reviewal process
     * @param $id Report ID
     * @return \Illuminate\Routing\Redirector
     */
    public function putParticipate(Request $request, $id)
    {
        //Validate Request
        $this->validateReport($request);

        try
        {
            $this->reportService->reportParticipate($id, $request->scores, $request->rules);
        }
        catch(\Exception $e)
        {
            //report not found, redirect to reports index and show error message
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        //Add success message and return
        Session::flash('flash_message',trans('reports.created_first'));

        if($this->reportService->redirectTo)
            return redirect($this->reportService->redirectTo);

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
        //Report array
        $reviewersScores = array();

        //Unique reviewers
        $reviewers = array();

        //Unique Rules
        $rules = array();

        
        $report = $this->reportService->reportRepository->getReportById($id);

        $this->reportService->allowedView($report, Auth::user());

        /*
         * List Data in rules by reviewer matrix
         */
        $scores = $report->scores()->get();
        //TODO
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
        try
        {
            $report = $this->reportService->reportRepository->getReportById($id);

            $this->reportService->openModification($report);

            //Get scores recorded by authenticated user who attempted edit
            $ruleScores = $this->reportService->reportRepository->getReviewerScores($report, Auth::user());
        }
        catch(\Exception $e)
        {
            Session::flash('error',$e->getMessage());
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

        try
        {
            $this->reportService->updateReport($id, $request->scores, $request->rules);
        }
        catch(\Exception $e)
        {
            Session::flash('error',$e->getMessage());
            return redirect(route('report.index'));
        }
        
        Session::flash('flash_message', trans('reports.created_first'));
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
        //TODO
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
    //TODO
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

}
