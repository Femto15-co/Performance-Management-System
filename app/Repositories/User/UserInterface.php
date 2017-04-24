<?php

namespace App\Repositories\User;

/**
 * Interface UserInterface
 * Simple contract to force implementation of below functions
 */
interface UserInterface
{

    /**
     * Create new User
     * @param $data array of key-value pairs
     * @return Model user data
     * @throws \Exception
     */
    public function create($data);
    public function getUserById($id);
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
     * Attach role to user
     * @param $user
     * @param $role
     */
    public function attachRole($user, $role);
    /**
     * Delete user from database
     * @param $id
     * @param string $attribute
     * @throws \Exception
     */
    public function destroy($id, $attribute="id");
    /**
     * Get Users for a role query scope
     * @param $roleId
     * @return mixed
     */
    public function getUsersForRoleScope($roleId);
}