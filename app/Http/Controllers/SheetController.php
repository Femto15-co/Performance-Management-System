<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SheetService;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;

class SheetController extends Controller
{

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



    public function __construct(SheetService $sheetService, ProjectService $projectService) {
        /*
         * Initialize controller dependencies
         */
        $this->sheetService = $sheetService;
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
        $dataTableRoute = route('sheet.list');

        return view('sheets.index', compact('includeDataTable', 'dataTableRoute'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $projects = $this->projectService->projectRepository->getAllActive();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('sheet.index'));
        }
        return view('sheets.create', compact('projects'));
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
        $this->validateSheet($request);

        try {

            $this->sheetService->addSheet(
                $request->input('date'),
                $request->input('project'),
                Auth::id(),
                $request->input('duration'),
                $request->input('desc')
            );
        } catch (\Exception $e) {
            //if not created, redirect to sheets index and show error message
            Session::flash('error', $e->getMessage());
            return redirect(route('sheet.index'));
        }

        Session::flash('flash_message', trans('sheets.created'));

        if($request->input('btn1') == 'clicked')
            return redirect(route('sheet.create'));

        return redirect(route('sheet.index'));
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
        try {
            $sheet = $this->sheetService->sheetRepository->getItem($id);
            $projects = $this->projectService->projectRepository->getAllItems();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('sheet.index'));
        }
        return view('sheets.edit', compact('sheet', 'projects', 'id'));
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
        $this->validateSheet($request);
        try {
            $this->sheetService->sheetRepository
                ->editItem($id, ['date' => $request->date,
                    'project_id' => $request->project,
                    'duration' => $request->duration,
                    'description' => $request->desc]);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('sheet.index'));
        }

        Session::flash('flash_message', trans('sheets.updated'));
        return redirect(route('sheet.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete sheet and all corresponding stuff
        try {
            $this->sheetService->sheetRepository->deleteItem($id);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('sheet.index'));
        }

        Session::flash('flash_message', trans('sheets.deleted'));
        return redirect(route('sheet.index'));
    }


    /**
     * Validate input request
     * @param Request $request
     */
    public function validateSheet(Request $request)
    {
        // Some defined rules that has to be achieved
        $rules = [
            'date' => 'required|date|before_or_equal:'.date('Y-m-d'),
            'duration' => 'required|regex:/[+-]?([0-9]*[.])?[0-9]+/|max:24',
            'project' => 'required|exists:projects,id',
            'desc' => 'required|max:255'
        ];

        // Run the validator on request data
        $this->validate($request, $rules);
    }

    /**
     * Returns sheets data to DataTable
     *
     * @return JSON
     */
    public function listData(Request $request, $userId = null)
    {
        $isAdmin = Auth::user()->hasRole('admin');
        $sheets = $this->sheetService->sheetRepository->getSheetsForAUserScope($isAdmin, Auth::id(), $userId);

        if(!$isAdmin) {
            return Datatables::of($sheets)
            ->addColumn('action', function ($sheet) use ($isAdmin) {
                return $this->sheetService->dataTableControllers($sheet, Auth::user());
            })
            ->removeColumn('username')
            ->removeColumn('status')
            ->removeColumn('userid')
            ->removeColumn('projectid')
            ->make();
        }
        return Datatables::of($sheets)
            ->addColumn('action', function ($sheet) use ($isAdmin) {
                return $this->sheetService->dataTableControllers($sheet, Auth::user());
            })
            ->removeColumn('status')
            ->removeColumn('userid')
            ->removeColumn('projectid')
            ->make();
    }
}
