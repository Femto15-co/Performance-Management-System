<?php
namespace App\Services;

use App\Repositories\Project\ProjectInterface;

class ProjectService
{
    public $projectRepository;

    public function __construct(ProjectInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function addProject($name, $desc, $status)
    {
        $rule = $this->projectRepository->addItem(['name' => $name,
                'description' => $desc,
                'status' => intval($status)]);
    }

    public function dataTableControllers($project)
    {
        $formHead = "<form class='delete-form' method='POST' action='" .
            route('project.destroy', $project->id) . "'>" . csrf_field();
        $editLink = "<a href=" . route('project.edit', $project->id) .
            " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" .
            trans('general.edit') .
            "</a>";

        $deleteForm =
            "  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                        </button>
                    </form>";

        return $formHead . $editLink . $deleteForm;
    }
}