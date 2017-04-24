<?php

namespace App\Repositories\Report;

/**
 * Interface ReportInterface
 * Simple contract to force implementation of below function
 */
interface ReportInterface
{
    /**
     * @param $id
     * @param $user
     * @return mixed
     */
    public function getFinalScores($id, $user);

    /**
     * @param $reportId
     * @param $userId
     * @return mixed
     */
    public function hasReviewerParticipated($reportId, $userId);

    /**
     * @param $reportId
     * @param $userId
     * @return mixed
     */
    public function getReviewerScores($reportId, $userId);
}
