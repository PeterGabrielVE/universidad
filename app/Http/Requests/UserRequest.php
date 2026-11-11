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

    /** Un <select> sin elegir envía un valor vacío: no es un programa. */
    protected function prepareForValidation(): void
    {
        if ($this->has('program_ids')) {
            $this->merge([
                'program_ids' => array_values(array_filter(
                    (array) $this->input('program_ids'),
                    fn ($id) => $id !== null && $id !== ''
                )),
            ]);
        }
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
            'program_ids' => ['nullable', 'array'],
            'program_ids.*' => ['integer', 'distinct', 'exists:doctorados,id'],
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
            programIds: array_map('intval', (array) $this->input('program_ids', [])),
            password: $this->filled('password') ? (string) $this->input('password') : null,
        );
    }
}
