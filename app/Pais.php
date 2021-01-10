<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'countries';

    protected $fillable = [
       'code','name','created_at','updated_at'
    ];
}
