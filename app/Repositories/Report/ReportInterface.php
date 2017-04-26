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

    /**
     * Get Reports for a specified user or for all users if current
     * logged in user is admin
     * @param $userId
     * @param integer $loggedInUserId
     * @param bool $isAdmin
     * @return mixed
     */
    public function getReportsForAUserScope($isAdmin, $loggedInUserId, $userId = null);

    /**
     * Attach score to a report for a reviewer
     * @param $ruleId
     * @param $loggedUserId
     * @param $score
     * @return mixed
     */
    public function attachScore($ruleId, $loggedUserId, $score);

    /**
     * Ensure score is unique for a given user and id
     * @param $ruleId
     * @param $loggedUserId
     */
    public function ensureScoreUniqueness($ruleId, $loggedUserId);

    /**
     * Update score for a given rule and user
     * @param $ruleId
     * @param $score
     * @param $loggedId
     * @return mixed
     */
    public function updateScore($ruleId, $score, $loggedId);

    /**
     * get average score for a report
     * @param $id
     * @param $reportUserId
     * @return mixed
     */
    public function getAvgScore($id, $reportUserId);

}
