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
            ['cod'=>'I3','name' => 'Metodología de la Inv. I','uc' => '2','required'=>''],
            ['cod'=>'I4','name' => 'Matemática I','uc' => '4','required'=>''],
            ['cod'=>'I5','name' => 'Lenguaje y comunicación','uc' => '3','required'=>''],
            ['cod'=>'I6','name' => 'Educación Fisica y Deporte','uc' => '2','required'=>''],
            ['cod'=>'I7','name' => 'Estructuras Discretas I','uc' => '3','required'=>''],
            ['cod'=>'I8','name' => 'Introducción a la Ingeniería','uc' => '2','required'=>''],
            ['cod'=>'I9','name' => 'Inglés II','uc' => '2','required'=>'I2'],
            ['cod'=>'I10','name' => 'Metodología de la Inv. II','uc' => '2','required'=>'I3'],
            ['cod'=>'I11','name' => 'Matemática II','uc' => '4','required'=>'I4'],
            ['cod'=>'I12','name' => 'Física I','uc' => '3','required'=>'I4'],
            ['cod'=>'I13','name' => 'Introducción a la computación','uc' => '2','required'=>''],
            ['cod'=>'I14','name' => 'Dibujo','uc' => '2','required'=>''],
            ['cod'=>'I15','name' => 'Ingeniería y Sociedad','uc' => '2','required'=>''],
            ['cod'=>'I16','name' => 'EstadÍstica I','uc' => '2','required'=>'I4'],
            ['cod'=>'I17','name' => 'Matemática III','uc' => '4','required'=>'I11'],
            ['cod'=>'I18','name' => 'Álgebra Lineal','uc' => '4','required'=>'I11'],
            ['cod'=>'I19','name' => 
            ['cod'=>'I20','name' => 'Computación para Ingenieros','uc' => '4','required'=>'I13'],
            ['cod'=>'I21','name' => 'Matemática IV','uc' => '4','required'=>'I17'],
            ['cod'=>'I22','name' => 'Química','uc' => '2','required'=>''],
            ['cod'=>'I23','name' => 'Circuitos Eléctricos I','uc' => '3','required'=>'I19'],
            ['cod'=>'I24','name' => 'Programación I','uc' => '4','required'=>'I20'],
            ['cod'=>'I25','name' => 'Estructuras Discretas II','uc' => '3','required'=>'I7'],
            ['cod'=>'I26','name' => 'Laboratorio de Programación','uc' => '2','required'=>'I20'],
            ['cod'=>'I27','name' => 'Análisis Numérico','uc' => '3','required'=>'I18'],
            ['cod'=>'I28','name' => 'Análisis de Señales','uc' => '3','required'=>'I21','I23'],
            ['cod'=>'I29','name' => 'Circuitos Eléctricos II','uc' => '4','required'=>'I23'],
            ['cod'=>'I30','name' => 'Programación II','uc' => '4','required'=>''],
            ['cod'=>'I31','name' => 'Estructuras de Datos I','uc' => '3','required'=>''],
            ['cod'=>'I32','name' => 'Teoría de Sistemas I','uc' => '2','required'=>'I26'],
            ['cod'=>'I33','name' => 'Desarrollo de Emprendedores','uc' => '2','required'=>''],
            ['cod'=>'I34','name' => 'Teoría de Control I','uc' => '3','required'=>'I28'],
            ['cod'=>'I35','name' => 'Electrónica I','uc' => '4','required'=>'I23'],
            ['cod'=>'I36','name' => 'Lógica de computación','uc' => '4','required'=>'I31'],
            ['cod'=>'I37','name' => 'Lenguaje de Programación','uc' => '3','required'=>'I30','I31'],
            ['cod'=>'I38','name' => 'Estructuras de Datos II','uc' => '3','required'=>'I31'],
            ['cod'=>'I39','name' => 'Teoría de Sistemas II','uc' => '2','required'=>'I32'],
            ['cod'=>'I40','name' => 'Teoría de Control II','uc' => '3','required'=>'I34'],
            ['cod'=>'I41','name' => 'Electrónica II','uc' => '4','required'=>'I35'],
            ['cod'=>'I42','name' => 'Circuitos Digitales','uc' => '4','required'=>'I35'],
            ['cod'=>'I43','name' => 'Autómatas Lenguajes Formales','uc' => '3','required'=>'I36'],
            ['cod'=>'I44','name' => 'Sistemas Operativos','uc' => '3','required'=>'I38'],
            ['cod'=>'I45','name' => 'Diseño de Software','uc' => '3','required'=>'I39'],
            ['cod'=>'I46','name' => 'Electiva','uc' => '2','required'=>''],
            ['cod'=>'I47','name' => 'Higiene y Seguridad Industrial','uc' => '1','required'=>''],
            ['cod'=>'I48','name' => 'Laboratorio de Instrument. y Control','uc' => '1','required'=>'I34'],
            ['cod'=>'I49','name' => 'Gestión Ambiental','uc' => '2','required'=>''],
            ['cod'=>'I50','name' => 'Fund. para el Diseño de Microprocesad.','uc' => '3','required'=>'I41','I42'],
            ['cod'=>'I51','name' => 'Inteligencia Artificial','uc' => '4','required'=>'I43','I45'],
            ['cod'=>'I52','name' => 'Teleproceso','uc' => '4','required'=>'I44'],
            ['cod'=>'I53','name' => 'Análisis de Algoritmo','uc' => '4','required'=>'I38'],
            ['cod'=>'I54','name' => 'Electiva','uc' => '2','required'=>''],
            ['cod'=>'I55','name' => 'Ejercicio Legal de la Ingeniería','uc' => '1','required'=>''],
            ['cod'=>'I56','name' => 'Técnicas de Mantenimiento y Control','uc' => '3','required'=>'I50'],
            ['cod'=>'I57','name' => 'Diseño de Microprocesad.','uc' => '3','required'=>'I50'],
            ['cod'=>'I58','name' => 'Robótica','uc' => '3','required'=>'I50'],
            ['cod'=>'I59','name' => 'Gestión Empresarial','uc' => '1','required'=>''],
            ['cod'=>'I60','name' => 'Seminario de Trabajo de Grado','uc' => '3','required'=>''],
            ['cod'=>'I61','name' => 'Proyecto','uc' => '2','required'=>''],
            ['cod'=>'I62','name' => 'Pasantias','uc' => '4','required'=>''],
            ['cod'=>'I63','name' => 'Trabajo de Grado','uc' => '6','required'=>''],
        ];

        DB::table('courses')->insert($courses);
    }
}
