<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\SetLocale;
use App\Modules\IdentityAccess\Domain\Permission as P;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// /login, /register, logout y recuperación de contraseña los define Auth::routes().
Route::get('/', [LoginController::class, 'showLoginForm'])->middleware(SetLocale::class);
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

Auth::routes();

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
| Se autoriza por permiso (App\Modules\IdentityAccess\Domain\Permission); qué
| rol tiene cada permiso lo define AccessPolicy. Los expedientes de estudiante
| pasan además por student.access: propiedad (estudiante) y alcance de programa
| (coordinador).
*/

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');

    // Usuarios. Las rutas de gestión van primero para que "user/create" no se
    // interprete como "user/{user}".
    Route::middleware(P::middleware(P::UsersManage))->group(function () {
        Route::resource('user', 'UserController')->only(['create', 'store', 'edit', 'update']);
        Route::get('users/create', 'UserController@create')->name('users.create');
        Route::delete('usuario/delete/{id}', 'UserController@delete')->name('user.delete');
    });
    Route::middleware(P::middleware(P::UsersView))->group(function () {
        Route::resource('user', 'UserController')->only(['index', 'show']);
        Route::get('usuario/administrativo', 'UserController@indexAdministrativo')->name('usuario/administrativo');
        Route::get('usuario/directivo', 'UserController@indexDirectivo')->name('usuario/directivo');
        Route::get('usuario/asistente', 'UserController@indexAsistente')->name('usuario/asistente');
    });

    Route::middleware(P::middleware(P::RolesManage))->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('roles/index', 'RoleController@index')->name('roles');
    });

    // Lapsos: el alcance por programa (Coordinador) lo aplica ManageLapses.
    Route::middleware(P::middleware(P::LapsesManage, P::LapsesManageProgram))->group(function () {
        Route::resource('lapso', 'LapsoController');
        Route::get('editLapso/{id}', 'LapsoController@edit')->name('editLapso');
        Route::post('updateLapso/{id}', 'LapsoController@update')->name('updateLapso');
    });

    // Programas y pensum: consultar (pensum.view, el Coordinador solo los suyos) y gestionar (programs.manage).
    Route::middleware(P::middleware(P::ProgramsManage, P::PensumView))->group(function () {
        Route::get('programas', 'ProgramController@index')->name('programas.index');
        Route::get('programas/{program}/pensum', 'ProgramController@pensum')->name('programas.pensum');
    });
    Route::middleware(P::middleware(P::ProgramsManage))->group(function () {
        Route::post('programas', 'ProgramController@store')->name('programas.store');
        Route::get('programas/{program}/edit', 'ProgramController@edit')->name('programas.edit');
        Route::put('programas/{program}', 'ProgramController@update')->name('programas.update');
        Route::delete('programas/{program}', 'ProgramController@destroy')->name('programas.destroy');
        Route::post('programas/{program}/pensum', 'ProgramController@attachCourse')->name('programas.pensum.attach');
        Route::delete('programas/{program}/pensum/{course}', 'ProgramController@detachCourse')->name('programas.pensum.detach');

        Route::resource('facultades', 'FacultyController')
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['facultades' => 'faculty']);
    });

    Route::middleware(P::middleware(P::ReportsView))->group(function () {
        Route::get('reports', 'EstudiantesController@reports')->name('reports');
        Route::get('download/sede/{id}', 'EstudiantesController@downloadSedePdf');
        Route::get('download/student/{id}', 'EstudiantesController@downloadStudentPdf');
    });

    // Estudiantes: listado (filtrado por programa en el controlador) y baja.
    Route::get('estudiantes', 'EstudiantesController@index')
        ->middleware(P::middleware(P::StudentsView))->name('estudiantes.index');
    Route::delete('estudiantes/{estudiante}', 'EstudiantesController@destroy')
        ->middleware([P::middleware(P::StudentsDelete), 'student.access:scope'])->name('estudiantes.destroy');

    // Inscripción.
    Route::middleware(P::middleware(P::StudentsEnroll))->group(function () {
        Route::post('buscar.estudiante', 'EstudiantesController@buscarEstudiante')->name('buscar.estudiante');
        Route::get('getEstudiante/{id}', 'EstudiantesController@getEstudiante')
            ->middleware('student.access:scope')->name('getEstudiante');
        Route::get('crearEstudiante/{cedula}', 'EstudiantesController@createEstudiante')->name('estudiante.crear');
    });

    // Calificaciones: ver (Director, Coordinador) o registrar (Docente).
    Route::get('calificacion/estudiante/{id}', 'EstudiantesController@calificacion')
        ->middleware([P::middleware(P::GradesView, P::GradesRecord), 'student.access:scope'])
        ->name('estudiantes.calificacion');
    Route::middleware([P::middleware(P::GradesRecord), 'student.access:scope'])->group(function () {
        Route::post('store/calificacion/{id}', 'EstudiantesController@storeCalificacion')->name('calificacion.store');
        Route::post('store/calificacion/{id}/{doc}', 'EstudiantesController@storeDocCalificacion')->name('calificacion.store.doc');
    });

    Route::get('download/document/{file}', 'EstudiantesController@downloadDocument')
        ->middleware(P::middleware(P::DocumentsDownload));

    // Expediente y entregas: el estudiante accede al suyo; el personal según permiso y programa.
    Route::middleware('student.access')->group(function () {
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
