<?php

use Illuminate\Database\Seeder;

class SemesterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('semester')->truncate();
        
         $semester = [
            ['code' => 'I'],
            ['code' => 'II'],
            ['code' => 'III'],
            ['code' => 'IV'],
            ['code' => 'V'],
            ['code' => 'VI'],
            ['code' => 'VII'],
            ['code' => 'VIII'],
            ['code' => 'IX'],
            ['code' => 'X'],
        ];

        DB::table('semester')->insert($semester);
    }
}
