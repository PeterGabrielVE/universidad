<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapso extends Model
{
    use HasFactory;

    protected $table = 'lapses';

    protected $fillable = [
       'name','start_lapse','end_lapse','status'
    ];
}
