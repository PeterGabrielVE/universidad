<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Forma de los datos del expediente. Qué campos se aplican según quién edita
 * (estudiante o personal) lo decide el controlador y ManageStudentRecords.
 */
class StudentRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'sede_id' => ['nullable', 'integer', 'exists:headquarters,id'],
            'equivalency' => ['nullable', 'boolean'],
            'admission_lapse_id' => ['nullable', 'integer'],
        ];
    }

    public function phone(): ?string
    {
        return $this->filled('phone') ? (string) $this->input('phone') : null;
    }

    public function countryId(): ?int
    {
        return $this->filled('country_id') ? $this->integer('country_id') : null;
    }

    public function campusId(): ?int
    {
        return $this->filled('sede_id') ? $this->integer('sede_id') : null;
    }

    public function admissionLapseId(): ?int
    {
        return $this->filled('admission_lapse_id') ? $this->integer('admission_lapse_id') : null;
    }
}
