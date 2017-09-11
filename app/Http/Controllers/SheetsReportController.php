<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SheetService;
use App\Services\ProjectService;
use App\Services\UserService;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SheetsReportController extends Controller
{
    /**
     * User Service
     * @var UserService
     */
    protected $userService;

    /**
     * Sheet Service
     * @var SheetService
     */
    protected $sheetService;


    /**
     * Project Service
     * @var ProjectService
     */
    protected $projectService;



    public function __construct(UserService $userService,
        SheetService $sheetService, ProjectService $projectService) {
        /*
         * Initialize controller dependencies
         */
        $this->userService = $userService;
        $this->sheetService = $sheetService;
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //Include DataTable
        $includeDataTable = true;

        //DataTable ajax route
        $dataTableRoute = route('timesheets.list');

        $projects = $this->projectService->projectRepository->getProjects()->get();
//        dd($projects);
        $users = $this->userService->userRepository->getAllEmployees();

        return view('sheetsreports.index', compact('includeDataTable', 'dataTableRoute',
            'projects', 'users'));
    }

    /**
     * Returns sheetsReport data to DataTable
     *
     * @return JSON
     */
    public function listData(Request $request)
    {

        $sheets = $this->sheetService->sheetRepository->getSheetsForAUserScope(true, Auth::id());
        $total = $this->sheetService->sheetRepository->getTotal();

        if($request->has('from')) {
            $sheets->where('date' , '>=' , $request->input('from'));
            $total->where('date' , '>=' , $request->input('from'));

        }

        if($request->has('to')) {
            $sheets->where('date' , '<=' , $request->input('to'));
            $total->where('date' , '<=' , $request->input('to'));
        }

        if($request->has('project_name')) {
            $sheets->where('projects.id' , '=' , $request->input('project_name'));
            $total->where('projects.id' , '=' , $request->input('project_name'));
        }

        if($request->has('user_name')) {
            $sheets->where('users.id' , '=' , $request->input('user_name'));
            $total->where('users.id' , '=' , $request->input('user_name'));
        }


        return Datatables::of($sheets)
            ->removeColumn('id')
            ->removeColumn('userid')
            ->removeColumn('projectid')
            ->editColumn('status', function ($project) {
                return ($project->status) ? 'Active' : 'Inactive';
            })
            ->with('total_duration', $total->select('duration')->sum('duration'))
            ->make();
    }

}
