<?php

namespace App\Repositories\Report;

/**
 * Interface ReportInterface
 * Simple contract to force implementation of below function
 */
interface ReportInterface
{
    public function getReportById($id);
    public function create($data);
    public function getFinalScores($id, $user);
    public function update($id, $data, $attribute = "id");
}