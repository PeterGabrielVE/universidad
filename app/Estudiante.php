<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
       'user_id','lapse_id','country_id','equivalency','status','created_at','updated_at'
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function lapso_estudiante()
    {
        return $this->belongsTo('App\Lapso_Estudiante','id','student_id');
    }
}
