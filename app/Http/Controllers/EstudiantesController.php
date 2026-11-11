<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use \PDF;
Use Alert;
use App\Asignatura;
use App\Estudiante;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Pais;
use App\Post1;
use App\Post2;
use App\Presentation;
use App\Doctorado;
use App\Sede;
use App\Models\User;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use App\Http\Requests\StudentRecordRequest;
use App\Modules\Academic\Application\UseCase\ManageStudentRecords;
use App\Modules\IdentityAccess\Application\Port\StudentRecords;

class EstudiantesController extends Controller
{
    private const DOCUMENTS_DISK = 'local';
    private const DOCUMENTS_DIR = 'documents';

    /**
     * Entregas académicas (FUNCTIONAL_SPECIFICATIONS.md §9): PDF, DOC o DOCX hasta
     * 25 MB, para que los jurados puedan revisarlas. Pósters y certificados del
     * formulario de presentación siguen esta misma regla.
     */
    private const DOCUMENT_RULES = ['file', 'mimes:pdf,doc,docx', 'max:25600'];

    public function index(UserDirectory $directory)
    {
        $title = "Estudiante";
        $programIds = $directory->programScopeOf(Auth::user())->programIds();

        // El Coordinador solo ve los estudiantes de sus programas (ProgramScope).
        $estudiantes = Estudiante::query()
            ->when($programIds !== null, fn ($q) => $q->whereHas('user.programs', fn ($p) => $p->whereIn('doctorados.id', $programIds)))
            ->latest()
            ->paginate(5);
        $programs = Doctorado::orderBy('id')->pluck('name', 'id')->all();

        return view('pages.estudiantes.index',compact('estudiantes','programs','title'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    /**
     * Ficha del expediente (#14). El estudiante ve y edita sus datos de contacto;
     * el personal con students.manage, también los administrativos.
     */
    public function show(Request $request, int $estudiante)
    {
        $student = Estudiante::with(['user.programs', 'user.roles'])->findOrFail($estudiante);
        $user = $request->user();
        $isOwner = (int) $student->user_id === (int) $user->id;
        $canManage = ! $isOwner && $user->can(\App\Modules\IdentityAccess\Domain\Permission::StudentsManage->value);
        $programIds = $student->user?->programs->pluck('id')->all() ?? [];

        return view('pages.estudiantes.show', [
            'student' => $student,
            'isOwner' => $isOwner,
            'canEditContact' => $isOwner || $canManage,
            'canManage' => $canManage,
            'canEditIdentity' => $user->can(\App\Modules\IdentityAccess\Domain\Permission::UsersManage->value),
            'countries' => Pais::orderBy('name')->pluck('name', 'id')->all(),
            'campuses' => Sede::orderBy('name')->pluck('name', 'id')->all(),
            'lapses' => Lapso::query()
                ->where(fn ($q) => $q->whereIn('program_id', $programIds)->orWhereNull('program_id'))
                ->latest('start_lapse')->pluck('name', 'id')->all(),
            'admissionLapse' => $student->lapse_id ? Lapso::find($student->lapse_id) : null,
            'campus' => $student->user?->sede_id ? Sede::find($student->user->sede_id) : null,
            'country' => $student->user?->country_id ? Pais::find($student->user->country_id) : null,
        ]);
    }

    /** La edición se hace en la misma ficha. */
    public function edit(int $estudiante)
    {
        return redirect()->route('estudiantes.show', $estudiante);
    }

    public function update(StudentRecordRequest $request, int $estudiante, ManageStudentRecords $records, UserDirectory $users)
    {
        $user = $request->user();
        $student = Estudiante::findOrFail($estudiante);

        if ((int) $student->user_id === (int) $user->id) {
            // El estudiante solo cambia su contacto: cualquier otro campo enviado se ignora.
            $records->updateOwnContact($estudiante, $user->id, $request->phone(), $request->countryId());
        } else {
            $records->updateByStaff(
                $estudiante,
                $request->phone(),
                $request->countryId(),
                $request->campusId(),
                $request->boolean('equivalency'),
                $request->admissionLapseId(),
                $users->programScopeOf($user),
            );
        }

        Alert::success('Expediente', 'Datos actualizados.');

        return redirect()->route('estudiantes.show', $estudiante);
    }

    /**
     * Antes era el formulario de "completar expediente" (#15). Toda cuenta con rol
     * Estudiante ya tiene expediente (CreateUser/UpdateUser y HomeController lo
     * garantizan), así que se lleva a su ficha.
     */
    public function create(Request $request, StudentRecords $studentRecords)
    {
        $studentRecords->ensureFor($request->user()->id);
        $student = Estudiante::where('user_id', $request->user()->id)->firstOrFail();

        return redirect()->route('estudiantes.show', $student->id);
    }
    public function document($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.document',compact('estudiante'));

    }

    public function document2($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.document_post_2',compact('estudiante'));

    }

     public function destroy($id)
    {
        try {
            $estudiante = Estudiante::find($id);
            $user = User::find($estudiante->user_id);
            $user->delete();
            $estudiante->delete();
            Alert::success('Usuario', 'Eliminado exitosamente!');
            return back();
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante a eliminación!');
            return redirect()->back();
        }
    }


    public function documentStore(Request $request, $id)
    {
        $this->validateDocuments($request, ['post1_extenso', 'post1_carta']);
        try{
        $file1 = $request->post1_extenso;
        $file2 = $request->post1_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $post1 = new Post1();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiante.document.post2',$id);
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function documentStorePost2(Request $request, $id)
    {
        $this->validateDocuments($request, ['post2_extenso', 'post2_carta']);
        try{
        $file1 = $request->post2_extenso;
        $file2 = $request->post2_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $post1 = new Post2();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiante.presentation',$id);
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }

    public function presentation($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.presentation',compact('estudiante'));
    }

    public function presentation_gerencia($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.presentation_gerencia',compact('estudiante'));
    }

    public function presentationStore(Request $request, $id)
    {
        $this->validateDocuments($request, ['extenso', 'carta_aceptacion', 'poster', 'certificado']);
        try{

        $file1 = $request->extenso;
        $file2 = $request->carta_aceptacion;
        $file3 = $request->poster;
        $file4 = $request->certificado;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);

            $fileName3 = $this->storeDocument($file3);

            $fileName4 = $this->storeDocument($file4);


            $pre = new Presentation();
            $pre->student_id = $id;
            $pre->extenso = $fileName;
            $pre->carta_aceptacion = $fileName2;
            $pre->poster = $fileName3;
            $pre->certificado = $fileName4;
            $pre->created_at = $date;
            $pre->updated_at = $date;
            $pre->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiantes.show',$id);
        }else{
            return back();
        }
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }

    public function reports()
    {
        $estudiantes = User::leftjoin('students','users.id','students.user_id')
                            ->select('students.id','users.identification_card')
                            ->whereIn('users.id', DB::table('program_user')->where('program_id', 1)->select('user_id'))
                            ->get();
        $sedes = Sede::all();
        return view('pages.estudiantes.report',compact('estudiantes','sedes'));
    }

    public function documents_store($file1,$file2, $id)
    {
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $post1 = new Post1();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();
        }else{
            return back();
        }

    }

    public function documents_store_ciencia(Request $req, $id)
    {
        $this->validateDocuments($req, ['c_post1_extenso', 'c_post1_carta', 'c_post2_extenso', 'c_post2_carta', 'c_extenso', 'c_carta_aceptacion', 'c_poster', 'c_certificado']);
        try{
        $date = Carbon::now();
        $file1 = $req->c_post1_extenso;
        $file2 = $req->c_post1_carta;
        $file3 = $req->c_post2_extenso;
        $file4 = $req->c_post2_carta;

        $file5 = $req->c_extenso;
        $file6 = $req->c_carta_aceptacion;
        $file7 = $req->c_poster;
        $file8 = $req->c_certificado;

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null
        && $file5 !=null && $file6 !=null && $file7 !=null && $file8 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);

            $post1 = new Post1();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->doctorado_id = 2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();

            $fileName3 = $this->storeDocument($file3);

            $fileName4 = $this->storeDocument($file4);


            $post1 = new Post2();
            $post1->student_id = $id;
            $post1->extenso = $fileName3;
            $post1->carta_aceptacion = $fileName4;
            $post1->doctorado_id = 2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();

            $fileName5 = $this->storeDocument($file5);

            $fileName6 = $this->storeDocument($file6);

            $fileName7 = $this->storeDocument($file7);

            $fileName8 = $this->storeDocument($file8);

            $pre = new Presentation();
            $pre->student_id = $id;
            $pre->extenso = $fileName5;
            $pre->carta_aceptacion = $fileName6;
            $pre->poster = $fileName7;
            $pre->certificado = $fileName8;
            $pre->doctorado_id = 2;
            $pre->created_at = $date;
            $pre->updated_at = $date;
            $pre->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiantes.show',$id);
        }else{
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }

    public function setdocument($id)
    {
        $estudiante = Estudiante::find($id);
        $post1 = Post1::where('student_id',$id)->first();
        $post2 = Post2::where('student_id',$id)->first();
        $pre = Presentation::where('student_id',$id)->first();
        return view('pages.estudiantes.set_document',compact('estudiante','post1','post2','pre'));

    }

    public function calificacion($id)
    {
        $estudiante = Estudiante::find($id);

        $calificacion = ['0'=>'Seleccione calificación','1'=>'Aprobado','2'=>'No Aprobado'];

        // Doble titulación (antes doctorado_id = 3): inscrito en Gerencia (1) y Ciencias (2).
        if($estudiante->user->belongsToProgram(1) && $estudiante->user->belongsToProgram(2)){
            $post1_gerencia = Post1::where('student_id',$id)->where('doctorado_id',1)->first();
            $post2_gerencia = Post2::where('student_id',$id)->where('doctorado_id',1)->first();
            $pre_gerencia = Presentation::where('student_id',$id)->where('doctorado_id',1)->first();

            $post1_ciencia = Post1::where('student_id',$id)->where('doctorado_id',2)->first();
            $post2_ciencia = Post2::where('student_id',$id)->where('doctorado_id',2)->first();
            $pre_ciencia = Presentation::where('student_id',$id)->where('doctorado_id',2)->first();

            return view('pages.estudiantes.documents.calificacion_total',compact('estudiante','post1_gerencia','post2_gerencia','pre_gerencia',
            'post1_ciencia','post2_ciencia','pre_ciencia','calificacion'));

        }else{
            $post1 = Post1::where('student_id',$id)->first();
            $post2 = Post2::where('student_id',$id)->first();
            $pre = Presentation::where('student_id',$id)->first();
            return view('pages.estudiantes.calificacion',compact('estudiante','post1','post2','pre','calificacion'));
        }


    }

    public function storeCalificacion(Request $req,$id)
    {
        try{
        $post1 = Post1::where('student_id',$id)->first();
        $post1->extenso_note = $req->post1_extenso_note;
        $post1->carta_aceptacion_note = $req->post1_carta_aceptacion_note;
        $post1->save();

        $post2 = Post2::where('student_id',$id)->first();
        $post2->extenso_note = $req->post2_extenso_note;
        $post2->carta_aceptacion_note = $req->post2_carta_aceptacion_note;
        $post2->save();

        $pre = Presentation::where('student_id',$id)->first();
        $pre->extenso_note = $req->pre_extenso_note;
        $pre->carta_aceptacion_note = $req->pre_carta_aceptacion_note;
        $pre->poster_note = $req->pre_poster_note;
        $pre->certificado_note = $req->pre_certificado_note;
        $pre->save();
        Alert::success('Estudiante', 'Guardado exitosamente!');
        return back();
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function storeDocCalificacion(Request $req,$id,$doc)
    {
        try{
        $post1 = Post1::where('student_id',$id)->where('doctorado_id',$doc)->first();
        $post1->extenso_note = $req->post1_extenso_note;
        $post1->carta_aceptacion_note = $req->post1_carta_aceptacion_note;
        $post1->save();

        $post2 = Post2::where('student_id',$id)->where('doctorado_id',$doc)->first();
        $post2->extenso_note = $req->post2_extenso_note;
        $post2->carta_aceptacion_note = $req->post2_carta_aceptacion_note;
        $post2->save();

        $pre = Presentation::where('student_id',$id)->where('doctorado_id',$doc)->first();
        $pre->extenso_note = $req->pre_extenso_note;
        $pre->carta_aceptacion_note = $req->pre_carta_aceptacion_note;
        $pre->poster_note = $req->pre_poster_note;
        $pre->certificado_note = $req->pre_certificado_note;
        $pre->save();
        Alert::success('Estudiante', 'Guardado exitosamente!');
        return back();
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function downloadDocument(string $file)
    {
        $path = self::DOCUMENTS_DIR . '/' . $file;

        if ($file !== basename($file) || ! Storage::disk(self::DOCUMENTS_DISK)->exists($path)) {
            abort(404);
        }

        return Storage::disk(self::DOCUMENTS_DISK)->download($path);
    }

    /**
     * Valida los archivos del formulario antes de procesarlos, para que el usuario
     * vea el error de validación en vez del mensaje genérico del catch.
     */
    private function validateDocuments(Request $request, array $fields): void
    {
        $request->validate(array_fill_keys($fields, array_merge(['nullable'], self::DOCUMENT_RULES)));
    }

    /**
     * Guarda un documento en el disco privado (storage/app/documents), fuera de
     * public/: Apache nunca lo sirve ni lo ejecuta. La extensión se deduce del
     * contenido, no del nombre enviado por el cliente.
     */
    private function storeDocument(UploadedFile $file): string
    {
        Validator::make(['document' => $file], ['document' => self::DOCUMENT_RULES])->validate();

        $name = Str::uuid()->toString() . '.' . $file->extension();
        $file->storeAs(self::DOCUMENTS_DIR, $name, self::DOCUMENTS_DISK);

        return $name;
    }

    public function downloadSedePdf($id)
    {
            $users = Post1::leftjoin('students','posts_1.student_id','students.id')
            ->leftjoin('users', 'students.user_id', '=', 'users.id')
            ->leftjoin('sedes','users.sede_id','sedes.id')
            ->select('posts_1.id AS id','posts_1.created_at AS created_at')
            ->where('sedes.id',$id)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });
        $usermcount = [];
        $userArr = [];

        foreach ($users as $key => $value) {
            $usermcount[(int)$key] = count($value) *2;
        }

        $month = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount[$i])) {
                $userArr[$i]['count'] = $usermcount[$i];
            } else {
                $userArr[$i]['count'] = 0;
            }
            $userArr[$i]['month'] = $month[$i - 1];
        }

        $posts2 = Post2::leftjoin('students','posts_2.student_id','students.id')
            ->leftjoin('users', 'students.user_id', '=', 'users.id')
            ->leftjoin('sedes','users.sede_id','sedes.id')
            ->select('posts_2.id AS id','posts_2.created_at AS created_at')
            ->where('sedes.id',$id)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });
        //dd($users);
        $usermcount2 = [];
        $userArr2 = [];

        foreach ($posts2 as $key => $value) {
            $usermcount2[(int)$key] = count($value) *2;
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount2[$i])) {
                $userArr2[$i]['count'] = $usermcount2[$i];
            } else {
                $userArr2[$i]['count'] = 0;
            }
            $userArr2[$i]['month'] = $month[$i - 1];
        }

        $pres = Presentation::leftjoin('students','presentations.student_id','students.id')
        ->leftjoin('users', 'students.user_id', '=', 'users.id')
        ->leftjoin('sedes','users.sede_id','sedes.id')
        ->select('presentations.id AS id','presentations.created_at AS created_at')
        ->where('sedes.id',$id)
        ->get()
        ->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m');
        });

        $usermcount3 = [];
        $userArr3 = [];

        foreach ($pres as $key => $value) {
            $usermcount3[(int)$key] = count($value) *4;
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount3[$i])) {
                $userArr3[$i]['count'] = $usermcount3[$i];
            } else {
                $userArr3[$i]['count'] = 0;
            }
            $userArr3[$i]['month'] = $month[$i - 1];
        }

        $sede = DB::table('sedes')->where('sedes.id',$id)->first();
        $pdf = PDF::loadView('pages.reports.sede', compact('userArr','userArr2','userArr3','sede'));

        return $pdf->stream('report-sede.pdf');
    }

    public function downloadStudentPdf($id)
    {
            $p1 = 0;
            $p2 = 0;
            $p3 = 0;

            $post1 = Post1::where('student_id',$id)
            ->get();
            if(isset($post1)){
                $p1 = 2;
            }
            $post2 = Post1::where('student_id',$id)
            ->get();

            if(isset($post2)){
                $p2 = 2;
            }
            $pre = Presentation::where('student_id',$id)
            ->get();

            if(isset($pre)){
                $p3 = 4;
            }

            $total = $p1 + $p2 + $p3;
        $student = Estudiante::find($id);
        $user = User::find($student->user_id);
        $pdf = PDF::loadView('pages.reports.student', compact('total','user'));

        return $pdf->stream('report-estudiante.pdf');
    }

    public function setAllDocument($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.all_documents',compact('estudiante'));

    }


    public function editDocumentos($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.documents.all_edit_documents',compact('estudiante'));

    }

    public function setDocumentGerencia($id)
    {
        $estudiante = Estudiante::find($id);
        $post1 = Post1::where('student_id',$id)->first();
        $post2 = Post2::where('student_id',$id)->first();
        $pre = Presentation::where('student_id',$id)->first();
        $doc = 1;
        return view('pages.estudiantes.set_document_gerencia',compact('estudiante','post1','post2','pre','doc'));

    }


    public function setDocumentCiencias($id)
    {
        $estudiante = Estudiante::find($id);
        $post1 = Post1::where('student_id',$id)->where('doctorado_id',2)->first();
        $post2 = Post2::where('student_id',$id)->where('doctorado_id',2)->first();
        $pre = Presentation::where('student_id',$id)->where('doctorado_id',2)->first();
        return view('pages.estudiantes.documents.show_documents_ciencias',compact('estudiante','post1','post2','pre'));

    }

    public function document_doctorado($id,$doc)
    {
        $estudiante = Estudiante::find($id);
        if($doc == 1){
            return view('pages.estudiantes.document2',compact('estudiante','doc'));
        }else{
            return view('pages.estudiantes.document2',compact('estudiante','doc'));
        }

    }

    public function documentPost1Store(Request $request, $id)
    {
        $this->validateDocuments($request, ['post1_extenso', 'post1_carta']);
        try{
        $file1 = $request->post1_extenso;
        $file2 = $request->post1_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $post1 = new Post1();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->doctorado_id = 1;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiante.documento.post2',$id);
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function documentPost2($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.document_post_2',compact('estudiante'));

    }

    public function documentoPost2($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.documento_post_2',compact('estudiante'));

    }

    public function documentoStorePost2(Request $request, $id)
    {
        $this->validateDocuments($request, ['post2_extenso', 'post2_carta']);
        try{
        $file1 = $request->post2_extenso;
        $file2 = $request->post2_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $post1 = new Post2();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->doctorado_id = 1;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiante.presentation.gerencia',$id);
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }

    public function presentationGerenciaStore(Request $request, $id)
    {
        $this->validateDocuments($request, ['extenso', 'carta_aceptacion', 'poster', 'certificado']);
        try{

        $file1 = $request->extenso;
        $file2 = $request->carta_aceptacion;
        $file3 = $request->poster;
        $file4 = $request->certificado;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);

            $fileName3 = $this->storeDocument($file3);

            $fileName4 = $this->storeDocument($file4);


            $pre = new Presentation();
            $pre->student_id = $id;
            $pre->extenso = $fileName;
            $pre->carta_aceptacion = $fileName2;
            $pre->poster = $fileName3;
            $pre->certificado = $fileName4;
            $pre->doctorado_id = 1;
            $pre->created_at = $date;
            $pre->updated_at = $date;
            $pre->save();
            Alert::success('Estudiante', 'Guardado exitosamente!');
            return redirect()->route('estudiantes.show',$id);
        }else{
            return back();
        }
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }

    public function editDocumentPost1($id,$doc)
    {
        $estudiante = Estudiante::find($id);
        $post1 = Post1::where('student_id',$id)->where('doctorado_id',$doc)->first();
        return view('pages.estudiantes.documents.edit_document_post1',compact('estudiante','post1'));

    }

    public function documentPost1Update(Request $request, $id, $doc)
    {
       $this->validateDocuments($request, ['post1_extenso', 'post1_carta']);
       try{
        $file1 = $request->post1_extenso;
        $file2 = $request->post1_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $estudiante = Estudiante::find($id);
            $post1 = Post1::where('student_id',$id)
                    ->where('doctorado_id',$doc)
                    ->first();

            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Actualizado exitosamente!');
            return redirect()->route('estudiantes.index');
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function editDocumentPost2($id,$doc)
    {
        $estudiante = Estudiante::find($id);
        $post1 = Post1::where('student_id',$id)->where('doctorado_id',$doc)->first();
        return view('pages.estudiantes.documents.edit_document_post2',compact('estudiante','post1'));

    }

    public function documentPost2Update(Request $request, $id, $doc)
    {
       $this->validateDocuments($request, ['post2_extenso', 'post2_carta']);
       try{
        $file1 = $request->post2_extenso;
        $file2 = $request->post2_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $estudiante = Estudiante::find($id);
            $post1 = Post2::where('student_id',$id)
                    ->where('doctorado_id',$doc)
                    ->first();

            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Actualizado exitosamente!');
            return redirect()->route('estudiantes.index');
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function editDocumentPres($id,$doc)
    {
        $estudiante = Estudiante::find($id);
        $pres = Presentation::where('student_id',$id)->where('doctorado_id',$doc)->first();
        return view('pages.estudiantes.documents.edit_document_pres',compact('estudiante','pres'));

    }

    public function documentPresUpdate(Request $request, $id, $doc)
    {
       $this->validateDocuments($request, ['post2_extenso', 'post2_carta']);
       try{
        $file1 = $request->post2_extenso;
        $file2 = $request->post2_carta;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);


            $estudiante = Estudiante::find($id);
            $post1 = Post2::where('student_id',$id)
                    ->where('doctorado_id',$doc)
                    ->first();

            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->updated_at = $date;
            $post1->save();
            Alert::success('Estudiante', 'Actualizado exitosamente!');
            return redirect()->route('estudiantes.index');
        }else{
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }

    }

    public function presentationGerenciaUpdate(Request $request, $id, $doc)
    {
        $this->validateDocuments($request, ['extenso', 'carta_aceptacion', 'poster', 'certificado']);
        try{

        $file1 = $request->extenso;
        $file2 = $request->carta_aceptacion;
        $file3 = $request->poster;
        $file4 = $request->certificado;
        $date = Carbon::now();

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);

            $fileName3 = $this->storeDocument($file3);

            $fileName4 = $this->storeDocument($file4);

            $pre = Presentation::where('student_id',$id)
            ->where('doctorado_id',$doc)
            ->first();
            $pre->student_id = $id;
            $pre->extenso = $fileName;
            $pre->carta_aceptacion = $fileName2;
            $pre->poster = $fileName3;
            $pre->certificado = $fileName4;
            $pre->doctorado_id = 1;
            $pre->created_at = $date;
            $pre->updated_at = $date;
            $pre->save();
            Alert::success('Estudiante', 'Actualizado exitosamente!');
            return redirect()->route('estudiantes.index',$id);
        }else{
            return back();
        }
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }

    public function documents_update_ciencia(Request $req, $id)
    {
        $this->validateDocuments($req, ['c_post1_extenso', 'c_post1_carta', 'c_post2_extenso', 'c_post2_carta', 'c_extenso', 'c_carta_aceptacion', 'c_poster', 'c_certificado']);
        try{
        $date = Carbon::now();
        $file1 = $req->c_post1_extenso;
        $file2 = $req->c_post1_carta;
        $file3 = $req->c_post2_extenso;
        $file4 = $req->c_post2_carta;

        $file5 = $req->c_extenso;
        $file6 = $req->c_carta_aceptacion;
        $file7 = $req->c_poster;
        $file8 = $req->c_certificado;

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null
        && $file5 !=null && $file6 !=null && $file7 !=null && $file8 !=null){

            $fileName = $this->storeDocument($file1);

            $fileName2 = $this->storeDocument($file2);

            $post1 = Post1::where('student_id',$id)
            ->where('doctorado_id', 2)
            ->first();
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();

            $fileName3 = $this->storeDocument($file3);

            $fileName4 = $this->storeDocument($file4);


            $post2 = Post2::where('student_id',$id)
            ->where('doctorado_id', 2)
            ->first();
            $post1->extenso = $fileName3;
            $post1->carta_aceptacion = $fileName4;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();

            $fileName5 = $this->storeDocument($file5);

            $fileName6 = $this->storeDocument($file6);

            $fileName7 = $this->storeDocument($file7);

            $fileName8 = $this->storeDocument($file8);

            $pre = Presentation::where('student_id',$id)
            ->where('doctorado_id', 2)
            ->first();
            $pre->extenso = $fileName5;
            $pre->carta_aceptacion = $fileName6;
            $pre->poster = $fileName7;
            $pre->certificado = $fileName8;
            $pre->created_at = $date;
            $pre->updated_at = $date;
            $pre->save();
            Alert::success('Estudiante', 'Actualizado exitosamente!');
            return redirect()->route('estudiantes.show',$id);
        }else{
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return back();
        }

        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante el almacenamiento!');
            return redirect()->back();
        }
    }







    /**
     * El formulario del expediente envía un solo programa (doctorado_id). Se guarda en
     * program_user. Gestión completa del expediente: Fase 3c.
     */
    private function syncStudentProgram(User $user, mixed $programId): void
    {
        if ($programId !== null && $programId !== '') {
            $user->programs()->sync([(int) $programId]);
        }
    }
}
