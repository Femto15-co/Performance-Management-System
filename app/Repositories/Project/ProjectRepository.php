<?php

namespace App\Repositories\Project;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
/**
 * ProjectRepository is a class that contains common queries for users
 */
class ProjectRepository extends BaseRepository implements ProjectInterface
{
    /**
     * ProjectRepository constructor.
     * Inject whatever passed model
     * @param Model $project
     */
    public function __construct(Model $project)
    {
        $this->setModel($project);
    }


    /**
     * Get project by id
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getProjectById($id)
    {
        $project = $this->getModel()->find($id);
        if(!$project)
        {
            throw new \Exception(trans('projects.project_not_found'));
        }
        return $project;
    }


    /**
     * Get all active projects
     * @return mixed
     */
    public function getAllActive(){
        $projects = $this->getModel()->get()->where('status', '=', true);
        return $projects;
    }

    public function getProjects(){
        $projects = $this->getModel()->select([
            'id', 'name', 'status'
        ]);

        return $projects;
    }
}