<?php namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	/**
     * Get the role id given the role name
     * @param  string $roleName The role name
     * @return mixed           Role ID or null
     */
    public static function getRoleIdByName($roleName)
    {
        $role=self::where('name',$roleName)->first();
        if ($role)
        {
        	//All is good so return the id
        	return $role->id;
        }
        //Not found return null
        return $role;
    }
}