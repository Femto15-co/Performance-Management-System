<?php

namespace App\Http\Controllers;

use App\Bonus;
use App\Services\BonusService;
use App\User;
use App\Services\UserService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class BonusController extends Controller {

	//Controller validation rules
	public $rules = ['description' => 'required|string', 'value' => 'required|integer', 'user_id' => 'required|exists:users,id'];

    /**
     * User Repository
     * @var
     */
	protected $userService;

    /**
     * Bonus service
     * @var
     */
	protected $bonusService;
    public function __construct(UserService $userService, BonusService $bonusService)
    {
        $this->userService = $userService;
        $this->bonusService = $bonusService;
	}

	/**
	 * Display a listing of the bonuses.
	 * @return \Illuminate\Http\Response
	 */
	public function index($userId) {
        try
        {
            //Only admin can pick whatever id they like
            $user = $this->userService->getLoggedOrSelected($userId);
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
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
        try
        {
            //Ensure that passed user exists and is employee
            $this->userService->onlyEmployee($this->userService->userRepository->getUserById($userId));
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
		return view('bonuses.create', compact(['userId']));
	}

	/**
	 * Store a newly created bonus in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $userId) {
		//Add the user ID to the request
        $request->merge(['user_id' => $userId]);

        //Validate input
        $this->validateBonus($request);

		try
        {
            $this->userService->onlyEmployee($this->userService->userRepository->getUserById($userId));
            $this->bonusService->bonusRepository->create($request->all());
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }


        //Return success
        Session::flash('flash_message', trans('bonuses.added'));
        return redirect()->route('bonus.index', [$userId]);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($userId, $id) {
		//Validate that we got the correct bonus
        try
        {
            $bonus = $this->bonusService->bonusRepository->getBonusForAUser($userId, $id);
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
		return view('bonuses.edit', compact(['bonus', 'userId']));
	}

	/**
	 * Update the specified bonus in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $userId, $id) {
		$request->merge(['user_id' => $userId]);
		//Validate input
		$this->validateBonus($request);

        try
        {
            $bonus = $this->bonusService->bonusRepository->getBonusForAUser($userId, $id);
            $this->bonusService->bonusRepository->update($id, $request->all());
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }

        //Return success
        Session::flash('flash_message', trans('bonuses.updated'));
        return redirect()->route('bonus.index', [$userId]);
    }

	/**
	 * Remove the specified bonus from database.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
	    try
        {
            $this->bonusService->bonusRepository->destroy($id);
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }

        //Success
        Session::flash('flash_message', trans('bonuses.deleted'));
        return redirect()->back();
	}

	/**
	 * Function to validate request
	 * @param  Request $request The request
	 */
	public function validateBonus(Request $request) {

		$this->validate($request, $this->rules);
	}

	/**
	 * Returns bonuses data to DataTable
	 *
	 * @return JSON
	 */
	public function listData($userId) {
        $isAdmin = Auth::user()->hasRole('admin');

		$bonuses = $this->userService->userRepository->getBonusesForUserScope($isAdmin, Auth::id(), $userId);

		return Datatables::of($bonuses)
			->addColumn('action', function ($bonus) use ($userId, $isAdmin) {
				if ($isAdmin) {
                    return $this->bonusService->dataTableControllers($userId, $bonus);
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
