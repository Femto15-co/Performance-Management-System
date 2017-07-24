<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $fillable = array(
        'user_id', 'max_score', 'overall_score'
    );

    //Report has many scores, it link to performance rules in a many to many relationship
    public function scores()
    {
        return $this->belongsToMany('App\PerformanceRule', 'scores', 'report_id', 'rule_id')
            ->withPivot('score', 'reviewer_id');
    }
    //Report has many comments from different users
    public function comments()
    {
        return $this->belongsToMany('App\Comment','comment_report', 'report_id', 'comment_id');       
    }
    //Report has one employee, the person being reviewed
    public function employee()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Appends a filter of whether user has reviewed
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeHasReviewed($query, $userId)
    {
        return $query->whereHas('scores', function ($query) use ($userId) {
            $query->where('reviewer_id', $userId);
        });
    }

    public function scopeWithReviewer($query, $userId)
    {
        return $query->with(['scores' => function ($query) use ($userId) {
            $query->where('reviewer_id', $userId);
        }]);
    }
}
