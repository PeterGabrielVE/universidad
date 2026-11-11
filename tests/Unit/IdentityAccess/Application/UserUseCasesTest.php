<?php

namespace Tests\Unit\IdentityAccess\Application;

use App\Modules\IdentityAccess\Application\Dto\UserData;
use App\Modules\IdentityAccess\Application\UseCase\CreateUser;
use App\Modules\IdentityAccess\Application\UseCase\DeleteUser;
use App\Modules\IdentityAccess\Application\UseCase\RegisterApplicant;
use App\Modules\IdentityAccess\Application\UseCase\UpdateUser;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Modules\IdentityAccess\Domain\Exception\EmailAlreadyInUse;
use App\Modules\IdentityAccess\Domain\Exception\IdentificationCardAlreadyInUse;
use App\Modules\IdentityAccess\Domain\Exception\PasswordRequired;
use App\Modules\IdentityAccess\Domain\Exception\RoleNotFound;
use App\Modules\IdentityAccess\Domain\Exception\UserNotFound;
use PHPUnit\Framework\TestCase;
use Tests\Support\IdentityAccess\FakePasswordHasher;
use Tests\Support\IdentityAccess\ImmediateTransaction;
use Tests\Support\IdentityAccess\InMemoryRoleRepository;
use Tests\Support\IdentityAccess\InMemoryUserRepository;
use Tests\Support\IdentityAccess\SpyStudentRecords;

class UserUseCasesTest extends TestCase
{
    private InMemoryUserRepository $users;
    private SpyStudentRecords $studentRecords;
    private ImmediateTransaction $transaction;
    private CreateUser $createUser;
    private UpdateUser $updateUser;

    protected function setUp(): void
    {
        $this->users = new InMemoryUserRepository();
        $this->studentRecords = new SpyStudentRecords();
        $this->transaction = new ImmediateTransaction();
        $roles = InMemoryRoleRepository::withSystemRoles();
        $hasher = new FakePasswordHasher();

        $this->createUser = new CreateUser($this->users, $roles, $hasher, $this->studentRecords, $this->transaction);
        $this->updateUser = new UpdateUser($this->users, $roles, $hasher, $this->studentRecords, $this->transaction);
    }

    public function test_create_user_hashes_the_password_and_normalizes_the_email(): void
    {
        $account = $this->createUser->execute($this->data(email: ' Ana@Gabook.TEST'));

        $this->assertSame('ana@gabook.test', $account->email()->value);
        $this->assertSame('hashed:secreto123', $this->users->passwordHashes[$account->id()]);
        $this->assertSame(1, $this->transaction->runs);
    }

    public function test_creating_a_student_creates_the_student_record(): void
    {
        $account = $this->createUser->execute($this->data(role: 'Estudiante'));

        $this->assertSame([$account->id()], $this->studentRecords->ensuredFor);
    }

    public function test_creating_staff_does_not_create_a_student_record(): void
    {
        $this->createUser->execute($this->data(role: 'Coordinador'));

        $this->assertSame([], $this->studentRecords->ensuredFor);
    }

    public function test_duplicate_email_is_rejected_regardless_of_case(): void
    {
        $this->createUser->execute($this->data(email: 'ana@gabook.test'));

        $this->expectException(EmailAlreadyInUse::class);
        $this->createUser->execute($this->data(email: 'ANA@gabook.test'));
    }

    public function test_unknown_role_is_rejected(): void
    {
        $this->expectException(RoleNotFound::class);

        $this->createUser->execute($this->data(role: 'Rector'));
    }

    public function test_new_account_requires_a_password(): void
    {
        $this->expectException(PasswordRequired::class);

        $this->createUser->execute($this->data(password: null));
    }

    public function test_update_keeps_the_password_when_none_is_given(): void
    {
        $account = $this->createUser->execute($this->data());

        $this->updateUser->execute($account->id(), $this->data(name: 'Renombrada', password: null));

        $this->assertSame('Renombrada', $this->users->find($account->id())->name());
        $this->assertSame('hashed:secreto123', $this->users->passwordHashes[$account->id()]);
    }

    public function test_update_changes_role_and_status(): void
    {
        $account = $this->createUser->execute($this->data(role: 'Asistente'));

        $this->updateUser->execute($account->id(), $this->data(role: 'Coordinador', status: AccountStatus::Suspendido));

        $stored = $this->users->find($account->id());
        $this->assertSame('Coordinador', $stored->role());
        $this->assertSame(AccountStatus::Suspendido, $stored->status());
    }

    public function test_update_allows_keeping_own_email_but_not_taking_another(): void
    {
        $ana = $this->createUser->execute($this->data(email: 'ana@gabook.test'));
        $this->createUser->execute($this->data(email: 'luis@gabook.test'));

        $this->updateUser->execute($ana->id(), $this->data(email: 'ana@gabook.test', name: 'Ana María'));

        $this->expectException(EmailAlreadyInUse::class);
        $this->updateUser->execute($ana->id(), $this->data(email: 'luis@gabook.test'));
    }

    public function test_promoting_an_applicant_to_student_creates_the_record(): void
    {
        $account = $this->createUser->execute($this->data(role: 'Aspirante'));

        $this->updateUser->execute($account->id(), $this->data(role: 'Estudiante', password: null));

        $this->assertSame([$account->id()], $this->studentRecords->ensuredFor);
    }

    public function test_update_of_missing_user_fails(): void
    {
        $this->expectException(UserNotFound::class);

        $this->updateUser->execute(999, $this->data());
    }

    public function test_delete_removes_the_account_and_rejects_unknown_ids(): void
    {
        $account = $this->createUser->execute($this->data());
        $delete = new DeleteUser($this->users);

        $delete->execute($account->id());
        $this->assertNull($this->users->find($account->id()));

        $this->expectException(UserNotFound::class);
        $delete->execute($account->id());
    }

    public function test_public_registration_always_creates_an_active_applicant(): void
    {
        $register = new RegisterApplicant($this->createUser);

        $account = $register->execute('Rosa', 'Mendoza', 25123456, 'rosa@gabook.test', 'secreto123');

        $this->assertSame('Aspirante', $account->role());
        $this->assertSame(AccountStatus::Activo, $account->status());
        $this->assertSame([], $account->programIds());
        $this->assertSame([], $this->studentRecords->ensuredFor);
    }

    public function test_duplicate_identification_card_is_rejected(): void
    {
        $this->createUser->execute($this->data(email: 'ana@gabook.test', card: 20111222));

        $this->expectException(IdentificationCardAlreadyInUse::class);
        $this->createUser->execute($this->data(email: 'otra@gabook.test', card: 20111222));
    }

    public function test_update_cannot_take_another_persons_identification_card(): void
    {
        $ana = $this->createUser->execute($this->data(email: 'ana@gabook.test', card: 20111222));
        $this->createUser->execute($this->data(email: 'luis@gabook.test', card: 30111222));

        $this->updateUser->execute($ana->id(), $this->data(email: 'ana@gabook.test', card: 20111222, name: 'Ana María'));

        $this->expectException(IdentificationCardAlreadyInUse::class);
        $this->updateUser->execute($ana->id(), $this->data(email: 'ana@gabook.test', card: 30111222));
    }

    /** Por defecto la cédula se deriva del correo: misma persona → misma cédula. */
    private function data(
        string $name = 'Ana',
        string $email = 'ana@gabook.test',
        string $role = 'Asistente',
        AccountStatus $status = AccountStatus::Activo,
        ?string $password = 'secreto123',
        ?int $card = null,
    ): UserData {
        return new UserData(
            name: $name,
            lastName: 'Pérez',
            identificationCard: $card ?? 1_000_000 + crc32(strtolower(trim($email))) % 90_000_000,
            email: $email,
            phone: null,
            role: $role,
            status: $status,
            programIds: [],
            password: $password,
        );
    }
}
