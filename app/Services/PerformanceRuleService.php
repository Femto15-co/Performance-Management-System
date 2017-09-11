<?php
namespace App\Services;

use App\Repositories\PerformanceRule\PerformanceRuleInterface;

class PerformanceRuleService
{
    public $performanceRuleRepository;

    public function __construct(PerformanceRuleInterface $performanceRuleRepository)
    {
        $this->performanceRuleRepository = $performanceRuleRepository;
    }

    public function addRule($rule_name, $desc, $weight, $etype)
    {
        $rule = $this->performanceRuleRepository->addItem(['rule' => $rule_name,
        'desc' => $desc,
        'weight' => intval($weight),
        'employee_type' => intval($etype)]);



    }

    public function dataTableControllers($rule)
    {
        $formHead = "<form class='delete-form' method='POST' action='" .
            route('rule.destroy', $rule->id) . "'>" . csrf_field();
        $editLink = "<a href=" . route('rule.edit', $rule->id) .
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