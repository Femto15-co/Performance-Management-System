<?php

namespace App\Repositories\Report;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * ReportRepository is a class that contains common queries for reports
 */
class ReportRepository extends BaseRepository implements ReportInterface
{

    /**
     * Holds report model
     * @var Model
     */
    protected $reportModel;

    /**
     * ReportRepository constructor.
     * Inject whatever passed model
     * @param Model $report
     */
    public function __construct(Model $report)
    {
        $this->reportModel = $report;
    }

    /**
     * Retrieves report by id
     * @param $id
     * @return Model report
     * @throws \Exception
     */
    public function getReportById($id)
    {
        $report = $this->reportModel->find($id);

        if(!$report)
        {
            throw new \Exception(trans('reports.not_found'));
        }
        return $report;
    }

    /**
     * Create new report
     * @param $data array of key-value pairs
     * @return \App\Report
     * @throws \Exception
     */
    public function create($data)
    {
        $report = $this->reportModel->create($data);
        if(!$report)
        {
            throw new \Exception(trans('reports.not_created'));
        }
        return $report;
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
        $avgScores = DB::table('scores')->select('rule_id',DB::raw( 'AVG(score) as avg_score' ))->where('report_id',$id)
            ->where('reviewer_id', '!=', $user->id)->groupBy('rule_id')->get();

        if(!$avgScores)
        {
            throw new \Exception(trans('reports.no_prior_evaluation'));
        }

        return $avgScores;
    }

    /**
     * update report
     * @param $data
     * @throws \Exception
     */
    public function update($id, $data, $attribute = "id")
    {
        if(!$this->reportModel->where($attribute, '=', $id)->update($data))
        {
            throw new \Exception('reports.not_updated');
        }
    }
}