<?php
namespace App\Services;

use App\Repositories\Bonus\DefectInterface;
use App\Repositories\User\UserInterface;

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
    /**
     * Verify the selected defect + user compination
     * @param  integer $userId             The user Id
     * @param  Integer $defectAttachmentId Defect User Pivot Id
     * @return mixed   User object on success or redirect otherwise
     */
    public function verifyDefectUser($userId, $defectAttachmentId) {
        try
        {
            //Get the user with the selected defect.
            $this->userRepository->getDefectsRelatedToUser($userId, $defectAttachmentId);
        }
        catch(\Exception $e)
        {
            Session::flash('alert', $e->getMessage());
            return redirect()->back()->send();
        }
        

        
    }
}