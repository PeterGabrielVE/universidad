<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name','guard_name','description','created_at','updated_at'];
}
