<?php

namespace App\Http\Requests;

use App\Modules\IdentityAccess\Application\Dto\UserData;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Forma de los datos de alta/edición de usuarios (web y API). Las reglas de
 * negocio (correo único, rol existente) las aplica el caso de uso.
 */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización la hace el middleware de permisos de la ruta.
        return true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('POST');

        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'identification_card' => ['required', 'integer', 'min:1'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'max:125'],
            'status' => ['nullable', Rule::in(AccountStatus::values())],
            'doctorado_id' => ['nullable', 'integer', 'exists:doctorados,id'],
            'password' => [$creating ? 'required' : 'nullable', 'string', 'min:8'],
        ];
    }

    public function toUserData(): UserData
    {
        return new UserData(
            name: $this->string('name')->trim()->value(),
            lastName: $this->string('last_name')->trim()->value(),
            identificationCard: $this->integer('identification_card'),
            email: $this->string('email')->value(),
            phone: $this->filled('phone') ? $this->string('phone')->trim()->value() : null,
            role: $this->string('role')->value(),
            status: AccountStatus::tryFrom((string) $this->input('status')) ?? AccountStatus::Activo,
            programId: $this->filled('doctorado_id') ? $this->integer('doctorado_id') : null,
            password: $this->filled('password') ? (string) $this->input('password') : null,
        );
    }
}
