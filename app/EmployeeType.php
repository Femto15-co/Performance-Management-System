<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    /**
     * Get the employee type by name given the role name
     * @param  string $roleName The role name
     * @return mixed           Role ID or null
     */
    public static function getEmployeeTypeIdByName($employeeTypeName)
    {
        $employeeType=self::where('type',$employeeTypeName)->first();
        if ($employeeType)
        {
        	//All is good so return the id
        	return $employeeType->id;
        }
        //Not found return null
        return $employeeType;
    }
}
