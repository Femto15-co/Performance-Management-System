<?php

namespace App\Repositories\User;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;


/**
 * UserRepository is a class that contains common queries for users
 */
class UserRepository extends BaseRepository implements UserInterface
{
    /**
     * UserRepository constructor.
     * Inject whatever passed model
     * @param Model $user
     */
    public function __construct(Model $user)
    {
        $this->setModel($user);
        $this->originalModel = $this->getModel();

    }

    /**
     * Get All users with role employee
     * @return \App\User[]
     * @throws \Exception
     */
    public function getAllEmployees()
    {
        $employees = $this->getModel()->whereHas('roles', function ($q) {
            $q->where('name', '=', 'employee');
        })->get();

        if (!$employees || $employees->isEmpty()) {
            throw new \Exception(trans('reports.no_employee'));
        }

        return $employees;
    }

    /**
     * Query scope that gets bonuses for a user
     * @param bool $isAdmin
     * @param Integer $loggedInUserId
     * @param Integer $sentUserId
     * @return mixed
     */
    public function getBonusesScope($isAdmin, $loggedInUserId, $sentUserId)
    {
        //Get bonuses related to a user by userId
        $bonuses = $this->getModel()->join('bonuses', 'users.id', 'bonuses.user_id')
            ->select(['bonuses.id', 'bonuses.description', 'bonuses.value', 'bonuses.created_at']);

        //Make sure that user can't see other users data
        if ($isAdmin) {
            return $bonuses->where('users.id', $sentUserId);
        }

        return $bonuses->where('users.id', $loggedInUserId);
    }

    /**
     * Get Users exclude role query scope
     * @param $roleId
     * @return mixed
     */
    public function getRoleScope($roleId)
    {
        $users = $this->model->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('employee_types', 'users.employee_type', '=', 'employee_types.id')
            ->where('role_user.role_id', '!=', $roleId)
            ->select(['users.id', 'users.name', 'users.email', 'employee_types.type']);

        return $users;
    }

    /**
     * Attach role to user
     * @param $role
     */
    public function attachRole($role)
    {
        $this->ensureBooted();

        $this->model->attachRole($role);
    }

    /**
     * Query scope that gets defects for a user
     * @param bool $isAdmin
     * @param Integer $loggedInUserId
     * @param Integer $sentUserId
     * @return mixed
     */
    public function getDefectsScope($isAdmin, $loggedInUserId, $sentUserId)
    {
        //Get defects related to a user by userId
        $defects = $this->getModel()->join('defect_user', 'users.id', 'defect_user.user_id')->join('defects', 'defect_user.defect_id', 'defects.id')
        ->leftjoin('comments', 'comments.id', 'defect_user.comment_id')
        ->select(['defect_user.id', 'defects.title', 'defects.score','comments.comment', 'defect_user.created_at']);

        //Make sure that user can't see other users data
        if ($isAdmin) {
            $defects = $defects->where('users.id', $sentUserId);
        } else {
            $defects = $defects->where('users.id', $loggedInUserId);
        }

        return $defects;
    }

    /**
     * Query gets defects that related to  a user by userId
     * @param Integer $defectAttachmentId
     * @param Integer $userId
     * @return mixed
     */
    public function getDefects($defectAttachmentId, $userId)
    {
        //Get defects related to a user by userId
        $userDefects = $this->getModel()->with(['defects' => function ($query) use ($defectAttachmentId) {
            $query->where('defect_user.id', $defectAttachmentId);
        }])->where('id', $userId)->first();

        //Didn't get a user
        if (empty($userDefects)) {
            throw new \Exception(trans('users.no_employee'));
        }
        //Defect id isn't correct
        if (!isset($userDefects->defects[0])) {
            throw new \Exception(trans('defects.no_defect'));
        }

        return $userDefects->defects[0];
    }

    /**
     * attach defect to user
     * @param $defectId
     * @param $commentId
     * @throws \Exception
     */
    public function attachDefect($defectId, $commentId)
    {
        $this->ensureBooted();
        $this->getModel()->defects()->attach($defectId, ['comment_id' => $commentId]);
    }

    /**
     * delete defects from database
     * @param $defectAttachmentId
     * @throws \Exception
     */
    public function detachDefect($defectAttachmentId)
    {

        if (!\DB::table('defect_user')->where('id', $defectAttachmentId)->delete()) {
            throw new \Exception('defects.not_deleted');
        }
    }

    /**
     * update defect of user
     * @param $userId
     * @param $defectAttachmentId
     * @param $requestDefect
     * @param $commentId
     * @throws \Exception
     */
    public function updateDefect($userId, $defectAttachmentId, $requestDefect, $commentId)
    {
        //Update defect
        if (!\DB::table('defect_user')->where([
            'id' => $defectAttachmentId,
            'user_id' => $userId,
        ])->update(['defect_id' => $requestDefect, 'comment_id' => $commentId])
        ) {
           // throw new \Exception('reports.not_updated');
        }
    }

    /**
     * Get all bonuses of user within that month
     * @param $dateStart
     * @param $dateEnd
     * @return string
     */
    public function getBonuses($dateStart, $dateEnd)
    {
        $this->ensureBooted();
        //get sum of user's bonuses
        $bonusesTotal = $this->getModel()->bonuses()->where('created_at', '>=', $dateStart)
            ->where('created_at', '<', $dateEnd)->sum('value');

        //Update bonuses result
        return ($bonusesTotal) ? number_format($bonusesTotal, 2, '.', '') : "0 ";
    }

    /**
     * Get all defects of user within that month
     * @param $dateStart
     * @param $dateEnd
     * return score
     */
    public function sumScoreOfDefects($dateStart, $dateEnd)
    {
        $this->ensureBooted();
        //get sum of user's defects
        $defectsTotal = $this->getModel()->defects()->where('defect_user.created_at', '>=', $dateStart)
            ->where('defect_user.created_at', '<', $dateEnd)->sum('score');

        return $defectsTotal;
    }

    /**
     * get reports of user
     * @param $dateStart
     * @param $dateEnd
     * @return mixed
     */
    public function reportsInPeriodScope($dateStart, $dateEnd)
    {
        $this->ensureBooted();
        return $this->getModel()->reports()->where('created_at', '>=', $dateStart)->where('created_at', '<', $dateEnd);
    }

    /**
     * get sum overall score of report
     * @param $dateStart
     * @param $dateEnd
     * @return overall_score
     */
    public function sumOverAllScoreOfReport($dateStart, $dateEnd)
    {
        //return sum of overall score
        return $this->reportsInPeriodScope($dateStart, $dateEnd)->sum('overall_score');
    }

    /**
     * get sum max score of report
     * @param $dateStart
     * @param $dateEnd
     * @return max_score
     */
    public function sumMaxScoreOfReport($dateStart, $dateEnd)
    {
        //return sum of max score
        return $this->reportsInPeriodScope($dateStart, $dateEnd)->sum('max_score');
    }

    /**
     * get count  of reports
     * @param $dateStart
     * @param $dateEnd
     * @return count of reports
     */
    public function sumCountOfReports($dateStart, $dateEnd)
    {
        //return count  of reports
        return $this->reportsInPeriodScope($dateStart, $dateEnd)->count();
    }

    /**
     * Get all reports of user within that month
     * @param $dateStart
     * @param $dateEnd
     * @return string
     */
    public function getPerformanceScore($dateStart, $dateEnd)
    {
        $result = "0 of 0";
        //return sum of overall score
        $reportsOverall = $this->sumOverAllScoreOfReport($dateStart, $dateEnd);
        //return sum of max score
        $reportsMax = $this->sumMaxScoreOfReport($dateStart, $dateEnd);
        //return count of reports
        $reportsCount = $this->sumCountOfReports($dateStart, $dateEnd);
        //We got some good reports
        if ($reportsCount > 0) {
            $reportsOverall = $reportsOverall / $reportsCount;
            $reportsMax = $reportsMax / $reportsCount;
            //Update result
            $result = "$reportsOverall of $reportsMax";
        }

        return $result;
    }
}