<?php

use App\Authorization\Roles;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// /login, /register, logout y recuperación de contraseña los define Auth::routes().
// Antes se declaraban también aquí con el mismo nombre, lo que impedía route:cache
// (y Auth::routes() sobrescribía esas definiciones, así que no cambia el comportamiento).
Route::get('/', [LoginController::class, 'showLoginForm'])->middleware(SetLocale::class);
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

Auth::routes();

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
| Grupos de roles en App\Authorization\Roles. Los estudiantes solo acceden a
| su propio expediente (middleware student.owner).
*/

Route::middleware('auth')->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');

    // Director y Administrador: usuarios, roles, lapsos y reportes.
    Route::middleware(Roles::middleware(Roles::ADMIN))->group(function () {
        Route::resource('user', 'UserController');
        Route::get('users/create', 'UserController@create')->name('users.create');
        Route::get('usuario/administrativo', 'UserController@indexAdministrativo')->name('usuario/administrativo');
        Route::get('usuario/directivo', 'UserController@indexDirectivo')->name('usuario/directivo');
        Route::get('usuario/asistente', 'UserController@indexAsistente')->name('usuario/asistente');
        Route::delete('usuario/delete/{id}', 'UserController@delete')->name('user.delete');

        Route::resource('roles', RoleController::class);
        Route::post('roles/index', 'RoleController@index')->name('roles');

        Route::resource('lapso', 'LapsoController');
        Route::get('editLapso/{id}', 'LapsoController@edit')->name('editLapso');
        Route::post('updateLapso/{id}', 'LapsoController@update')->name('updateLapso');

        Route::get('reports', 'EstudiantesController@reports')->name('reports');
        Route::get('download/sede/{id}', 'EstudiantesController@downloadSedePdf');
        Route::get('download/student/{id}', 'EstudiantesController@downloadStudentPdf');
    });

    // Personal académico: listado de estudiantes, inscripción y calificaciones.
    Route::middleware(Roles::middleware(Roles::STAFF))->group(function () {
        Route::resource('estudiantes', 'EstudiantesController')->only(['index', 'destroy']);
        Route::post('buscar.estudiante', 'EstudiantesController@buscarEstudiante')->name('buscar.estudiante');
        Route::get('getEstudiante/{id}', 'EstudiantesController@getEstudiante')->name('getEstudiante');
        Route::get('crearEstudiante/{cedula}', 'EstudiantesController@createEstudiante')->name('estudiante.crear');

        Route::get('calificacion/estudiante/{id}', 'EstudiantesController@calificacion')->name('estudiantes.calificacion');
        Route::post('store/calificacion/{id}', 'EstudiantesController@storeCalificacion')->name('calificacion.store');
        Route::post('store/calificacion/{id}/{doc}', 'EstudiantesController@storeDocCalificacion')->name('calificacion.store.doc');

        Route::get('download/document/{file}', 'EstudiantesController@downloadDocument');
    });

    // Expediente y documentos: el personal accede a todos, cada estudiante al suyo.
    Route::middleware('student.owner')->group(function () {
        Route::resource('estudiantes', 'EstudiantesController')->only(['create', 'store', 'show', 'edit', 'update']);

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

        Route::get('documento/estudiante/{id}', 'EstudiantesController@setdocument')->name('estudiantes.document');
        Route::get('documentos/estudiante/{id}/{doc}', 'EstudiantesController@document_doctorado')->name('estudiante.documentos');
        Route::get('todos/documento/estudiante/{id}', 'EstudiantesController@setAlldocument')->name('estudiantes.all_document');
        Route::post('setAll/documento/estudiante/{id}', 'EstudiantesController@documents_store_ciencia')->name('estudiantes.set_all_document');
        Route::get('estudiantes/documentos/ciencias/{id}', 'EstudiantesController@setdocumentCiencias')->name('estudiantes/documentos/ciencias');
        Route::get('estudiantes/documentos/gerencia/{id}', 'EstudiantesController@setDocumentGerencia')->name('estudiantes/documentos/gerencia');
        Route::get('edit/documentPost1/{id}/{doc}', 'EstudiantesController@editDocumentPost1');
        Route::get('edit/documentPost2/{id}/{doc}', 'EstudiantesController@editDocumentPost2');
        Route::get('edit/documentPres/{id}/{doc}', 'EstudiantesController@editDocumentPres');
        Route::post('documentPost1Update/{id}/{doc}', 'EstudiantesController@documentPost1Update')->name('estudiante.documentPost1Update');
        Route::post('documentPost2Update/{id}/{doc}', 'EstudiantesController@documentPost2Update')->name('estudiante.documentPost2Update');
        Route::get('edit/documentos/{id}', 'EstudiantesController@editDocumentos');
        Route::post('updateAll/documento/estudiante/{id}', 'EstudiantesController@documents_update_ciencia')->name('estudiantes.update_all_document');
    });
});
