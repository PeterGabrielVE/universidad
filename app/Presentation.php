<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    protected $table = 'presentations';

    protected $fillable = [
       'extenso','carta_aceptacion','poster','certificado','student_id','created_at','updated_at'
    ];
}
