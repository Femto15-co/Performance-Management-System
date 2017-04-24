<?php

namespace App\Repositories\EmployeeType;

/**
 * Interface EmployeeTypeInterface
 * Simple contract to force implementation of below functions
 */
interface EmployeeTypeInterface
{
    /**
     * Get all EmployeeTypes
     * @throws \Exception
     * @return EmployeeType[]
     */
    public function getAll();
}