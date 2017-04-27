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
    * @param Integer $defectAttachmentId
    * @param Integer $userId
    * @return mixed
    */
    public function getDefectsRelatedToUser($defectAttachmentId, $userId)
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

        return $userDefects;
    }

    /**
    * attach defect to user
    * @param $user
    * @param $defectId
    * @throws \Exception
    */
    public function attachDefectToUser($user,$defectId){
        $this->ensureBooted();
        $user->defects()->attach($defectId);
    }
    /**
    * delete defects from database
    * @param $defectAttachmentId
    * @throws \Exception
    */
    public function detachDefectFromUser($defectAttachmentId){

        if(!\DB::table('defect_user')->where('id', $defectAttachmentId)->delete()){
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
    public function updateDefectOfUser($userId, $defectAttachmentId,$requestDefect)
    {
        //Update defect
        if(!\DB::table('defect_user')->where(
                [
                    'id' => $defectAttachmentId,
                    'user_id' => $userId,
                ])->update(['defect_id' => $requestDefect]))
        {
            throw new \Exception('reports.not_updated');
        }
    }
   

    /**
    * Get all bonuses of user within that month
    * @param $user
    * @param $dateStart
    * @param $dateEnd
    * @param $bonusesTotal
    * return $result[0]
    */
    public function bonusesOfUser($user,$dateStart,$dateEnd,$bonusesTotal)
    {
        //get sum of user's bonuses
        $bonusesTotal=$user->bonuses()->where('created_at','>=',$dateStart)
        ->where('created_at','<',$dateEnd)->sum('value');
      
        //Update bonuses result
        $result[0]=($bonusesTotal)?number_format($bonusesTotal, 2, '.', ''):"0 ";

        return $result[0];
    }

    /**
    * Get all defects of user within that month
    * @param $user
    * @param $dateStart
    * @param $dateEnd
    * @param $defectsTotal
    * return $result[1]
    */
    public function defectsOfUser($user,$dateStart,$dateEnd,$defectsTotal)
    {
        //get sum of user's defects
        $defectsTotal=$user->defects()->where('defect_user.created_at','>=',$dateStart)
        ->where('defect_user.created_at','<',$dateEnd)->sum('score');
      
        //Update defects result
        $defectsDays=number_format($defectsTotal/6,2,'.','');
        $result[1]=($defectsTotal)?$defectsTotal." ($defectsDays Days)":"0 (0 Days)";  

        return $result[1];
    }

    /**
    * get reports of user
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function reportsInPeriodScope($user,$userId,$dateStart,$dateEnd)
    {
        return $user->reports()->where('user_id',$userId)
        ->where('created_at','>=',$dateStart)->where('created_at','<',$dateEnd);
    }
    /**
    * get sum overall score of report
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function sumOverAllScoreOfReport($user,$userId,$dateStart,$dateEnd)
    {
        //return sum of overall score
        return $reportsOverall=$this->reportsInPeriodScope($user,$userId,$dateStart,$dateEnd)->sum('overall_score');
    }
    /**
    * get sum max score of report
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function sumMaxScoreOfReport($user,$userId,$dateStart,$dateEnd)
    {
        //return sum of max score
        return $reportsOverall=$this->reportsInPeriodScope($user,$userId,$dateStart,$dateEnd)->sum('max_score');
    }
    /**
    * get count  of reports
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function sumCountOfReports($user,$userId,$dateStart,$dateEnd)
    {
        //return count  of reports
        return $reportsOverall=$this->reportsInPeriodScope($user,$userId,$dateStart,$dateEnd)->count();
    }
    /**
    * Get all reports of user within that month
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @param $reportsCount
    * return $result[2]
    */
    public function getScoreOfReport($user,$userId,$dateStart,$dateEnd,$reportsCount)
    {
        $result[2]="0 of 0";
        //return sum of overall score
        $reportsOverall=$this->sumOverAllScoreOfReport($user,$userId,$dateStart,$dateEnd);
        //return sum of max score
        $reportsMax=$this->sumMaxScoreOfReport($user,$userId,$dateStart,$dateEnd);
        //return count of reports
        $reportsCount=$this->sumCountOfReports($user,$userId,$dateStart,$dateEnd);
        //We got some good reports
        if ($reportsCount>0)
        {
            $reportsOverall=$reportsOverall/$reportsCount;
            $reportsMax=$reportsMax/$reportsCount;
            //Update result
            $result[2]="$reportsOverall of $reportsMax";
        }

        return $result[2];
    }




     
}