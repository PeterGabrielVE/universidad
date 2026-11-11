<?php

namespace App;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Programa académico (tabla histórica "doctorados"). Pertenece a una facultad y
 * define su nota mínima aprobatoria (FUNCTIONAL_SPECIFICATIONS.md §3, §13).
 */
class Doctorado extends Model
{
    use HasFactory;

    protected $table = 'doctorados';

    protected $fillable = [
       'id', 'faculty_id', 'name', 'description', 'min_passing_grade', 'created_at',
    ];

    protected $casts = [
        'min_passing_grade' => 'float',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
}
