<?php

namespace App\Models;

use App\Doctorado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Facultad: agrupa varios programas (1:N). No tiene rol propio (Decano) por ahora.
 */
class Faculty extends Model
{
    protected $fillable = ['name', 'description'];

    public function programs(): HasMany
    {
        return $this->hasMany(Doctorado::class, 'faculty_id');
    }
}
