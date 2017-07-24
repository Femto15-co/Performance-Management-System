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
     * @param array $relations to eager load
     * @return mixed
     */
    public function getReviewerScores($reportId, $userId, $relations = []);

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

    /**
     * get the comment written by the logged in user on report
     * @param  Report $report
     * @return Comment or null if there is no Comment
     */
    public function getUserComment($report);

    /**
     * gets all comments on report
     * @param  Report $report all report data
     * @return Collection     collection of reports
     */
    public function getAllComments($report);
    
}
