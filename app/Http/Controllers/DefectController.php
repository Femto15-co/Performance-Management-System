<?php

namespace App\Http\Controllers;

use App\Defect;
use App\Services\DefectService;
use App\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class DefectController extends Controller {

	public $rules = ['defect' => 'exists:defects,id', 'userId' => 'exists:users,id'];
	/**
     * User Repository
     * @var
     */
	protected $userService;

    /**
     * Bonus service
     * @var
     */
	protected $defectService;
    public function __construct(UserService $userService, DefectService $defectService)
    {
        $this->userService = $userService;
        $this->defectService = $defectService;
	}
	/**
	 * Display a listing of the users with defects.
	 *
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
		$dataTableRoute = route('defect.list', ['userId' => $userId]);
		return view('defects.index', compact('includeDataTable', 'dataTableRoute', 'user'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($userId) {
		try
        {
            //get all defects
            $defect = $this->defectService->getAll();
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
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
		try
        {
            $user=$this->userService->onlyEmployee($this->userService->userRepository->getUserById($userId));
            //Attache defect to the user.
			$this->defectService->attachToUser($user,$request->defect);
            
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
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
		try
        {
            //get all defects
            $defects = $this->defectService->getAll();
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
		//Verify that the defect belongs to the given user id
		$user = $this->defectService->verifyDefectUser($userId, $defectAttachmentId);
		//Otherwise return edit
		return view('defects.edit', ['selectedDefect' => $user->defects[0]->id, 'defectAttachmentId' => $defectAttachmentId,'userId' => $userId, 'defects' => $defects]);
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
		$this->defectService->verifyDefectUser($userId, $defectAttachmentId);
		try{
			//Update defect
			$this->defectService->update($userId, $defectAttachmentId,$request->defect);
		}
		catch{
		 	Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
		}
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
		try{
			//Defect deleted
			$this->defectService->destroy($defectAttachmentId);
		}
		catch{
			//Couldn't delete defect
		 	Session::flash('alert', $e->getMessage());
            return redirect()->back();
		}
		Session::flash('flash_message', trans('defects.deleted'));
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
		
	 	$isAdmin = Auth::user()->hasRole('admin');
		//Get defects related to a user by userId
		$defects = $this->userService->userRepository->getDefectsForUserScope($isAdmin, Auth::id(), $userId);
		
		return Datatables::of($defects)
			->addColumn('action', function ($defects) use ($userId) {
				if ($isAdmin) {
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

}
