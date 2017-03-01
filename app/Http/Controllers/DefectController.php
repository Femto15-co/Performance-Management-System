<?php

namespace App\Http\Controllers;

use App\Defect;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class DefectController extends Controller {

	public $rules = ['defect' => 'exists:defects,id', 'userId' => 'exists:users,id'];
	/**
	 * Display a listing of the users with defects.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($userId) {

		//Only admin can pick whatever id they like
		if (!Auth::user()->hasRole('admin')) {
			$userId = Auth::id();
		}

		//Get the user and return an error if no user
		$user = User::find($userId);
		if (!$user) {
			Session::flash('alert', trans('users.no_employee'));
			return redirect()->route('home');
		}
		//Include DataTable
		$includeDataTable = true;
		//DataTable ajax route
		$dataTableRoute = route('defect.list', ['userId' => $userId]);
		return view('defects.index', compact('includeDataTable', 'dataTableRoute', 'user'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($userId) {
		//Get all defects
		$defects = Defect::all();
		return view('defects.create', compact(['defects', 'userId']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $userId) {
		//Add the user ID to the request
		$request->merge(['userId' => $userId]);
		//Validate input
		$this->validateDefect($request);

		//Get the inteded user
		$user = User::find($userId);
		if (!$user || $user->hasRole('admin')) {
			Session::flash('error', trans('users.no_employee'));
			return redirect()->back();
		}
		//Attache defect to the user.
		$user->defects()->attach($request->defect);
		//Return success
		Session::flash('flash_message', trans('defects.added'));
		return redirect()->route('defect.index', [$userId]);

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Defect  $defectAttachmentId
	 * @return \Illuminate\Http\Response
	 */
	public function edit($userId, $defectAttachmentId) {
		//Get all defects
		$defects = Defect::all();
		//Verify that the defect belongs to the given user id
		$user = $this->verifyDefectUser($userId, $defectAttachmentId);
		//Otherwise return edit
		return view('defects.edit', ['selectedDefect' => $user->defects[0]->id, 'defectAttachmentId' => $defectAttachmentId,
			'userId' => $userId, 'defects' => $defects]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Defect  $defectAttachmentId
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $userId, $defectAttachmentId) {
		//Verify that the defect belongs to the given user id
		$user = $this->verifyDefectUser($userId, $defectAttachmentId);
		//Update defect
		DB::table('defect_user')->where(
			[
				'id' => $defectAttachmentId,
				'user_id' => $userId,
			])->update(['defect_id' => $request->defect]);
		Session::flash('flash_message', trans('defects.updated'));
		return redirect()->route('defect.index', [$userId]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Defect  $defectAttachmentId
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($defectAttachmentId) {

		//Defect deleted
		if (DB::table('defect_user')->where('id', $defectAttachmentId)->delete()) {
			Session::flash('flash_message', trans('defects.deleted'));
			return redirect()->back();
		}
		//Couldn't delete defect
		Session::flash('error', trans('defects.not_deleted'));
		return redirect()->back();
	}
	/**
	 * Function to validate request
	 * @param  Request $request The request
	 */
	public function validateDefect(Request $request) {

		$this->validate($request, $this->rules);
	}
	/**
	 * Returns defects data to DataTable
	 *
	 * @return JSON
	 */
	public function listData($userId) {
		//Get defects related to a user by userId
		$defects = User::join('defect_user', 'users.id', 'defect_user.user_id')->join('defects', 'defect_user.defect_id', 'defects.id')->select(['defect_user.id', 'defects.title', 'defects.score', 'defect_user.created_at']);
		//Maks sure that user can't see other users data
		if (Auth::user()->hasRole('admin')) {
			$defects = $defects->where('users.id', $userId);
		} else {
			$defects = $defects->where('users.id', Auth::id());
		}
		return Datatables::of($defects)
			->addColumn('action', function ($defects) use ($userId) {
				if (Auth::user()->hasRole('admin')) {
					$formHead = "<form class='delete-form' method='POST' action='" . route('defect.destroy', $defects->id) . "'>" . csrf_field();
					$editLink = "<a href=" . route('defect.edit', [$userId, $defects->id]) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
					$deleteForm =
					"  <input type='hidden' name='_method' value='DELETE'/>
	                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
	                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
	                        </button>
	                    </form>";

					return $formHead . $editLink . $deleteForm;
				}
				//Not admin so no actions
				return '-';

			}) //Change the Format of report date
			->editColumn('created_at', function ($defects) {
				return date('d M Y', strtotime($defects->created_at));
			}) // To Update the Offdays Section and Convert it to String
			->make();
	}
	/**
	 * Verify the selected defect + user compination
	 * @param  integer $userId             The user Id
	 * @param  Integer $defectAttachmentId Defect User Pivot Id
	 * @return mixed   User object on success or redirect otherwise
	 */
	public function verifyDefectUser($userId, $defectAttachmentId) {
		//Get the user with the selected defect.
		$user = User::with(['defects' => function ($query) use ($defectAttachmentId) {
			$query->where('defect_user.id', $defectAttachmentId);
		}])->where('id', $userId)->first();
		//Didn't get a user
		if (empty($user)) {
			Session::flash('error', trans('users.no_employee'));
			return redirect()->back()->send();
		}
		//Defect id isn't correct
		if (!isset($user->defects[0]->pivot)) {
			Session::flash('error', trans('defects.no_defect'));
			return redirect()->back()->send();
		}

		return $user;
	}
}
