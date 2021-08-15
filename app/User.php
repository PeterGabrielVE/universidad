<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','rol_id','sede_id','doctorado_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function doctorado()
    {
        return $this->belongsTo('App\Doctorado', 'doctorado_id');
    }

    public function paises()
    {
        return $this->belongsTo('App\Pais', 'country_id');
    }

    public function lapsos()
    {
        return $this->belongsTo('App\Lapso', 'lapse_id');
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais', 'country_id');
    }

    public function prefijo()
    {
        return $this->belongsTo('App\Pais', 'cod_phone');
    }

}
