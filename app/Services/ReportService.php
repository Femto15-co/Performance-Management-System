<?php
namespace App\Services;

use App\PerformanceRule;
use App\Report;
use App\Repositories\PerformanceRule\PerformanceRuleInterface;
use App\Repositories\Report\ReportInterface;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mockery\CountValidator\Exception;

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
     * PerformanceRule Repository
     * @var PerformanceRuleInterface
     */
    public $performanceRuleRepository;

    public function __construct(ReportInterface $reportRepository, PerformanceRuleInterface $performanceRuleRepository)
    {
        $this->redirectTo = null;
        $this->reportRepository = $reportRepository;
        $this->performanceRuleRepository = $performanceRuleRepository;
    }

    /**
     * Add new report
     * @param integer $employee to add a report for
     * @param $scores
     * @param $rules
     * @throws \Exception
     */
    public function addReport($employee, $scores, $rules)
    {
        //Get the max possible score
        $maxScore=$this->performanceRuleRepository->getMaxScoreByType($employee->employee_type);

        //Create Report
        $report = $this->reportRepository->create(['user_id'=>$employee->id,'max_score'=>intval($maxScore)]);

        //Attach scores to report
        $this->addScores($scores, $rules, $report, $employee);
    }

    /**
     * Participate in stored report, if employee being reviewed is participating
     * consider his evaluation and close the report
     * @param $id
     * @param $scores
     * @param $rules
     * @throws \Exception
     */
    public function reportParticipate($id, $scores, $rules)
    {
        $report = $this->reportRepository->getReportById($id);

        $this->canParticipate($report, Auth::user());

        $this->openModification($report);

        $employee = $this->getReportEmployee($report);

        //Attach scores to report
        $this->addScores($scores, $rules, $report, $employee);

        //If employee is evaluating himself, calculate overall score and prevent further participation
        if($employee->id == Auth::id())
        {
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
     * @throws \Exception
     */
    public function updateReport($id, $scores, $rules)
    {
        $report = $this->reportRepository->getReportById($id);

        $this->openModification($report);
        $employee = $this->getReportEmployee($report);

        $update = true;
        $this->addScores($scores, $rules, $report, $employee, $update);
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

        foreach($avgScores as $ruleScore)
        {
            try
            {
                $rule= $this->performanceRuleRepository->getRuleById($ruleScore->rule_id);
            }
            catch(Exception $e)
            {
                continue;
            }

            $overallScore += $ruleScore->avg_score*$rule->weight;
        }

        //Update report with the overall Score
        $this->reportRepository->update($report->id, ['overall_score'=>$overallScore]);
    }

    /**
     * Attach scores to report, one reviewer at a time
     * @param Array $scores set of scores
     * @param Array $rules set of rules to be scored
     * @param Report $report Report Model instance
     * @param $employee to attach scores for
     * @return bool true on success
     */
    private function addScores($scores, $rules, $report, $employee, $update=false)
    {
        $i = 0;

        foreach($rules as $ruleId)
        {
            //If no score is paired with that rule, abort
            if(!isset($scores[$i]))
                break;


            //If rule doesn't belong to selected employees related rules, ignore and continue
            $validRule = PerformanceRule::where('employee_type', $employee->employee_type)->where('id', $ruleId)->exists();
            if(!$validRule)
                continue;

            // If score are being update, just update them
            if($update)
            {
                //Update pivot with new scores with their ordering
                $report->scores()->where('rule_id', $ruleId)->where('reviewer_id', Auth::id())
                    ->update(['score'=>$scores[$i++]]);
                continue;
            }

            /*
             * Otherwise, ensure that rule is not duplicated for the report and then add
             */

            //Check first that no record contains same reportId, reviewerId and ruleId
            $foundDuplicate = $report->scores()->where('rule_id',$ruleId)->where('reviewer_id', Auth::id())->count();

            if(!$foundDuplicate)
            {
                $report->scores()->attach([$ruleId => ['reviewer_id'=>Auth::id(), 'score'=>$scores[$i++]]]);
            }
        }
    }

    /**
     * Get employee being reviewed
     * @param $report
     * @throws \Exception
     * @return \App\User Employee being reviewed
     */
    public function getReportEmployee($report)
    {
        if(!$employee = $report->employee()->first()) {
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
        $reviewerParticipated = $report->scores()->where('reviewer_id', $user->id)->exists();

        if($reviewerParticipated)
        {
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
        if(!$report->overall_score)
            throw new \Exception(trans('reports.not_found'));

        if (!$user->hasRole('admin') && $report->user_id != $user->id) {
            throw new \Exception(trans('reports.not_found'));
        }
    }

    /**
     * Get scores recorded by authenticated user who attempted edit
     * @param $report
     * @return mixed scores
     * @throws \Exception
     */
    public function getReviewerScores($report, $user)
    {
        $ruleScores = $report->scores()->where('reviewer_id', $user->id)->get();

        if($ruleScores->isEmpty())
        {
            throw new \Exception(trans('reports.no_scores_recorded'));
        }

        return $ruleScores;
    }
}