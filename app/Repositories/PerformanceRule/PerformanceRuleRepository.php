<?php

namespace App\Repositories\PerformanceRule;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
/**
 * PerformanceRuleRepository is a class that contains common queries for users
 */
class PerformanceRuleRepository extends BaseRepository implements PerformanceRuleInterface
{

    /**
     * Holds performanceRule model
     * @var Model
     */
    protected $performanceRuleModel;

    /**
     * PerformanceRuleRepository constructor.
     * Inject whatever passed model
     * @param Model $performanceRule
     */
    public function __construct(Model $performanceRule)
    {
        $this->performanceRuleModel = $performanceRule;
    }

    /**
     * Get performance rules by type
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    public function getRulesByType($type)
    {
        //load rules based on employee type
        $performanceRules = $this->performanceRuleModel->where('employee_type', $type)->get();

        if(!$performanceRules || $performanceRules->isEmpty())
        {
            throw new \Exception(trans('reports.no_rules'));
        }

        return $performanceRules;
    }

    /**
     * Get maximum possible final score for a type
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    public function getMaxScoreByType($type)
    {
        $maxScore=$this->performanceRuleModel->select(DB::raw('SUM(weight)*10 as final'))->where('employee_type',$type)->first();
        if(!$maxScore)
        {
            throw new \Exception(trans('reports.no_max_score'));
        }
        return $maxScore->final;
    }

    /**
     * Get rule by id
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getRuleById($id)
    {
        $rule = $this->performanceRuleModel->find($id);
        if(!$rule)
        {
            throw new \Exception(trans('reports.rule_not_found'));
        }
        return $rule;
    }

    /**
     * Does rule exist for a given type
     * @param $employeeType
     * @param $ruleId
     * @return mixed
     */
    public function isRuleExistsForType($employeeType, $ruleId)
    {
        return $this->performanceRuleModel->where('employee_type', $employeeType)->where('id', $ruleId)->exists();
    }

}