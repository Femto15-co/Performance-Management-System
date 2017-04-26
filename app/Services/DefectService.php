<?php
namespace App\Services;

use App\Repositories\Defect\DefectInterface;
use App\Repositories\User\UserInterface;
use Illuminate\Support\Facades\Session;


class DefectService
{
    public $defectRepository;

    public function __construct(DefectInterface $defectRepository,UserInterface $userRepository)
    {
        $this->defectRepository = $defectRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Generates dataTable controllers
     * @param $userId
     * @param $defect
     * @return string
     */
    public function dataTableControllers($userId, $defect)
    {
        $formHead = "<form class='delete-form' method='POST' action='" . route('bonus.destroy', $defect->id) . "'>" . csrf_field();
        $editLink = "<a href=" . route('bonus.edit', [$userId, $defect->id]) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
        $deleteForm =
            "  <input type='hidden' name='_method' value='DELETE'/>
                            <button type='submit' class='btn btn-xs btn-danger main_delete'>
                                <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                            </button>
                        </form>";

        return $formHead . $editLink . $deleteForm;
    }
    
}