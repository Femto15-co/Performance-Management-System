<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $fillable = array(
        'user_id'
    );

    //Report has many scores, it link to performance rules in a many to many relationship
    public function scores()
    {
        return $this->belongsToMany('App\PerformanceRule', 'scores', 'report_id', 'rule_id')->withPivot('score', 'reviewer_id');
    }

    //Report has one employee, the person being reviewed
    public function employee()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
