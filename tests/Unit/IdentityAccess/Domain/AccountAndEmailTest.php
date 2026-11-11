<?php

namespace Tests\Unit\IdentityAccess\Domain;

use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\IdentityAccess\Domain\Exception\InvalidEmail;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;

class AccountAndEmailTest extends TestCase
{
    public function test_only_active_accounts_can_log_in(): void
    {
        $this->assertTrue(AccountStatus::Activo->canLogIn());
        $this->assertFalse(AccountStatus::Inactivo->canLogIn());
        $this->assertFalse(AccountStatus::Suspendido->canLogIn());
    }

    public function test_email_is_normalized(): void
    {
        $this->assertSame('ana.perez@gabook.test', Email::from('  Ana.Perez@Gabook.TEST ')->value);
    }

    public function test_emails_differing_only_in_case_are_equal(): void
    {
        $this->assertTrue(Email::from('A@b.test')->equals(Email::from('a@B.test')));
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->expectException(InvalidEmail::class);

        Email::from('no-es-un-correo');
    }

    public function test_system_roles_are_recognized_by_exact_name(): void
    {
        $this->assertTrue(RoleName::isSystemRole('Coordinador'));
        $this->assertFalse(RoleName::isSystemRole('coordinador'));
        $this->assertFalse(RoleName::isSystemRole('Jurado externo'));
    }
}
