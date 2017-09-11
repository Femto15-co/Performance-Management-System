<?php

namespace App\Http\Controllers;

use App\Services\PerformanceRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use App\Services\UserService;


class PerformanceController extends Controller
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


    public function __construct(
        UserService $userService,
        PerformanceRuleService $performanceRuleService
    ) {
        /*
         * Initialize controller dependencies
         */
        $this->userService = $userService;
        $this->performanceRuleService = $performanceRuleService;
    }

    /**
     * Display a listing of the performance rules.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //Include DataTable
        $includeDataTable = true;

        //DataTable ajax route
        $dataTableRoute = route('rule.list');

        return view('rules.index', compact('includeDataTable', 'dataTableRoute'));
    }

    /**
     * create new performance rule
     * @return \Illuminate\View\View
     */
    public function create(){
        try {
            $employeeTypes = $this->userService->employeeTypeRepository->getAllItems();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('rule.index'));
        }
        return view('rules.create')->with('employeeTypes', $employeeTypes);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        //Validate Request
        $this->validateRule($request);

        try {

            $this->performanceRuleService->addRule(
                $request->input('rule'),
                $request->input('desc'),
                $request->input('weight'),
                $request->input('etype')
            );
        } catch (\Exception $e) {
            //if not created, redirect to reports index and show error message
            Session::flash('error', $e->getMessage());
            return redirect(route('rule.index'));
        }

        Session::flash('flash_message', trans('rules.created'));
        return redirect(route('rule.index'));

    }

    /**
     * Show the form for editing the specified rule.
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $rule = $this->performanceRuleService->performanceRuleRepository->getRuleById($id);
            $employeeTypes = $this->userService->employeeTypeRepository->getAllItems();
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('rule.index'));
        }
        return view('rules.edit', compact('rule', 'employeeTypes', 'id'));
    }

    public function destroy($id)
    {
        //Delete rule and all corresponding stuff
        try {
            $this->performanceRuleService->performanceRuleRepository->deleteItem($id);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('rule.index'));
        }

        Session::flash('flash_message', trans('rules.deleted'));
        return redirect(route('rule.index'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id Rule ID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate Request
        $this->validateRule($request);
        try {
            $this->performanceRuleService->performanceRuleRepository
                ->editItem($id, ['rule' => $request->rule,
                    'desc' => $request->desc,
                    'weight' => $request->weight,
                    'employee_type' => $request->etype]);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('rule.index'));
        }

        Session::flash('flash_message', trans('rules.updated'));
        return redirect(route('rule.index'));
    }


    /**
     * Validate input request
     * @param Request $request
     */
    public function validateRule(Request $request)
    {
        // Some defined rules that has to be achieved
        $rules = [
            'rule' => 'required|max:255',
            'desc' => 'required|max:255',
            'weight' => 'required|integer|max:10',
            'etype' => 'required|exists:employee_types,id'
        ];

        // Run the validator on request data
        $this->validate($request, $rules);
    }

    /**
     * Returns rules data to DataTable
     *
     * @return JSON
     */
    public function listData(Request $request)
    {

        $rules = $this->performanceRuleService->performanceRuleRepository->getAll();

        return Datatables::of($rules)
            ->addColumn('action', function ($rule) {
                return $this->performanceRuleService->dataTableControllers($rule);
            })
            ->make();
    }
}

