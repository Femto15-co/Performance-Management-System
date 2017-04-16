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

}