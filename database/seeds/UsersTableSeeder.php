<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first() ?? new User();
        $admin->forceFill([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'identification_card' => '11111',
            'password' => bcrypt('12345678'),
            'rol_id' => 1,
        ])->save();

        $admin->syncRoles(['Director']);
    }
}
