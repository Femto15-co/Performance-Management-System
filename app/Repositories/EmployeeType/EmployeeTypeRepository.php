<?php

namespace App\Repositories\EmployeeType;

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
    public function __construct(Model $model)
    {
        $this->setModel($model);
        $this->originalModel = $this->getModel();
    }
}