<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','employee_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function defects()
    {
        return $this->belongsToMany('App\Defect')->withPivot('id','defect_id','user_id')->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany('App\Report');
    }
    public function bonuses()
    {
        return $this->hasMany('App\Bonus');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password']=Hash::make($value);
    }
}
