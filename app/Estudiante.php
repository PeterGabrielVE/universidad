<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'students';

    protected $fillable = [
       'user_id','status','created_at','updated_at'
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
