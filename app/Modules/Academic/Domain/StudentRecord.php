<?php

namespace App\Modules\Academic\Domain;

/**
 * Expediente del estudiante (FUNCTIONAL_SPECIFICATIONS.md §19).
 *
 * El estudiante solo actualiza sus datos de contacto. Sede, equivalencia y lapso
 * de ingreso los gestiona el personal. Identidad (nombre, cédula, correo) y
 * programas pertenecen a la cuenta y se editan en la gestión de usuarios.
 */
final class StudentRecord
{
    /** @param list<int> $programIds */
    private function __construct(
        private int $id,
        private int $userId,
        private array $programIds,
        private ?ContactPhone $phone,
        private ?int $countryId,
        private ?int $campusId,
        private bool $hasEquivalency,
        private ?int $admissionLapseId,
    ) {
    }

    /** @param list<int> $programIds */
    public static function restore(
        int $id,
        int $userId,
        array $programIds,
        ?ContactPhone $phone,
        ?int $countryId,
        ?int $campusId,
        bool $hasEquivalency,
        ?int $admissionLapseId,
    ): self {
        return new self($id, $userId, $programIds, $phone, $countryId, $campusId, $hasEquivalency, $admissionLapseId);
    }

    public function updateContact(?ContactPhone $phone, ?int $countryId): void
    {
        $this->phone = $phone;
        $this->countryId = $countryId;
    }

    public function updateAdministrative(?int $campusId, bool $hasEquivalency, ?int $admissionLapseId): void
    {
        $this->campusId = $campusId;
        $this->hasEquivalency = $hasEquivalency;
        $this->admissionLapseId = $admissionLapseId;
    }

    public function belongsTo(int $userId): bool
    {
        return $this->userId === $userId;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    /** @return list<int> */
    public function programIds(): array
    {
        return $this->programIds;
    }

    public function phone(): ?ContactPhone
    {
        return $this->phone;
    }

    public function countryId(): ?int
    {
        return $this->countryId;
    }

    public function campusId(): ?int
    {
        return $this->campusId;
    }

    public function hasEquivalency(): bool
    {
        return $this->hasEquivalency;
    }

    public function admissionLapseId(): ?int
    {
        return $this->admissionLapseId;
    }
}
