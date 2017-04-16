<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $fillable=['description','value','user_id'];
}
