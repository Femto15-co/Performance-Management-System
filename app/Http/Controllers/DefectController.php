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

	
	/**
     * User Repository
     * @var
     */
	protected $userService;

    /**
     * defect service
     * @var
     */
	protected $defectService;
    public function __construct(UserService $userService, DefectService $defectService)
    {
        $this->userService = $userService;
        $this->defectService = $defectService;
     	$this->rules = [
            'defect' => 'exists:defects,id',
            'userId'       => 'exists:users,id',
           
        ];
	}
	/**
	 * Display a listing of the users with defects.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($userId) {

		try {
            //Only admin can pick whatever id they like
            $user = $this->userService->getLoggedOrSelected($userId);
        } catch(\Exception $e) {
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
		try {
            //get all defects
            $defects = $this->defectService->defectRepository->getAllItems();
        } catch(\Exception $e) {
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
		try {
        	
           	$user=$this->userService->userRepository->getItem($userId);
           	$this->userService->onlyEmployee($user);
       		//Boot model
            $this->userService->userRepository->setModel($user);
           	//Attache defect to the user.
			$this->userService->userRepository->attachDefects($user,$request->defect);
			//un-boot model
            $this->userService->userRepository->resetModel();
            
        } catch(\Exception $e) {
        	
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
		try {
            //get all defects
            $defects = $this->defectService->defectRepository->getAllItems();
            //Verify that the defect belongs to the given user id
			$user = $this->userService->userRepository->getDefects($defectAttachmentId ,$userId);
        } catch(\Exception $e) {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
		
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
		try {
		 	//Verify that the defect belongs to the given user id
			$this->userService->userRepository->getDefects($defectAttachmentId ,$userId);
			//Update defect
			$this->userService->userRepository->updateDefect($userId, $defectAttachmentId,$request->defect);
		} catch(\Exception $e){
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

		try {
			//Defect deleted
			$this->userService->userRepository->detachDefect($defectAttachmentId);
		} catch(\Exception $e) {
			
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

	 	//Get defects related to a user by userId
		$defects = $this->userService->userRepository->getDefectsScope(Auth::user()->hasRole('admin'), Auth::id(), $userId);
		
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

}
