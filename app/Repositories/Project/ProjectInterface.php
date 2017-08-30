<?php

namespace App\Repositories\Project;

/**
 * Interface Project
 * Simple contract to force implementation of below functions
 */
interface ProjectInterface
{
    public function getProjectById($id);
}