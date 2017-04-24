<?php

namespace App\Repositories\Role;

/**
 * Interface RoleInterface
 * Simple contract to force implementation of below functions
 */
interface RoleInterface
{
    /**
     * Get the role given role name
     * @param  string $roleName The role name
     * @return mixed           role or null
     * @throws \Exception
     */
    public function getRoleByName($roleName);

}