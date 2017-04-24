<?php

namespace App\Http\Controllers;

use App\Repositories\EmployeeType\EmployeeTypeInterface;
use App\Services\UserService;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class UserController extends Controller {

    protected $employeeTypeRepository;
    protected $userService;

    /**
     * UserController constructor.
     * @param EmployeeTypeInterface $employeeTypeRepository
     * @param UserService $userService
     */
    public function __construct(
        EmployeeTypeInterface $employeeTypeRepository,
        UserService $userService
    ) {
        $this->employeeTypeRepository = $employeeTypeRepository;
        $this->userService = $userService;
    }

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
	    try
        {
            $roles = $this->employeeTypeRepository->getAll();
        }
        catch (\Exception $e)
        {
            Session::flash('error', $e->getMessage());
            return redirect()->route('home');
        }
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
		$this->validateUser($request);

		try
        {
            $user = $this->userService->userRepository->create($request->all());

            //Ensure that rule Id exists
            $role = $this->userService->getRoleFromType($request->employee_type);

            //Add the role to user
            $this->userService->userRepository->attachRole($user, $role);
        }
        catch(\Exception $e)
        {
            Session::flash('error', trans($e->getMessage()));
            return redirect()->route('home');
        }

		//Return success
		Session::flash('flash_message', trans('users.added'));
		return redirect()->route('user.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
	    try
        {
            $user = $this->userService->userRepository->destroy($id);
        }
        catch (\Exception $e)
        {
            Session::flash('error', trans('users.not_deleted'));
            return redirect()->back();
        }

        Session::flash('flash_message', trans('users.deleted'));
        return redirect()->back();
	}

	/**
	 * Returns users data to DataTable
	 *
	 * @return JSON
	 */
	public function listData() {

	    //Get admin rule
        try
        {
            $role = $this->userService->roleRepository->getRoleByName('admin');
            $users = $this->userService->userRepository->getUsersForRoleScope($role->id);
        }
        catch (\Exception $e)
        {
            return json_encode($e->getMessage());
        }

		return Datatables::of($users)
			->addColumn('action', function ($user) {
				return $this->userService->dataTableControllers($user, Auth::user()->hasRole('admin'));
			})
			->make();
	}

    /**
     * Validate input request
     * @param Request $request
     */
    public function validateUser(Request $request)
    {
        // Some defined rules that has to be achieved
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'employee_type' => 'required|integer|exists:employee_types,id',
        ];

        // Run the validator on request data
        $this->validate($request, $rules);
    }
}