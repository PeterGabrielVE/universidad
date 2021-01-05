<?php

use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('courses')->truncate();
        
         $courses = [
            ['cod'=>'I1','name' => 'Actividades de Orientación','uc' => '2','required'=>''],
            ['cod'=>'I2','name' => 'Inglés I','uc' => '2','required'=>''],
        ];

        DB::table('courses')->insert($courses);
    }
}
