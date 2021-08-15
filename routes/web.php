<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {

	// users, roles, presmissions
	Route::resource('user', 'UserController');
	Route::resource('rol', 'RolController');
	Route::resource('permission', 'PermissionController');
	Route::resource('estudiantes', 'EstudiantesController');
	Route::resource('lapso', 'LapsoController');

});

Route::get('/inscripcion', 'InscripcionController@index')->name('inscripcion');
Route::post('buscar.estudiante', 'EstudiantesController@buscarEstudiante')->name('buscar.estudiante');
Route::get('getLapso/{id}', 'InscripcionController@getLapso')->name('getLapso');
Route::post('updateLapso', 'InscripcionController@updateLapso')->name('updateLapso');
Route::get('usuario/administrativo', 'UserController@indexAdministrativo')->name('usuario/administrativo');
Route::get('usuario/directivo', 'UserController@indexDirectivo')->name('usuario/directivo');
Route::get('usuario/operativo', 'UserController@indexOperativo')->name('usuario/operativo');
Route::get('pensum', 'LapsoController@getPensum')->name('pensum');
Route::get('editLapso/{id}', 'LapsoController@edit')->name('editLapso');
Route::post('updateLapso/{id}', 'LapsoController@update')->name('updateLapso');
Route::get('createInscripcion/{id}', 'InscripcionController@create')->name('createInscripcion');
Route::post('createLapsoEstudiante/{id}', 'InscripcionController@store')->name('createLapsoEstudiante');
Route::post('createLapsoEstudianteEquivalencia/{id}', 'InscripcionController@storeEquivalency')->name('createLapsoEstudianteEquivalencia');
Route::get('getEstudiante/{id}', 'EstudiantesController@getEstudiante')->name('getEstudiante');
Route::get('buscarEstudiantePorCedula', 'EstudiantesController@buscarEstudiantePorCedula')->name('buscarEstudiantePorCedula');
Route::get('crearEstudiante/{cedula}', 'EstudiantesController@createEstudiante')->name('estudiante.crear');
Route::get('documentEstudiante/{id}', 'EstudiantesController@document')->name('estudiante.document');
Route::post('documentStore/{id}', 'EstudiantesController@documentStore')->name('estudiante.documentStore');
Route::get('document2Estudiante/{id}', 'EstudiantesController@document2')->name('estudiante.document.post2');
Route::post('documentStore2/{id}', 'EstudiantesController@documentStorePost2')->name('estudiante.documentStorePost2');
Route::get('presentations/{id}', 'EstudiantesController@presentation')->name('estudiante.presentation');
Route::post('presentationStore/{id}', 'EstudiantesController@presentationStore')->name('estudiante.presentationStore');
Route::get('reports', 'EstudiantesController@reports')->name('reports');
