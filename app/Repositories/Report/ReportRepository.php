<?php

namespace App\Repositories\Report;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * ReportRepository is a class that contains common queries for reports
 */
class ReportRepository extends BaseRepository implements ReportInterface
{
    /**
     * ReportRepository constructor.
     * Inject whatever passed model
     * @param Model $report
     */
    public function __construct(Model $report)
    {
        $this->setModel($report);
        $this->originalModel = $this->getModel();
    }

    /**
     * Calculate average scores and overall score by averaging all scores for
     * that report excluding user's own review
     * @param $id report id
     * @param $user
     * @throws \Exception
     * @return mixed
     */
    public function getFinalScores($id, $user)
    {
        $avgScores = DB::table('scores')
            ->select('rule_id', DB::raw('AVG(score) as avg_score'), 'performance_rules.weight')
            ->join('performance_rules', 'scores.rule_id', 'performance_rules.id')
            ->where('report_id', $id)
            ->where('reviewer_id', '!=', $user->id)->groupBy('rule_id')->get();

        if (!$avgScores) {
            throw new \Exception(trans('reports.no_prior_evaluation'));
        }
        return $avgScores;
    }

    /**
     * returns true if reviewer already participated
     * @param $reportId
     * @param $userId
     * @return bool
     */
    public function hasReviewerParticipated($reportId, $userId)
    {
        return $this->getModel()->where('id', $reportId)->hasReviewed($userId)->exists();
    }

    /**
     * Get report with scores recorded by authenticated user who attempted edit
     * @param $reportId
     * @param $userId
     * @param array $relations to eager load
     * @return mixed scores
     * @throws \Exception
     */
    public function getReviewerScores($reportId, $userId, $relations = [])
    {
        $reportWithScores = $this->getModel()->where('id', $reportId)->hasReviewed($userId)
            ->withReviewer($userId)->with($relations)->first();

        if (!$reportWithScores) {
            throw new \Exception(trans('reports.no_scores_recorded'));
        }

        return $reportWithScores;
    }

    /**
     * Update score for a given rule and user
     * @param $ruleId
     * @param $score
     * @param $loggedId
     * @return mixed
     */
    public function updateScore($ruleId, $score, $loggedId)
    {
        $this->ensureBooted();

        return $this->getModel()->scores()->where('rule_id', $ruleId)->where('reviewer_id', $loggedId)
            ->update(['score' => $score]);
    }

    /**
     * get average score for a report
     * @param $id
     * @param $reportUserId
     * @return mixed
     */
    public function getAvgScore($id, $reportUserId)
    {
        $avgScores = DB::table('scores')->select('rule_id', DB::raw('AVG(score) as avg_score'))->where('report_id', $id)
            ->where('reviewer_id', '!=', $reportUserId)->groupBy('rule_id')->get()->groupBy('rule_id')->toArray();

        return $avgScores;
    }

    /**
     * Ensure score is unique for a given user and id
     * @param $ruleId
     * @param $loggedUserId
     */
    public function ensureScoreUniqueness($ruleId, $loggedUserId)
    {
        $this->ensureBooted();

        return $this->getModel()->scores()->where('rule_id', $ruleId)->where('reviewer_id', $loggedUserId)->count();
    }

    /**
     * Attach score to a report for a reviewer
     * @param $ruleId
     * @param $loggedUserId
     * @param $score
     * @return mixed
     */
    public function attachScore($ruleId, $loggedUserId, $score)
    {
        $this->ensureBooted();

        return $this->getModel()->scores()
            ->attach([$ruleId => ['reviewer_id' => $loggedUserId, 'score' => $score]]);
    }

    /**
     * Get Reports for a specified user or for all users if current
     * logged in user is admin
     * @param $userId
     * @param integer $loggedInUserId
     * @param bool $isAdmin
     * @return mixed
     */
    public function getReportsForAUserScope($isAdmin, $loggedInUserId, $userId = null)
    {
        $reports = $this->getModel()->join('users', 'reports.user_id', '=', 'users.id')
            ->select([
                'reports.id', 'reports.user_id', 'users.name', 'reports.overall_score',
                'reports.max_score', 'reports.created_at'
            ]);

        //If user is not admin, load users reports only
        if (!$isAdmin) {
            return $reports->where('reports.user_id', $loggedInUserId);
        }

        //Otherwise, display user's related reports only if any user ID
        if ($userId) {
            return $reports->where('reports.user_id', $userId);
        }

        return $reports;
    }

    /**
     * get the comment written by the logged in user on report
     * @param  Report $report
     * @return Comment or null if there is no Comment
     */
    public function getUserComment($report)
    {
        $comment = $report->comments->where('user_id', Auth::id())->first();
        if ($comment) {
            return $comment;
        }
        return null;
    }

    /**
     * gets all comments on report
     * @param  Report $report all report data
     * @return Collection     collection of reports
     */
    public function getAllComments($report)
    {
        return $report->comments;
    }
}
