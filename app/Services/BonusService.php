<?php
namespace App\Services;

use App\Repositories\Bonus\BonusInterface;

class BonusService
{
    public $bonusRepository;

    public function __construct(BonusInterface $bonusRepository)
    {
        $this->bonusRepository = $bonusRepository;
    }

    /**
     * Generates dataTable controllers
     * @param $userId
     * @param $bonus
     * @return string
     */
    public function dataTableControllers($userId, $bonus)
    {
        $formHead = "<form class='delete-form' method='POST' action='" . route('bonus.destroy', $bonus->id) . "'>" . csrf_field();
        $editLink = "<a href=" . route('bonus.edit', [$userId, $bonus->id]) . " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" . trans('general.edit') . "</a>";
        $deleteForm =
            "  <input type='hidden' name='_method' value='DELETE'/>
                            <button type='submit' class='btn btn-xs btn-danger main_delete'>
                                <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                            </button>
                        </form>";

        return $formHead . $editLink . $deleteForm;
    }
}