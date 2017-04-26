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
    public function getBonusesForUserScope($isAdmin, $loggedInUserId, $sentUserId)
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
    public function getUsersForRoleScope($roleId)
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
    public function getDefectsForUserScope($isAdmin, $loggedInUserId, $sentUserId)
    {
        //Get defects related to a user by userId
        $defects = $this->getModel()->join('defect_user', 'users.id', 'defect_user.user_id')->join('defects', 'defect_user.defect_id', 'defects.id')->select(['defect_user.id', 'defects.title', 'defects.score', 'defect_user.created_at']);

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
     * @param int $defectAttachmentId
     * @param int $userId
     * @return Model|null|static
     * @throws \Exception
     */
    public function getDefectRelatedToUser($defectAttachmentId, $userId)
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
        if (!isset($userDefects->defects[0]->pivot)) {
            throw new \Exception(trans('defects.no_defect'));
        }

        return $userDefects->defects[0];
    }

    /**
     * attach defect to user
     * @param $user
     * @param $defectId
     * @throws \Exception
     */
    public function attachDefectToUser($user, $defectId)
    {
        $this->ensureBooted();
        $user->defects()->attach($defectId);
    }

    /**
     * delete defects from database
     * @param $defectAttachmentId
     * @throws \Exception
     */
    public function detachDefectFromUser($defectAttachmentId)
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
     * @throws \Exception
     */
    public function updateDefectOfUser($userId, $defectAttachmentId, $requestDefect)
    {
        //Update defect
        if (!\DB::table('defect_user')->where([
                'id' => $defectAttachmentId,
                'user_id' => $userId,
            ])->update(['defect_id' => $requestDefect])
        ) {
            throw new \Exception('reports.not_updated');
        }
    }


}