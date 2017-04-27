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
    * in case of no results to user
    * 
    * @return $result[]
    */
    public function emptyResult();

    /**
    * Get all bonuses of user within that month
    * @param $dateStart
    * @param $dateEnd
    * @param $bonusesTotal
    * return $result[0]
    */
    public function bonusesOfUser($dateStart,$dateEnd,$bonusesTotal);

     /**
    * Get all defects of user within that month
    * @param $dateStart
    * @param $dateEnd
    * @param $defectsTotal
    * return $result[1]
    */
    public function defectsOfUser($dateStart,$dateEnd,$defectsTotal);

    /**
    * get reports of user
    * @param $dateStart
    * @param $dateEnd
    * @return mixed
    */
    public function userReportsScope($dateStart,$dateEnd);

    /**
    * Get all reports of user within that month
    * @param $dateStart
    * @param $dateEnd
    * @param $reportsCount
    * return $result[2]
    */
    public function getScoreOfUserReport($dateStart,$dateEnd,$reportsCount);
}