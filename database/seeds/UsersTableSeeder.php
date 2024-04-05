<?php

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'identification_card' => '11111',
            'password' => bcrypt('12345678'),
            'rol_id' => 1,
        ]);
    }
}
