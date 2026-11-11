<?php

namespace App\Modules\Academic\Infrastructure;

use App\Doctorado;
use App\Models\Faculty;
use App\Modules\Academic\Application\Dto\FacultyData;
use App\Modules\Academic\Application\Port\FacultyRepository;

final class EloquentFacultyRepository implements FacultyRepository
{
    public function find(int $id): ?FacultyData
    {
        $faculty = Faculty::find($id);

        return $faculty === null ? null : $this->toData($faculty);
    }

    public function nameTaken(string $name, ?int $exceptId = null): bool
    {
        return Faculty::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($exceptId !== null, fn ($q) => $q->whereKeyNot($exceptId))
            ->exists();
    }

    public function create(string $name, ?string $description): FacultyData
    {
        return $this->toData(Faculty::create(['name' => $name, 'description' => $description]));
    }

    public function update(int $id, string $name, ?string $description): FacultyData
    {
        $faculty = Faculty::findOrFail($id);
        $faculty->update(['name' => $name, 'description' => $description]);

        return $this->toData($faculty);
    }

    public function hasPrograms(int $id): bool
    {
        return Doctorado::where('faculty_id', $id)->exists();
    }

    public function delete(int $id): void
    {
        Faculty::whereKey($id)->delete();
    }

    private function toData(Faculty $faculty): FacultyData
    {
        return new FacultyData($faculty->id, $faculty->name, $faculty->description);
    }
}
