<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Lista las cuentas que comparten cédula. Se ejecuta antes de la migración que
 * hace única la cédula: resolver los duplicados (fusionar o eliminar cuentas de
 * prueba) es una decisión de Control de Estudios, no algo que la migración haga sola.
 */
class ListDuplicateIdentificationCards extends Command
{
    protected $signature = 'users:duplicate-identification-cards';

    protected $description = 'Lista las cuentas que comparten cédula de identidad';

    public function handle(): int
    {
        $duplicates = self::find();

        if ($duplicates === []) {
            $this->info('No hay cédulas duplicadas.');

            return self::SUCCESS;
        }

        $this->warn(count($duplicates) . ' cédula(s) repetida(s):');
        $this->table(
            ['Cédula', 'Id', 'Correo', 'Nombre', 'Creado'],
            DB::table('users')
                ->whereIn('identification_card', array_keys($duplicates))
                ->orderBy('identification_card')->orderBy('id')
                ->get(['identification_card', 'id', 'email', 'name', 'created_at'])
                ->map(fn ($u) => (array) $u)
                ->all()
        );

        return self::FAILURE;
    }

    /** @return array<int, int> cédula => cantidad de cuentas */
    public static function find(): array
    {
        return DB::table('users')
            ->select('identification_card', DB::raw('COUNT(*) AS total'))
            ->groupBy('identification_card')
            ->having('total', '>', 1)
            ->pluck('total', 'identification_card')
            ->all();
    }
}
