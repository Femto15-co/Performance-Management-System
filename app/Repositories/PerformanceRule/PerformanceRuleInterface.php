<?php

namespace App\Repositories\PerformanceRule;

/**
 * Interface PerformanceRule
 * Simple contract to force implementation of below functions
 */
interface PerformanceRuleInterface
{
    public function getRulesByType($type);
    public function getMaxScoreByType($type);
    public function getRuleById($id);
}