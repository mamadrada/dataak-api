<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Users\EloquentUser;

class Activity extends Model
{
    protected $table = 'activity_log';
    protected $fillable = ['*'];


    public function getUser()
    {
        return $this->hasOne('App\EloquentUser', 'id', 'causer_id');
    }

    public function getSubject()
    {
        return $this->hasOne('App\EloquentUser', 'id', 'causer_id');
    }
}
