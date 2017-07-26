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
use Carbon\Carbon;

class DefectController extends Controller
{


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

    /**
     * Validation rules array
     * @var array
     */
    protected $rules;

    public function __construct(UserService $userService, DefectService $defectService)
    {
        $this->userService = $userService;
        $this->defectService = $defectService;
        $this->rules = [
            'defect' => 'exists:defects,id',
            'userId' => 'exists:users,id',

        ];
    }

    /**
     * Display a listing of the users with defects.
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($userId)
    {

        try {
            //Only admin can pick whatever id they like
            $user = $this->userService->getLoggedOrSelected($userId);
        } catch (\Exception $e) {
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
    public function create($userId)
    {
        try {
            //get all defects
            $defects = $this->defectService->defectRepository->getAllItems();
        } catch (\Exception $e) {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
        return view('defects.create', compact(['defects', 'userId']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        //Add the user ID to the request
        $request->merge(['userId' => $userId]);
        //Validate input
        $this->validateDefect($request);
        try {
            $user = $this->userService->userRepository->getItem($userId);
            $this->userService->onlyEmployee($user);
            //Boot model
            $this->userService->userRepository->setModel($user);
            //check if there is a comment
            if(!empty(trim($request->comment)))
            {
                //Create Comment and get its object back
                $comment = $this->defectService->addComment(trim($request->comment), Auth::id());
            }
            //Attach defect and its comment to the user.
            $this->userService->userRepository->attachDefect(
                $request->defect, 
                isset($comment->id)? $comment->id: null
            );
            

            //un-boot model
            $this->userService->userRepository->resetModel();

        } catch (\Exception $e) {
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
     * @param  \App\Defect $defectAttachmentId
     * @return \Illuminate\Http\Response
     */
    public function edit($userId, $defectAttachmentId)
    {
        try {
            //get all defects
            $defects = $this->defectService->defectRepository->getAllItems();
            //Verify that the defect belongs to the given user id
            $selectedDefect = $this->userService->userRepository->getDefects($defectAttachmentId, $userId);
            //get defect comment
            $comment = $this->defectService->getComment($defectAttachmentId);
           
        } catch (\Exception $e) {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }

        //Otherwise return edit
        return view('defects.edit', [
            'selectedDefect' => $selectedDefect->id,
            'defectAttachmentId' => $defectAttachmentId,
            'userId' => $userId,
            'defects' => $defects,
            'comment' => isset($comment->comment)? $comment->comment : ""
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $userId
     * @param $defectAttachmentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $userId, $defectAttachmentId)
    {
        $this->validateDefect($request);
        try {
            if(!empty(trim($request->comment)))
            {
                //Update Comment and adds it if not existed before
                $commentId = $this->defectService->updateComment(
                    $defectAttachmentId,
                    trim($request->comment),
                    Auth::id());
            }
            //Update defect
            $this->userService->userRepository->updateDefect($userId, $defectAttachmentId, $request->defect, $commentId);

        } catch (\Exception $e) {
            Session::flash('alert', $e->getMessage());
            return redirect()->route('home');
        }
        Session::flash('flash_message', trans('defects.updated'));
        return redirect()->route('defect.index', [$userId]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Defect $defectAttachmentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($defectAttachmentId)
    {
        try {
            //Defect deleted
            $this->userService->userRepository->detachDefect($defectAttachmentId);
        } catch (\Exception $e) {
            //Couldn't delete defect
            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
        Session::flash('flash_message', trans('defects.deleted'));
        return redirect()->back();
    }

    /**
     * Function to validate request
     * @param  Request $request The request
     */
    public function validateDefect(Request $request)
    {
        $this->validate($request, $this->rules);
    }

    /**
     * Returns defects data to DataTable
     *
     * @return JSON
     */
    public function listData($userId)
    {
        //Get defects related to a user by userId
        $defects = $this->userService->userRepository
            ->getDefectsScope(Auth::user()->hasRole('admin'), Auth::id(), $userId);

        return Datatables::of($defects)
            ->addColumn('action', function ($defect) use ($userId) {
                if (Auth::user()->hasRole('admin')) {
                    return $this->defectService->dataTableControllers($userId, $defect);
                }
                //Not admin so no actions
                return '-';
            })//Change the Format of report date
            ->editColumn('created_at', function ($defect) {
                return (new Carbon($defect->created_at))->format('d M Y');
            })// To Update the Offdays Section and Convert it to String
            ->make();
    }
}
