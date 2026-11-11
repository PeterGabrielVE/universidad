<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Cuenta de usuario: identidad, rol y estado. La contraseña no forma parte de
 * la entidad: el repositorio recibe el hash por separado.
 */
final class UserAccount
{
    private function __construct(
        private ?int $id,
        private string $name,
        private string $lastName,
        private ?int $identificationCard,
        private Email $email,
        private ?string $phone,
        private string $role,
        private AccountStatus $status,
        private ?int $programId,
    ) {
    }

    public static function create(
        string $name,
        string $lastName,
        ?int $identificationCard,
        Email $email,
        ?string $phone,
        string $role,
        AccountStatus $status,
        ?int $programId,
    ): self {
        return new self(null, $name, $lastName, $identificationCard, $email, $phone, $role, $status, $programId);
    }

    /** Reconstruye una cuenta ya persistida. Solo para adaptadores de persistencia. */
    public static function restore(
        int $id,
        string $name,
        string $lastName,
        ?int $identificationCard,
        Email $email,
        ?string $phone,
        string $role,
        AccountStatus $status,
        ?int $programId,
    ): self {
        return new self($id, $name, $lastName, $identificationCard, $email, $phone, $role, $status, $programId);
    }

    public function updateProfile(
        string $name,
        string $lastName,
        ?int $identificationCard,
        Email $email,
        ?string $phone,
        ?int $programId,
    ): void {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->identificationCard = $identificationCard;
        $this->email = $email;
        $this->phone = $phone;
        $this->programId = $programId;
    }

    public function changeRole(string $role): void
    {
        $this->role = $role;
    }

    public function changeStatus(AccountStatus $status): void
    {
        $this->status = $status;
    }

    public function isStudent(): bool
    {
        return $this->role === RoleName::Estudiante->value;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function identificationCard(): ?int
    {
        return $this->identificationCard;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function status(): AccountStatus
    {
        return $this->status;
    }

    public function programId(): ?int
    {
        return $this->programId;
    }
}
