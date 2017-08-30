<?php
namespace App\Services;

use App\Repositories\Sheet\SheetInterface;

class SheetService
{
    public $sheetRepository;

    public function __construct(SheetInterface $sheetRepository)
    {
        $this->sheetRepository = $sheetRepository;
    }

    public function addSheet($date, $projectId, $userId, $duration, $description)
    {
       // dd($date);
        try {
            $sheet = $this->sheetRepository->addItem(['date' => $date,
                'project_id' => $projectId,
                'user_id' => $userId,
                'duration' => $duration,
                'description' => $description]);
        }catch (\Exception $e){
            dd($e->getMessage());
        }
    }

    public function dataTableControllers($sheet)
    {
        $formHead = "<form class='delete-form' method='POST' action='" .
            route('sheet.destroy', $sheet->id) . "'>" . csrf_field();
        $editLink = "<a href=" . route('sheet.edit', $sheet->id) .
            " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" .
            trans('general.edit') .
            "</a>";

        $deleteForm =
            "  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                        </button>
                    </form>";

        return $formHead . $editLink . $deleteForm;
    }
}