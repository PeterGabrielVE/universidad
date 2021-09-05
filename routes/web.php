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

Route::post('buscar.estudiante', 'EstudiantesController@buscarEstudiante')->name('buscar.estudiante');
Route::get('getLapso/{id}', 'InscripcionController@getLapso')->name('getLapso');
Route::post('updateLapso', 'InscripcionController@updateLapso')->name('updateLapso');

Route::get('usuario/administrativo', 'UserController@indexAdministrativo')->name('usuario/administrativo');
Route::get('usuario/directivo', 'UserController@indexDirectivo')->name('usuario/directivo');
Route::get('usuario/asistente', 'UserController@indexAsistente')->name('usuario/asistente');
Route::get('usuario/delete/{id}', 'UserController@delete')->name('user.delete');
Route::get('editLapso/{id}', 'LapsoController@edit')->name('editLapso');
Route::post('updateLapso/{id}', 'LapsoController@update')->name('updateLapso');


Route::get('getEstudiante/{id}', 'EstudiantesController@getEstudiante')->name('getEstudiante');
Route::get('crearEstudiante/{cedula}', 'EstudiantesController@createEstudiante')->name('estudiante.crear');
Route::get('documentEstudiante/{id}', 'EstudiantesController@document')->name('estudiante.document');

Route::post('documentStore/{id}', 'EstudiantesController@documentStore')->name('estudiante.documentStore');
Route::post('documentPost1Store/{id}', 'EstudiantesController@documentPost1Store')->name('estudiante.document.post1.store');
Route::get('document2Estudiante/{id}', 'EstudiantesController@document2')->name('estudiante.document.post2');
Route::get('documentpost2Estudiante/{id}', 'EstudiantesController@documentoPost2')->name('estudiante.documento.post2');
Route::post('documentStore2/{id}', 'EstudiantesController@documentStorePost2')->name('estudiante.documentStorePost2');
Route::post('documentoStore2/{id}', 'EstudiantesController@documentoStorePost2')->name('estudiante.documentoStorePost2');
Route::get('presentations/{id}', 'EstudiantesController@presentation')->name('estudiante.presentation');
Route::get('presentations/gerencia/{id}', 'EstudiantesController@presentation_gerencia')->name('estudiante.presentation.gerencia');
Route::post('presentationStore/{id}', 'EstudiantesController@presentationStore')->name('estudiante.presentationStore');
Route::post('presentationGerenciaStore/{id}', 'EstudiantesController@presentationGerenciaStore')->name('estudiante.presentationGerenciaStore');
Route::post('presentationGerenciaUpdate/{id}', 'EstudiantesController@presentationGerenciaUpdate')->name('estudiante.presentationGerenciaUpdate');
Route::get('reports', 'EstudiantesController@reports')->name('reports');

Route::get('documento/estudiante/{id}', 'EstudiantesController@setdocument')->name('estudiantes.document');
Route::get('documentos/estudiante/{id}/{doc}', 'EstudiantesController@document_doctorado')->name('estudiante.documentos');
Route::get('calificacion/estudiante/{id}', 'EstudiantesController@calificacion')->name('estudiantes.calificacion');
Route::post('store/calificacion/{id}', 'EstudiantesController@storeCalificacion')->name('calificacion.store');
Route::get('download/document/{file}','EstudiantesController@downloadDocument');
Route::get('download/sede/{id}','EstudiantesController@downloadSedePdf');
Route::get('download/student/{id}','EstudiantesController@downloadStudentPdf');
Route::get('todos/documento/estudiante/{id}', 'EstudiantesController@setAlldocument')->name('estudiantes.all_document');
Route::post('setAll/documento/estudiante/{id}', 'EstudiantesController@documents_store_ciencia')->name('estudiantes.set_all_document');
Route::get('estudiantes/documentos/ciencias/{id}', 'EstudiantesController@setdocumentCiencias')->name('estudiantes/documentos/ciencias');
Route::get('estudiantes/documentos/gerencia/{id}', 'EstudiantesController@setDocumentGerencia')->name('estudiantes/documentos/gerencia');
Route::get('edit/documentPost1/{id}/{doc}','EstudiantesController@editDocumentPost1');
Route::get('edit/documentPost2/{id}/{doc}','EstudiantesController@editDocumentPost2');
Route::get('edit/documentPres/{id}/{doc}','EstudiantesController@editDocumentPres');
Route::post('documentPost1Update/{id}/{doc}', 'EstudiantesController@documentPost1Update')->name('estudiante.documentPost1Update');
Route::post('documentPost2Update/{id}/{doc}', 'EstudiantesController@documentPost2Update')->name('estudiante.documentPost2Update');
Route::get('edit/documentos/{id}','EstudiantesController@editDocumentos');
Route::post('updateAll/documento/estudiante/{id}', 'EstudiantesController@documents_update_ciencia')->name('estudiantes.update_all_document');
