<?php

namespace App\Repositories\EmployeeType;

use App\EmployeeType;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * EmployeeTypeRepository is a class that contains common queries for EmployeeTypes
 */
class EmployeeTypeRepository extends BaseRepository implements EmployeeTypeInterface
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
     * Get all EmployeeTypes
     * @throws \Exception
     * @return EmployeeType[]
     */
    public function getAll()
    {
        $employeeTypes = $this->model->all();

        if($employeeTypes->isEmpty())
        {
            throw new \Exception('users.not_added');
        }

        return $employeeTypes;
    }
}