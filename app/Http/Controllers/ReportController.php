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

    public function __construct(
        UserService $userService,
        PerformanceRuleService $performanceRuleService,
        ReportService $reportService
    ) {
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
    public function index($userId = null)
    {
        //Include DataTable
        $includeDataTable = true;

        //DataTable ajax route
        $dataTableRoute = ($userId) ? route('report.list', [$userId]) : route('report.list');

        return view('reports.index', compact('includeDataTable', 'dataTableRoute', 'userId'));
    }

    /**
     * Show step1 form for creating a new report.
     * Step 1 for Admin only, Choose employee to evaluate
     * @return \Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function create()
    {
        try {
            $employees = $this->userService->userRepository->getAllEmployees();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        return view('reports.step1')->with('employees', $employees);
    }

    /**
     * Show step2 form for creating a new report.
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createStepTwo($id)
    {
        try {
            $employee = $this->userService->userRepository->getItem($id);

            $this->userService->onlyEmployee($employee);

            $performanceRules = $this->performanceRuleService
                ->performanceRuleRepository->getRulesByType($employee->employee_type);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        //Pass Counter to view
        $counter = 0;
        return view('reports.step2', compact(['counter']))
            ->with(['employee' => $employee->id, 'performanceRules' => $performanceRules]);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate Request
        $this->validateReport($request);

        try {
            $employee = $this->userService->userRepository->getItem($request->employee);

            $this->userService->onlyEmployee($employee);

            $this->reportService->addReport(
                $employee,
                $request->input('scores'),
                $request->input('rules'),
                trim($request->input('comment'))
                );
        } catch (\Exception $e) {
            //if not created, redirect to reports index and show error message
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        Session::flash('flash_message', trans('reports.created_first'));
        return redirect(route('report.index'));
    }

    /**
     * Return a from that allows users to participate in his reviewal process
     * @param $id report id
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getParticipate($id)
    {
        try {
            $report = $this->reportService->reportRepository->getItem($id);

            //Can current user participate?
            $this->reportService->canParticipate($report, Auth::user());

            //Still open for participation?
            $this->reportService->openModification($report);

            //Load employee to be evaluated
            $employee = $this->reportService->getReportEmployee($report);

            //load rules based on employee type
            $performanceRules = $this->performanceRuleService->performanceRuleRepository->getRulesByType($employee->employee_type);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        //Pass Counter to view
        $counter = 0;
        return view('reports.participate', compact(['performanceRules', 'counter', 'report']));
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

        try {
            $this->reportService->reportParticipate(
                $id,
                $request->scores,
                $request->rules,
                trim($request->comment)
                );
        } catch (\Exception $e) {
            //report not found, redirect to reports index and show error message
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        //Add success message and return
        Session::flash('flash_message', trans('reports.created_first'));

        if ($this->reportService->redirectTo) {
            return redirect($this->reportService->redirectTo);
        }

        return redirect(route('report.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            //Load report with scores
            $report = $this->reportService->reportRepository->getItem($id, ['scores','comments']);
            $this->reportService->allowedView($report, Auth::user());

            //List Data in rules x reviewer matrix
            extract($this->reportService->getReviewsMatrix($report));

            //Get a Collection of Comments on report (empty if no comments)
            $comments= $this->reportService->reportRepository->getAllComments($report);
            
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route('report.index');
        }

        return view('reports.show', compact('reviewersScores', 'reviewers', 'rules', 'id', 'avgScores', 'report','comments'));
    }

    /**
     * Show the form for editing the specified report.
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            //Get scores recorded by authenticated user who attempted edit
            $reportWithScores = $this->reportService->reportRepository
            ->getReviewerScores($id, Auth::user()->id, ['comments']);
            
            //Get Comment of logged in user on report
            $comment = $this->reportService->reportRepository
            ->getUserComment($reportWithScores);
            //check if there is a comment for logged in user
            if($comment){
                $comment = $comment->comment;
            }
           
            $this->reportService->openModification($reportWithScores);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        //Pass Counter to view
        $counter = 0;
        return view('reports.edit', compact(['reportWithScores', 'id', 'counter', 'comment']))
            ->with('reportWithScores', $reportWithScores);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id Report ID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate Request
        $this->validateReport($request);

        try {
            $this->reportService->updateReport(
                $id,
                $request->scores,
                $request->rules,
                trim($request->comment));
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('report.index'));
        }

        Session::flash('flash_message', trans('reports.created_first'));
        return redirect(route('report.index'));
    }

    /**
     * Remove the specified report from storage.
     *
     * @param  int $id Report ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete report and all corresponding stuff
        try {
            $this->reportService->reportRepository->deleteItem($id);
        } catch (\Exception $e) {
            Session::flash('error', trans('reports.not_deleted'));
            return redirect(route('report.index', $id));
        }

        Session::flash('flash_message', trans('reports.deleted'));
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
            'rules.*' => 'required|exists:performance_rules,id',
        ];

        // Run the validator on request data
        $this->validate($request, $rules);
    }

    /**
     * Returns reports data to DataTable
     *
     * @return JSON
     */
    public function listData(Request $request, $userId = null)
    {
        $isAdmin = Auth::user()->hasRole('admin');
        $reports = $this->reportService->reportRepository->getReportsForAUserScope($isAdmin, Auth::id(), $userId);

        return Datatables::of($reports)
            ->addColumn('action', function ($report) use ($isAdmin) {
                return $this->reportService->dataTableControllers($report, Auth::user());
            })
            //Change the Format of report date
            ->editColumn('created_at', function ($reports) {
                return date('d M Y', strtotime($reports->created_at));
            })->editColumn('overall_score', function ($report) {
                return ($report->overall_score == 0) ?
                    trans('general.not_ready') : $report->overall_score . ' ' . trans('of') . ' ' . $report->max_score;
            })
            //Remove max_score
            ->removeColumn('max_score')
            ->removeColumn('user_id')
            ->make();
    }
}
