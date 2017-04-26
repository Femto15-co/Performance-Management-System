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
    public function getDefectsForUserScope($isAdmin, $loggedInUserId, $sentUserId)
    /**
    * Query gets defects that related to  a user by userId
    * @param Integer $defectAttachmentId
    * @param Integer $userId
    * @return mixed
    */
    public function getDefectsRelatedToUser($defectAttachmentId, $userId)

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
}