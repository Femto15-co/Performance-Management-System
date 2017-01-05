<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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

        //Add something to session to indicate that user passed here to prevent heading directly to step 2
        Session::put('passed_step1', true);

        return view('reports.step1', compact(['employees']));
    }

    public function createStepTwo()
    {
        //if session has step1 passed indictor proceed, otherwise abort
        if(!Session::has('passed_step1'))
        {
            //redirect to reports index and show no error message
            return redirect(route('report.index'));
        }

        //no more needed
        Session::forget('passed_step1');

        return view('reports.step2', compact([]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
