<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    protected $fillable = ['date', 'project_id', 'user_id', 'duration', 'description'];
}
