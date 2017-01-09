<?php

namespace App\Http\Controllers;

use App\Defect;
use App\User;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DefectController extends Controller {
	/**
	 * Display a listing of the users with defects.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($userId) {
		//Get the user and return an error if no user
		$user=User::find($userId);
		if (!$user)
		{
			Session::flash('alert',trans('defects.no_employee'));
			return redirect()->route('home');
		}
		//Include DataTable
		$includeDataTable = true;
		//DataTable ajax route
		$dataTableRoute = route('defect.list',['userId'=>$userId]);
		return view('defects.index', compact('includeDataTable', 'dataTableRoute','user'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Defect  $defect
	 * @return \Illuminate\Http\Response
	 */
	public function show(Defect $defect) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Defect  $defect
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Defect $defect) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Defect  $defect
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Defect $defect) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Defect  $defect
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Defect $defect) {
		//
	}
	/**
	 * Returns reports data to DataTable
	 *
	 * @return JSON
	 */
	public function listData($userId) {
		//Get defects related to a user by userId
		$defects=User::join('defect_user','users.id','defect_user.user_id')->join('defects','defect_user.defect_id','defects.id')->select(['defect_user.id','defects.title','defects.score','defect_user.created_at'])->where('users.id',$userId);
		return Datatables::of($defects)
			->addColumn('action', function ($defects) {

				$formHead = "<form class='form-horizontal main_form' method='POST' action='" . route('defect.destroy', $defects->id) . "'>" . csrf_field();
				$viewLink = "<a href=" . route('defect.show', $defects->id) . " class='btn btn-xs btn-success'><i class='glyphicon glyphicon-eye-open'></i> " . trans('general.show') . "</a>&nbsp;";
				$editLink = "<a href=" . route('defect.edit', $defects->id) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
				$deleteForm =
				"  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                        </button>
                    </form>";

				return $formHead . $viewLink . $editLink . $deleteForm;

			}) //Change the Format of report date
			->editColumn('created_at', function ($defects) {
				return date('d M Y', strtotime($defects->created_at));
			}) // To Update the Offdays Section and Convert it to String
			->make();
	}
}
