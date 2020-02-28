<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'date', 'time','active'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
