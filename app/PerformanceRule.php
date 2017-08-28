<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerformanceRule extends Model
{
     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    protected $fillable = ['rule', 'desc', 'weight', 'employee_type'];
}
