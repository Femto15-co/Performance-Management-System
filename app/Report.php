<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //Report has many scores, it link to performance rules in a many to many relationship
    public function scores()
    {
        return $this->belongsToMany('App/PerformanceRule', 'scores', 'report_id', 'rule_id')->withPivot('score', 'reviewer_id');
    }
}
