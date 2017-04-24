<?php

namespace App\Repositories\Role;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;

/**
 * EmployeeTypeRepository is a class that contains common queries for EmployeeTypes
 */
class RoleRepository extends BaseRepository implements RoleInterface
{
    /**
     * EmployeeType Model
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the role given role name
     * @param  string $roleName The role name
     * @return mixed           role or null
     * @throws \Exception
     */
    public function getRoleByName($roleName)
    {
        $role=$this->model->where('name',$roleName)->first();

        if (!$role)
        {
            throw new \Exception(trans('users.role_not_found'));
        }

        return $role;
    }
}