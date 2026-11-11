<?php

namespace App\Http\Requests;

use App\Modules\Academic\Application\Dto\ProgramInput;
use App\Modules\Academic\Domain\PassingGrade;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Forma de un programa. Nombre único y facultad existente los comprueba ManagePrograms.
 */
class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'faculty_id' => ['required', 'integer'],
            'min_passing_grade' => ['required', 'numeric', 'between:0,100'],
        ];
    }

    public function toInput(): ProgramInput
    {
        return new ProgramInput(
            name: $this->string('name')->trim()->value(),
            description: $this->filled('description') ? $this->string('description')->trim()->value() : null,
            facultyId: $this->integer('faculty_id'),
            passingGrade: PassingGrade::of((string) $this->input('min_passing_grade')),
        );
    }
}
