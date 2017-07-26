<?php

namespace App\Services;

use App\PerformanceRule;
use App\Report;
use App\Repositories\PerformanceRule\PerformanceRuleInterface;
use App\Repositories\Report\ReportInterface;
use App\Repositories\User\UserInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Comment\CommentInterface;

class ReportService
{
    /**
     * route to redirect to, initially null
     * @var string
     */
    public $redirectTo;

    /**
     * Report Repository
     * @var ReportInterface
     */
    public $reportRepository;

     /**
     * Comment Repository
     * @var CommentInterface
     */
    public $commentRepository;
    /**
     * PerformanceRule Repository
     * @var PerformanceRuleInterface
     */
    public $performanceRuleRepository;

    /**
     * User Repository
     * @var UserInterface
     */
    public $userRepository;

    public function __construct(
        ReportInterface $reportRepository,
        PerformanceRuleInterface $performanceRuleRepository,
        UserInterface $userRepository,
        CommentInterface $commentRepository
    ) {
        $this->redirectTo = null;
        $this->reportRepository = $reportRepository;
        $this->performanceRuleRepository = $performanceRuleRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Add new report
     * @param integer $employee to add a report for
     * @param $scores
     * @param $rules
     * @throws \Exception
     */
    public function addReport($employee, $scores, $rules, $comment)
    {
        //Get the max possible score
        $maxScore = $this->performanceRuleRepository->getMaxScoreByType($employee->employee_type);

        //Create Report
        $report = $this->reportRepository->addItem(['user_id' => $employee->id, 'max_score' => intval($maxScore)]);

        //Attach scores to report
        $this->addScores($scores, $rules, $report, $employee);

        //Check if there is a comment
        if(!empty($comment))
        {
            $this->addComment($comment, $report);
        }
    }
    /**
     * Create comment then attach it to report
     * @param string $comment comment text
     * @param Report $report  Report Object
     */
    public function addComment($comment, $report)
    {
        //Create Comment
        $comment_created = $this->commentRepository->addItem([
            'comment' => $comment,
            'user_id' =>Auth::id()
            ]);

        //Attach Comment to Report
        $report->comments()->attach($comment_created->id);        
    }
    /**
     * Participate in stored report, if employee being reviewed is participating
     * consider his evaluation and close the report
     * @param $id
     * @param $scores
     * @param $rules
     * @throws \Exception
     */
    public function reportParticipate($id, $scores, $rules, $comment)
    {
        $report = $this->reportRepository->getItem($id);

        $this->canParticipate($report, Auth::user());

        $this->openModification($report);

        $employee = $this->getReportEmployee($report);

        //Attach scores to report
        $this->addScores($scores, $rules, $report, $employee);

        //Check if there is a comment
        if(!empty($comment))
        {
            $this->addComment($comment, $report);
        }
        //If employee is evaluating himself, calculate overall score and prevent further participation
        if ($employee->id == Auth::id()) {
            $this->closeReport($report);
            $this->redirectTo = route('report.show', $report->id);
            return;
        }
        //Otherwise, report results is not yet ready to be viewed
        $this->redirectTo = route('report.index');
    }

    /**
     * Updates report scores given report Id
     * @param $id
     * @param $scores
     * @param $rules
     * @param $comment string new comment written
     * @throws \Exception
     */
    public function updateReport($id, $scores, $rules, $comment)
    {
        $report = $this->reportRepository->getItem($id, ['comments']);
        $this->openModification($report);
        $employee = $this->getReportEmployee($report);

        $update = true;
        $this->addScores($scores, $rules, $report, $employee, $update);

        //get user comment (return null if there is no comment before)
        $user_comment = $this->reportRepository->getUserComment($report);
        //update comment if existed
        if($user_comment)
        {
            //update comment
            $this->commentRepository->editItem($user_comment->id, ['comment' => $comment]);
            return;
        }

        //creates new comment if not comment isn't empty string
        if(!empty($comment))
        {
            $this->addComment($comment, $report);
        }
    }

    /**
     * If employee is evaluating himself, calculate overall score and prevent further participation
     * @param $report
     * @throws \Exception
     */
    public function closeReport($report)
    {
        //Calculate average scores and overall score by averaging all scores for that report excluding user's own review
        $avgScores = $this->reportRepository->getFinalScores($report->id, Auth::user());

        $overallScore = 0;

        foreach ($avgScores as $ruleScore) {
            $overallScore += $ruleScore->avg_score * $ruleScore->weight;
        }

        //Update report with the overall Score
        $this->reportRepository->editItem($report->id, ['overall_score' => $overallScore]);
    }

    /**
     * Attach scores to report, one reviewer at a time
     * @param Array $scores set of scores
     * @param Array $rules set of rules to be scored
     * @param Report $report Report Model instance
     * @param $employee to attach scores for
     * @return bool true on success
     */
    private function addScores($scores, $rules, $report, $employee, $update = false)
    {
        $i = 0;

        //Boot model before instructing update
        $this->reportRepository->setModel($report);

        foreach ($rules as $ruleId) {
            //If no score is paired with that rule, abort
            if (!isset($scores[$i])) {
                break;
            }

            //If rule doesn't belong to selected employees related rules, ignore and continue
            $validRule = $this->performanceRuleRepository->isRuleExistsForType($employee->employee_type, $ruleId);
            if (!$validRule) {
                continue;
            }

            // If score are being updated, just update them
            if ($update) {
                $this->reportRepository->updateScore($ruleId, $scores[$i++], Auth::id());

                continue;
            }

            /*
             * Otherwise, ensure that rule is not duplicated for the report and then add
             */
            //Check first that no record contains same reportId, reviewerId and ruleId
            $foundDuplicate = $this->reportRepository->ensureScoreUniqueness($ruleId, Auth::id());

            if (!$foundDuplicate) {
                $this->reportRepository->attachScore($ruleId, Auth::id(), $scores[$i++]);
            }
        }

        //un-boot model
        $this->reportRepository->resetModel();
    }

    /**
     * Get employee being reviewed
     * @param $report
     * @throws \Exception
     * @return \App\User Employee being reviewed
     */
    public function getReportEmployee($report)
    {
        if (!$employee = $report->employee()->first()) {
            throw new \Exception(trans('reports.not_found'));
        }
        return $employee;
    }

    /**
     * Is user allowed to participate in report
     * Only admin and user being evaluated can participate
     * @param \App\Report $report
     * @param \App\User $user
     * @throws \Exception
     */
    public function canParticipate($report, $user)
    {
        if (!$user->hasRole('admin') && $report->user_id != $user->id) {
            throw new \Exception(trans('reports.no_participation'));
        }

        $this->participated($report, $user);
    }

    /**
     * If participated before, throw exception
     * @throws \Exception
     */
    private function participated($report, $user)
    {
        $reviewerParticipated = $this->reportRepository->hasReviewerParticipated($report->id, $user->id);

        if ($reviewerParticipated) {
            throw new \Exception(trans('reports.no_participation'));
        }
    }

    /**
     * If report overall score is set, no longer participation is allowed
     * @param $report
     * @throws \Exception
     */
    public function openModification($report)
    {
        if ($report->overall_score) {
            throw new \Exception(trans('reports.no_operation'));
        }
    }

    /**
     * Is view allowed given report and user who's trying to view
     * @param $report
     * @param $user
     * @throws \Exception
     */
    public function allowedView($report, $user)
    {
        if (!$report->overall_score) {
            throw new \Exception(trans('reports.not_found'));
        }

        if (!$user->hasRole('admin') && $report->user_id != $user->id) {
            throw new \Exception(trans('reports.not_found'));
        }
    }

    /**
     * Returns reviews matrix for a final report
     * @param $reportWithScores
     * @return array
     * @throws \Exception
     */
    public function getReviewsMatrix($reportWithScores)
    {
        if (empty($reportWithScores->scores)) {
            throw new \Exception('reports.no_scores_recorded');
        }

        foreach ($reportWithScores->scores as $score) {
            $reviewersScores[$score->pivot->rule_id][$score->pivot->reviewer_id] = $score->pivot->score;

            //List all unique reviewers
            $reviewers[$score->pivot->reviewer_id] = $this->userRepository->getItem($score->pivot->reviewer_id);

            //List all unique rules
            $rules[$score->pivot->rule_id] = $this->performanceRuleRepository->getItem($score->pivot->rule_id);
        }

        //Get avg score if overall score is set
        $avgScores = $this->reportRepository->getAvgScore($reportWithScores->id, $reportWithScores->user_id);

        //if no rules, reviewers or reviewer scores register, abort
        if (empty($reviewersScores) || empty($rules) || empty($reviewers)) {
            throw new \Exception(trans('reports.not_found'));
        }

        return [
            'reviewers'=> $reviewers,
            'reviewersScores'=> $reviewersScores,
            'rules'=>$rules,
            'avgScores'=>$avgScores
        ];
    }

    /**
     * Generate dataTable Controllers
     * @param $report
     * @param $loggedInUser
     * @return string
     */
    public function dataTableControllers($report, $loggedInUser)
    {
        try {
            $viewLink = "";
            $this->openModification($report);
        } catch (\Exception $e) {
            //Show link, show only if overall score is defined
            $viewLink = "<a href=" . route('report.show', $report->id) . " class='btn btn-xs btn-success'>
                    <i class='glyphicon glyphicon-eye-open'></i> " . trans('reports.final_report') . "</a>&nbsp;";
        }

        try {
            //Show participate link if only user can participate
            $this->canParticipate($report, $loggedInUser);
            //Still open for participation?
            $this->reportService->openModification($report);

            $participateLink =
                "<a href=" . route('report.getParticipate', $report->id) .
                " class='btn btn-xs btn-success'><i class='glyphicon glyphicon-pencil'></i> " .
                trans('reports.participate') .
                "</a>&nbsp;";

        } catch (\Exception $e) {
            $participateLink = "";
        }

        // Edit link, show while overall score is not defined
        // and admin or reviewer has already participated in the evaluation process
        $editLink = "";

        //returns true if reviewer participated in the evaluation process
        $reviewerParticipated = $this->reportRepository->hasReviewerParticipated($report->id, $loggedInUser->id);

        if (!$report->overall_score && $reviewerParticipated && $loggedInUser->hasRole('admin')) {
            $editLink = "<a href=" . route('report.edit', $report->id) .
                " class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-edit'></i>" .
                trans('general.edit') .
                "</a>";
        }

        //Delete form, show if admin
        $deleteForm = "";
        $formHead = "";
        if ($loggedInUser->hasRole('admin')) {
            $formHead = "<form class='delete-form' method='POST' action='" .
                route('report.destroy', $report->id) . "'>" . csrf_field();

            $deleteForm =
                "  <input type='hidden' name='_method' value='DELETE'/>
                        <button type='submit' class='btn btn-xs btn-danger main_delete'>
                            <i class='glyphicon glyphicon-trash'></i> " . trans('general.delete') . "
                        </button>
                    </form>";
        }

        return $formHead . $viewLink . $editLink . $participateLink . $deleteForm;
    }
}