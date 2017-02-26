<?php

namespace App\Http\Controllers;

use App\Defect;
use App\User;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class DefectController extends Controller {

	public $rules=['defect'=>'exists:defects,id','userId'=>'exists:users,id'];
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
	public function create($userId) {
		//Get all defects
		$defects=Defect::all();
		return view('defects.manage',compact(['defects','userId']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request,$userId) {
		//Add the user ID to the request
		$request->merge(['userId'=>$userId]);
		//Validate input
		$this->validateDefect($request);

		//Get the inteded user
		$user=User::find($userId);
		if (!$user||$user->hasRole('admin'))
		{
			Session::flash('error',trans('defects.no_employee'));
			return redirect()->back();
		}
		//Attache defect to the user.
		$user->defects()->attach($request->defect);
		//Return success
		Session::flash('flash_message',trans('defects.added'));
		return redirect()->route('defect.index',[$userId]);

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Defect  $defectAttachmentId
	 * @return \Illuminate\Http\Response
	 */
	public function edit($userId,$defectAttachmentId) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Defect  $defectAttachmentId
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request,$defectAttachmentId) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Defect  $defectAttachmentId
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($defectAttachmentId) {

		//Defect deleted
		if (DB::table('defect_user')->where('id',$defectAttachmentId)->delete())
		{
			Session::flash('flash_message',trans('defects.deleted'));
			return redirect()->back();
		}
		//Couldn't delete defect
		Session::flash('error',trans('defects.not_deleted'));
		return redirect()->back();
	}
	/**
	 * Function to validate request
	 * @param  Request $request The request
	 */
	public function validateDefect(Request $request)
	{

		$this->validate($request,$this->rules);
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
				$editLink = "<a href=" . route('defect.edit', $defects->id) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
				$deleteForm =
				"  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                        </button>
                    </form>";

				return $formHead . $editLink . $deleteForm;

			}) //Change the Format of report date
			->editColumn('created_at', function ($defects) {
				return date('d M Y', strtotime($defects->created_at));
			}) // To Update the Offdays Section and Convert it to String
			->make();
	}
}
