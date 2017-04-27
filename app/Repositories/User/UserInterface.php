<?php

namespace App\Repositories\User;

/**
 * Interface UserInterface
 * Simple contract to force implementation of below functions
 */
interface UserInterface
{
    /**
     * Get All users with role employee
     * @return \App\User[]
     * @throws \Exception
     */
    public function getAllEmployees();

    /**
     * Query scope that gets bonuses for a user
     * @param bool $isAdmin
     * @param Integer $loggedInUserId
     * @param Integer $sentUserId
     * @return mixed
     */
    public function getBonusesForUserScope($isAdmin, $loggedInUserId, $sentUserId);
    /**
    * Query scope that gets defects for a user
    * @param bool $isAdmin
    * @param Integer $loggedInUserId
    * @param Integer $sentUserId
    * @return mixed
    */
    public function getDefectsForUserScope($isAdmin, $loggedInUserId, $sentUserId);
    /**
    * Query gets defects that related to  a user by userId
    * @param Integer $defectAttachmentId
    * @param Integer $userId
    * @return mixed
    */
    public function getDefectsRelatedToUser($defectAttachmentId, $userId);

    /**
     * Attach role to user
     * @param $role
     */
    public function attachRole($role);

    /**
     * Get Users for a role query scope
     * @param $roleId
     * @return mixed
     */
    public function getUsersForRoleScope($roleId);

      /**
    * attach defect to user
    * @param $user
    * @param $defectId
    * @throws \Exception
    */
    public function attachDefectToUser($user,$defectId);

     /**
    * delete defects from database
    * @param $defectAttachmentId
    * @throws \Exception
    */
    public function detachDefectFromUser($defectAttachmentId);

     /**
    * update defect of user
    * @param $userId
    * @param $defectAttachmentId
    * @param $requestDefect
    * @throws \Exception
    */
    public function updateDefectOfUser($userId, $defectAttachmentId,$requestDefect);

    

    /**
    * Get all bonuses of user within that month
    * @param $dateStart
    * @param $dateEnd
    * @param $bonusesTotal
    * return $result[0]
    */
    public function bonusesOfUser($user,$dateStart,$dateEnd,$bonusesTotal);

     /**
    * Get all defects of user within that month
    * @param $dateStart
    * @param $dateEnd
    * @param $defectsTotal
    * return $result[1]
    */
    public function defectsOfUser($user,$dateStart,$dateEnd,$defectsTotal);

    /**
    * get reports of user
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function reportsInPeriodScope($user,$userId,$dateStart,$dateEnd);

    /**
    * get sum overall score of report
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function sumOverAllScoreOfReport($user,$userId,$dateStart,$dateEnd);

    /**
    * get sum max score of report
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function sumMaxScoreOfReport($user,$userId,$dateStart,$dateEnd);

    /**
    * get count  of reports
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function sumCountOfReports($user,$userId,$dateStart,$dateEnd);

    /**
    * Get all reports of user within that month
    * @param $user
    * @param $userId
    * @param $dateStart
    * @param $dateEnd
    * @param $reportsCount
    * return $result[2]
    */
    public function getScoreOfReport($user,$userId,$dateStart,$dateEnd,$reportsCount);
}