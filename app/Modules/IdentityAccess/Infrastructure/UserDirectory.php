<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Models\User;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Domain\RoleName;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Consultas de lectura sobre usuarios (listados, filtros, alcance por programa).
 * Es un lado de lectura: no pasa por el dominio porque no aplica reglas que cambien datos.
 */
final class UserDirectory
{
    /**
     * @return list<array{id: int, name: string, last_name: ?string, email: string, role: ?string, status: string, program_ids: list<int>}>
     */
    public function search(?string $role, ?string $term): array
    {
        return User::query()
            ->with(['roles', 'programs'])
            ->when($role, fn ($q) => $q->role($role))
            ->when($term, function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like));
            })
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->summary($user))
            ->all();
    }

    /** @return array{id: int, name: string, last_name: ?string, email: string, role: ?string, status: string, program_ids: list<int>} */
    public function summary(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name,
            'status' => $user->status->value,
            'program_ids' => $user->programs->pluck('id')->all(),
        ];
    }

    public function countByRole(RoleName $role): int
    {
        return User::role($role->value)->count();
    }

    /** Alcance por programa del usuario autenticado (solo limita al Coordinador). */
    public function programScopeOf(Authenticatable $user): ProgramScope
    {
        /** @var User $user */
        $role = RoleName::tryFrom((string) $user->roles->first()?->name);

        return $role === null
            ? ProgramScope::forStaff(RoleName::Coordinador, [])
            : ProgramScope::forStaff($role, $user->programs()->pluck('doctorados.id')->all());
    }
}
