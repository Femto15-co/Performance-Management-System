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
     * RoleRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->setModel($model);
        $this->originalModel = $this->getModel();
    }

    /**
     * Get the role given role name
     * @param  string $roleName The role name
     * @return mixed           role or null
     * @throws \Exception
     */
    public function getRoleByName($roleName)
    {
        $role = $this->model->where('name', $roleName)->first();

        if (!$role) {
            throw new \Exception(trans('users.role_not_found'));
        }

        return $role;
    }
}