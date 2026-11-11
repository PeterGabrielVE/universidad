<?php

namespace App\Http\Requests;

use App\Modules\Academic\Domain\LapseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Forma de los datos de un lapso. Las reglas (fechas coherentes, transiciones,
 * cierre, alcance por programa) las aplica ManageLapses.
 */
class LapseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('POST') && $this->routeIs('lapso.store');

        return [
            'program_id' => [$creating ? 'required' : 'prohibited', 'integer'],
            'start_lapse' => ['required', 'date_format:Y-m-d'],
            'end_lapse' => ['required', 'date_format:Y-m-d'],
            'status' => ['required', Rule::in($creating
                ? [LapseStatus::Planificacion->value, LapseStatus::Abierto->value]
                : LapseStatus::values())],
        ];
    }

    public function status(): LapseStatus
    {
        return LapseStatus::from((string) $this->input('status'));
    }
}
