<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Forma de los datos de un rol. La unicidad del nombre y la protección de los
 * roles del sistema las aplica ManageRoles. El guard es siempre "web".
 */
class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:125'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function roleName(): string
    {
        return $this->string('name')->trim()->value();
    }

    public function description(): ?string
    {
        return $this->filled('description') ? $this->string('description')->trim()->value() : null;
    }
}
