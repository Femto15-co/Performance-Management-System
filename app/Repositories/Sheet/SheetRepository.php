<?php

namespace App\Repositories\Sheet;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
/**
 * SheetRepository is a class that contains common queries for users
 */
class SheetRepository extends BaseRepository implements SheetInterface
{
    /**
     * SheetRepository constructor.
     * Inject whatever passed model
     * @param Model $sheet
     */
    public function __construct(Model $sheet)
    {
        $this->setModel($sheet);
    }


    /**
     * Get Sheets for a specified user or for all users if current
     * logged in user is admin
     * @param $userId
     * @param integer $loggedInUserId
     * @param bool $isAdmin
     * @return mixed
     */
    public function getSheetsForAUserScope($isAdmin, $loggedInUserId, $userId = null)
    {
        $sheets = $this->getModel()
            ->join('users', 'sheets.user_id', '=', 'users.id')
            ->join('projects','sheets.project_id', '=', 'projects.id')
            ->select([
                'sheets.id', 'sheets.date', 'users.name AS username', 'sheets.duration',
                'projects.name AS projectname', 'projects.status', 'users.id AS userid',
                'projects.id AS projectid'
            ]);
        //If user is not admin, load users reports only
        if (!$isAdmin) {
            return $sheets->where('sheets.user_id', $loggedInUserId);
        }

        //Otherwise, display user's related reports only if any user ID
        if ($userId) {
            return $sheets->where('sheets.user_id', $userId);
        }

        return $sheets;
    }

    public function getTotal()
    {
        $sheets = $this->getModel()
            ->join('users', 'sheets.user_id', '=', 'users.id')
            ->join('projects','sheets.project_id', '=', 'projects.id')
            ->select([
                'sheets.id', 'sheets.date', 'users.name AS username', 'sheets.duration',
                'projects.name AS projectname', 'projects.status', 'users.id AS userid',
                'projects.id AS projectid'
            ]);
        return $sheets;
    }
}