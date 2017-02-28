<?php

namespace App\Http\Controllers;

use App\EmployeeType;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class UserController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//Include DataTable
		$includeDataTable = true;

		//DataTable ajax route
		$dataTableRoute = route('user.list');

		return view('users.index', compact('includeDataTable', 'dataTableRoute'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$roles = EmployeeType::all();
		return view('users.create', compact('roles'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//Validate request
		$this->validate($request, [
			'name' => 'required|string',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|string|min:8',
			'employee_type' => 'required|integer|exists:employee_types,id',
		]);

		//Try to create the user
		$user=User::create($request->all());
		if (!$user) {
			//Something is wrong!
			Session::flash('error', trans('users.not_added'));
			return redirect()->back();
		}

		//We got an admin
		$adminType=EmployeeType::getEmployeeTypeIdByName('admin');
		if ($request->employee_type==$adminType)
		{
			$role=$adminType;
		}
		else
		{
			//Otherwise it's just an employee
			$role=EmployeeType::getEmployeeTypeIdByName('employee');
		}

		//Add the role to user
		$user->attachRole($role);
		//Return success
		Session::flash('flash_message', trans('users.added'));
		return redirect()->route('user.index');

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
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$user = User::find($id);
		//We got a correct user
		if ($user) {
			$user->delete();
			Session::flash('flash_message', trans('users.deleted'));
			return redirect()->back();
		}
		Session::flash('error', trans('users.not_deleted'));
		return redirect()->back();
	}

	/**
	 * Returns users data to DataTable
	 *
	 * @return JSON
	 */
	public function listData() {
		$users = User::join('role_user', 'users.id', '=', 'role_user.user_id')->join('employee_types', 'users.employee_type', '=', 'employee_types.id')->where('role_user.role_id', '>', Role::getRoleIdByName('admin'))
			->select(['users.id', 'users.name', 'users.email', 'employee_types.type']);

		return Datatables::of($users)
			->addColumn('action', function ($user) {
				$actions = '<a href="' . route('bonus.index', [$user->id]) . '" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-star"></i>' . trans('bonuses.title') . '</a> ';
				$actions .= '<a href="' . route('defect.index', [$user->id]) . '" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-remove"></i>' . trans('defects.title') . '</a> ';
				$actions .= '<a href="' . route('report.user.index', [$user->id]) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-file"></i>' . trans('reports.reports') . '</a> ';
				//Delete form, show if admin
				if (Auth::user()->hasRole('admin')) {
					$actions .= "<form class='delete-form' method='POST' action='" . route('user.destroy', $user->id) . "'>"
					. csrf_field() .
					"<input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                        </button>
                    </form>";
				}

				return $actions;

			})
			->make();
	}
}
