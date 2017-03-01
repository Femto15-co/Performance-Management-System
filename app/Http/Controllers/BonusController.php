<?php

namespace App\Http\Controllers;

use App\Bonus;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class BonusController extends Controller {

	//Controller validation rules
	public $rules = ['description' => 'required|string', 'value' => 'required|integer', 'user_id' => 'required|exists:users,id'];

	/**
	 * Display a listing of the resource.
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
		$dataTableRoute = route('bonus.list', ['userId' => $userId]);
		return view('bonuses.index', compact('includeDataTable', 'dataTableRoute', 'user'));
	}

	/**
	 * Show the form for creating a new bonus.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($userId) {
		return view('bonuses.create', compact(['userId']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $userId) {
		//Add the user ID to the request
		$request->merge(['user_id' => $userId]);
		//Validate input
		$this->validateBonus($request);

		//Get the inteded user
		$user = User::find($userId);
		if (!$user || $user->hasRole('admin')) {
			Session::flash('error', trans('users.no_employee'));
			return redirect()->back();
		}
		//Add bonus for user
		if (Bonus::create($request->all())) {
			//Return success
			Session::flash('flash_message', trans('bonuses.added'));
			return redirect()->route('bonus.index', [$userId]);
		}
		//Something is wrong!
		Session::flash('error', trans('bonuses.not_added'));
		return redirect()->route('bonus.index', [$userId]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($userId, $id) {
		//Validate that we got the correct bonus
		$bonus = $this->verifyBonus($userId, $id);
		return view('bonuses.edit', compact(['bonus', 'userId']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $userId, $id) {
		$request->merge(['user_id' => $userId]);
		//Validate input
		$this->validateBonus($request);
		//Validate that we got the correct bonus
		$bonus = $this->verifyBonus($userId, $id);

		//Update bonus
		if ($bonus->update($request->all())) {
			//Return success
			Session::flash('flash_message', trans('bonuses.updated'));
			return redirect()->route('bonus.index', [$userId]);
		}
		//Something is wrong!
		Session::flash('error', trans('bonuses.not_updated'));
		return redirect()->route('bonus.index', [$userId]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$bonus = Bonus::find($id);
		if (!$bonus) {
			//Something is wrong!
			Session::flash('error', trans('bonuses.not_deleted'));
			return redirect()->back();
		}
		//Delete bonus
		if ($bonus->delete()) {
			//Success
			Session::flash('flash_message', trans('bonuses.deleted'));
			return redirect()->back();
		}
	}

	/**
	 * Function to validate request
	 * @param  Request $request The request
	 */
	public function validateBonus(Request $request) {

		$this->validate($request, $this->rules);
	}

	/**
	 * Verify that the bonus belongs to the given user!
	 * @param  integer $userId The user id
	 * @param  integer $id     The bonus id
	 * @return mixed         Return Bonus object on success or redirect with error on failur
	 */
	public function verifyBonus($userId, $id) {
		//Get the intended bonus
		$bonus = Bonus::where(['id' => $id, 'user_id' => $userId])->first();
		if (!$bonus) {
			//Something is wrong!
			Session::flash('error', trans('bonuses.not_found'));
			return redirect()->route('bonus.index', [$userId])->withErrors()->withInput()->send();
		}
		return $bonus;
	}
	/**
	 * Returns bonuses data to DataTable
	 *
	 * @return JSON
	 */
	public function listData($userId) {
		//Get bonuses related to a user by userId
		$bonuses = User::join('bonuses', 'users.id', 'bonuses.user_id')
			->select(['bonuses.id', 'bonuses.description', 'bonuses.value', 'bonuses.created_at']);

		//Make sure that user can't see other users data
		if (Auth::user()->hasRole('admin')) {
			$bonuses = $bonuses->where('users.id', $userId);
		} else {
			$bonuses = $bonuses->where('users.id', Auth::id());
		}

		return Datatables::of($bonuses)
			->addColumn('action', function ($bonuses) use ($userId) {
				if (Auth::user()->hasRole('admin')) {
					$formHead = "<form class='delete-form' method='POST' action='" . route('bonus.destroy', $bonuses->id) . "'>" . csrf_field();
					$editLink = "<a href=" . route('bonus.edit', [$userId, $bonuses->id]) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
					$deleteForm =
					"  <input type='hidden' name='_method' value='DELETE'/>
                            <button type='submit' class='btn btn-xs btn-danger main_delete'>
                                <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                            </button>
                        </form>";

					return $formHead . $editLink . $deleteForm;
				}
				//Otherwise no actions
				return '-';

			}) //Change the Format of report date
			->editColumn('created_at', function ($bonuses) {
				return date('d M Y', strtotime($bonuses->created_at));
			}) // To Update the Offdays Section and Convert it to String
			->make();
	}
}
