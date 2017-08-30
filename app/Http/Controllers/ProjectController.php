<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use App\Services\ProjectService;

class ProjectController extends Controller
{

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Include DataTable
        $includeDataTable = true;

        //DataTable ajax route
        $dataTableRoute = route('project.list');

        return view('projects.index', compact('includeDataTable', 'dataTableRoute'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
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
        $this->validateRule($request);

        try {

            $this->projectService->addProject(
                $request->input('name'),
                $request->input('desc'),
                $request->input('status')
            );
        } catch (\Exception $e) {
            //if not created, redirect to projects index and show error message
            Session::flash('project error', $e->getMessage());
            return redirect(route('project.index'));
        }

        Session::flash('flash_message', trans('projects.created'));
        return redirect(route('project.index'));
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
        $project = $this->projectService->projectRepository->getProjectById($id);
        return view('projects.edit', compact('project', 'id'));
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
        //Validate Request
        $this->validateRule($request);
        try {
            $this->projectService->projectRepository
                ->editItem($id, ['name' => $request->name,
                    'description' => $request->desc,
                    'status' => $request->status]);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('project.index'));
        }

        Session::flash('flash_message', trans('projects.updated'));
        return redirect(route('project.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete project and all corresponding stuff
        try {
            $this->projectService->projectRepository->deleteItem($id);
        } catch (\Exception $e) {
            Session::flash('error', trans('projects.not_deleted'));
            return redirect(route('project.index'));
        }

        Session::flash('flash_message', trans('projects.deleted'));
        return redirect(route('project.index'));
    }


    /**
     * Validate input request
     * @param Request $request
     */
    public function validateRule(Request $request)
    {
        // Some defined rules that has to be achieved
        $rules = [
            'name' => 'required',
            'desc' => 'required',
            'status' => 'required'
        ];

        // Run the validator on request data
        $this->validate($request, $rules);
    }

    /**
     * Returns projects data to DataTable
     *
     * @return JSON
     */
    public function listData(Request $request)
    {

        $rules = $this->projectService->projectRepository->getAllItems();

        return Datatables::of($rules)
            ->addColumn('action', function ($project) {
                return $this->projectService->dataTableControllers($project);
            })
            ->editColumn('status', function ($project) {
                return ($project->status) ? 'Active' : 'Inactive';
            })
            ->removeColumn('description')
            ->removeColumn('created_at')
            ->removeColumn('updated_at')
            ->make();
    }

}
