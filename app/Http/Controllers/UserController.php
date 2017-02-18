<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
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
        $dataTableRoute = route('user.list');

        return view('user.index', compact('includeDataTable', 'dataTableRoute'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Returns users data to DataTable
     *
     * @return JSON
     */
    /*public function listData()
    {
        $users = User::join('employee_types', 'users.employee_type', '=', 'employee_types.id')
            ->select(['users.name', 'users.email', 'employee_types.type']);

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                $viewLink = "<a href=".route('user.show',$user->id)." class='btn btn-xs btn-success'>
                <i class='glyphicon glyphicon-view'></i> ".trans('reports.final_report')."</a>&nbsp;";



                //Edit link, show while overall score is not defined, admin and reviewer has participated in the evaluation process
                $editLink = "<a href=".route('report.edit',$report->id)." class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>".trans('general.edit')."</a>";


                //Delete form, show if admin
                if($this->loggedInUser->hasRole('admin'))
                {
                    $formHead = "<form class='form-horizontal main_form' method='POST' action='".route('report.destroy',$report->id)."'>".csrf_field();
                    $deleteForm =
                        "  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-delete'></i> ".trans('general.delete')."
                        </button>
                    </form>";
                }


                return $formHead . $viewLink . $editLink . $participateLink. $deleteForm;

            }) //Change the Format of report date
            ->editColumn('created_at', function ($reports) {
                return date('d M Y', strtotime($reports->created_at));
            }) // To Update the Offdays Section and Convert it to String
            ->make();
    }*/
}
