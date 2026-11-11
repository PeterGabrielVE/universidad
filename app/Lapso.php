<?php

namespace App;

use App\Modules\Academic\Domain\LapseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lapso o cohorte. Las reglas (estados, cierre) están en
 * App\Modules\Academic\Domain\Lapse; este modelo es solo persistencia y lectura.
 */
class Lapso extends Model
{
    use HasFactory;

    protected $table = 'lapses';

    protected $fillable = [
       'program_id', 'name', 'start_lapse', 'end_lapse', 'status',
    ];

    protected $casts = [
        'status' => LapseStatus::class,
        'passing_grade_applied' => 'float',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Doctorado::class, 'program_id');
    }
}
