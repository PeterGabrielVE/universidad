<?php

namespace App\Models;

use App\Doctorado;
use App\Estudiante;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Pais;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Único modelo de usuario (autenticación web, Sanctum y Spatie). Sustituye a
 * App\User, que coexistía sobre la misma tabla con otro $fillable.
 *
 * Los roles viven solo en Spatie y los programas en program_user; las columnas
 * users.rol_id y users.doctorado_id quedaron obsoletas.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'last_name',
        'identification_card',
        'phone',
        'email',
        'password',
        'status',
        'sede_id',
        'country_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // "hashed" no vuelve a hashear un valor que ya es un hash.
        'password' => 'hashed',
        'status' => AccountStatus::class,
    ];

    protected $attributes = [
        'status' => 'activo',
    ];

    /**
     * Programas del usuario (N:M). Sustituye a la columna users.doctorado_id y a su
     * comodín 3 = "ambos" (FUNCTIONAL_SPECIFICATIONS.md §15).
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Doctorado::class, 'program_user', 'user_id', 'program_id')->withTimestamps();
    }

    public function belongsToProgram(int $programId): bool
    {
        return $this->programs->contains('id', $programId);
    }

    public function paises(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'country_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'country_id');
    }

    /** Prefijo telefónico del país (columna cod_phone; ver PROJECT_STATUS.md, inventario #14). */
    public function prefijo(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'cod_phone');
    }

    public function studentRecord(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'user_id');
    }
}
