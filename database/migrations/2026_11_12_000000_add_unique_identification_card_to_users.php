<?php

use App\Console\Commands\ListDuplicateIdentificationCards;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La cédula es única (FUNCTIONAL_SPECIFICATIONS.md §13).
 *
 * Si ya hay cuentas que comparten cédula, la migración se detiene sin tocar
 * nada: hay que revisarlas con `php artisan users:duplicate-identification-cards`
 * y fusionarlas o eliminarlas antes de volver a ejecutarla.
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicates = ListDuplicateIdentificationCards::find();

        if ($duplicates !== []) {
            throw new RuntimeException(
                'No se puede hacer única la cédula: ' . count($duplicates) . ' cédula(s) repetida(s) ('
                . implode(', ', array_keys($duplicates)) . '). '
                . 'Revísalas con "php artisan users:duplicate-identification-cards" y vuelve a migrar.'
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('identification_card', 'users_identification_card_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_identification_card_unique');
        });
    }
};
