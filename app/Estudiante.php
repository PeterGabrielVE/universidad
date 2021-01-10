<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'students';

    protected $fillable = [
       'first_name','last_name','identification_card','email','phone','lapse_id','country_id','equivalency','status'
    ];

     public function paises()
    {
        return $this->belongsTo('App\Pais', 'country_id');
    }

    public function lapsos()
    {
        return $this->belongsTo('App\Lapso', 'lapse_id');
    }
}
