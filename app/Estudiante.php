<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'students';

    protected $fillable = [
       'user_id','lapse_id','country_id','equivalency','status','created_at','updated_at'
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function lapso_estudiante()
    {
        return $this->belongsTo('App\Lapso_Estudiante','id','student_id');
    }
}
