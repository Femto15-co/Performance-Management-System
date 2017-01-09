<?php

namespace App\Http\Controllers;

use App\Defect;
use Illuminate\Http\Request;

class DefectController extends Controller {
	/**
	 * Display a listing of the users with defects.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//Include DataTable
		$includeDataTable = true;

		//DataTable ajax route
		$dataTableRoute = route('report.list');

		return view('reports.index', compact('includeDataTable', 'dataTableRoute'));
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
	public function listData() {
		$reports = Report::join('users', 'reports.user_id', '=', 'users.id')
			->select(['reports.id', 'users.name', 'reports.overall_score', 'reports.created_at']);

		return Datatables::of($reports)
			->addColumn('action', function ($reports) {

				$formHead = "<form class='form-horizontal main_form' method='POST' action='" . route('report.destroy', $reports->id) . "'>" . csrf_field();
				$viewLink = "<a href=" . route('report.show', $reports->id) . " class='btn btn-xs btn-success'><i class='glyphicon glyphicon-view'></i> " . trans('general.show') . "</a>&nbsp;";
				$editLink = "<a href=" . route('report.edit', $reports->id) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
				$deleteForm =
				"  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-delete'></i> " . trans('general.delete') . "
                        </button>
                    </form>";

				return $formHead . $viewLink . $editLink . $deleteForm;

			}) //Change the Format of report date
			->editColumn('created_at', function ($reports) {
				return date('d M Y', strtotime($reports->created_at));
			}) // To Update the Offdays Section and Convert it to String
			->make();
	}
}
