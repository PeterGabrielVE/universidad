<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use App\Rol;
use App\User;

class EstudiantesController extends Controller
{
    public function index()
    {
        $rol = 4;
        $title = "Estudiante";
    	$estudiantes = Estudiante::latest()->paginate(5);
        $roles = Rol::get()->pluck('name','id');
        return view('pages.estudiantes.index',compact('estudiantes','roles','rol','title'))
            ->with('i', (request()->input('page', 1) - 1) * 5);

    }

    public function show($id)
    {
        $estudiante = Estudiante::find($id);
        $lapso = Lapso::latest('id')->first();
        $paises = Pais::get()->pluck('name','id');
        $prefijo = Pais::get()->pluck('Prefijo','id');
        $user = User::find($estudiante->user_id);
        $doctorados = Doctorado::get()->pluck('name','id');
        return view('pages.estudiantes.show',compact('estudiante','lapso','paises','prefijo','user','doctorados'));

    }

      /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lapso = Lapso::get()->pluck('name','id')->prepend('Seleccione una opción','');
        $paises = Pais::get()->pluck('name','id')->prepend('Seleccione una opción','');
        $prefijo = Pais::get()->pluck('Prefijo','id')->prepend('Seleccione una opción','');
        $doctorados = Doctorado::get()->pluck('name','id')->prepend('Seleccione una opción','');
        $sedes = Sede::get()->pluck('name','id')->prepend('Seleccione una opción','');
        return view('pages.estudiantes.create',compact('lapso','paises','prefijo','sedes','doctorados'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
    	//dd($req->all());
        $user = User::find(Auth::id());
        $user->name = $req->first_name;
        $user->last_name = $req->last_name;
        $user->sede_id = $req->sede_id;
        $user->doctorado_id = $req->doctorado_id;
        $user->identification_card = $req->identification_card;
        $user->save();

        $student = new Estudiante();
        $student->user_id = Auth::id();
        $student->status = $req->status;
        $student->created_at = Carbon::now();
        $student->updated_at =Carbon::now();
        $student->save();

        $stud = Estudiante::latest('id')->first();

        if($req->doctorado_id == 1){
            $this->documents_store($req->d_post1_extenso,$req->d_post1_carta,$stud->id);
        }else{
            $file1 = $req->c_post1_extenso;
            $file2 = $req->c_post1_carta;
            $file3 = $req->c_post2_extenso;
            $file4 = $req->c_post2_carta;

            $file5 = $req->c_extenso;
            $file6 = $req->c_carta_aceptacion;
            $file7 = $req->c_poster;
            $file8 = $req->c_certificado;
            $this->documents_store_ciencia($file1,$file2,$file3,$file4,$file5,$file6,$file7,$file8,$stud->id);
        }


        return redirect()->route('estudiantes.show',$stud->id)
                        ->with('success','Estudiante guardado exitosamente.');
    }

    public function buscarEstudiante(Request $request){

        $query = $request->consulta;
        $estudiante = Estudiante::where('identification_card',$query)->first();
        $lapso = Lapso::latest('id')->first();
        $carga = Lapso_Estudiante::where('student_id',$estudiante->id)->where('lapse_id',$lapso->id)->get();
        $historia = Lapso_Estudiante::where('student_id',$estudiante->id)->get();

        return view('pages.inscripcion.index',compact('estudiante','carga','historia','lapso'));
    }

        public function getEstudiante($id){

        $estudiante = Estudiante::find($id);
        $lapso = Lapso::latest('id')->first();
        $carga = Lapso_Estudiante::where('student_id',$estudiante->id)->where('lapse_id',$lapso->id)->get();
        $historia = Lapso_Estudiante::where('student_id',$estudiante->id)->get();

        return view('pages.inscripcion.index',compact('estudiante','carga','historia','lapso'));
    }

    public function edit($id)
    {
        $estudiante = Estudiante::find($id);
        $lapso = Lapso::latest('id')->first();
        $paises = Pais::get()->pluck('name','id');
        $prefijo = Pais::get()->pluck('Prefijo','id');
        $user = User::find($estudiante->user_id);
        $doctorados = Doctorado::get()->pluck('name','id');
        return view('pages.estudiantes.edit',compact('estudiante','lapso','paises','prefijo','user','doctorados'));

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

    public function update(Request $req, $id)
    {
        try{
        $user = User::find(Auth::id());
        $user->first_name = $req->first_name;
        $user->last_name = $req->last_name;
        $user->sede_id = $req->sede_id;
        $user->doctorado_id = $req->doctorado_id;
        $user->country_id = $req->country_id;
        $user->cod_phone = $req->cod_phone;
        $user->phone = $req->phone;
        $user->identification_card = $req->identification_card;
        $user->save();

        $data = $req->all();
        $estudiante = Estudiante::find($id);
        $estudiante->update($data);
        $estudiante->save();

        $search_lapse = Lapso_Estudiante::where('student_id',$id)
                                        ->where('lapse_id',$req->lapse_id)
                                        ->where('doctorado_id',$req->doctorado_id)
                                        ->first();

        if($search_lapse == null){
            $lapso = new Lapso_Estudiante();
            $lapso->student_id = $id;
            $lapso->lapse_id = $req->lapse_id;
            $lapso->doctorado_id = $req->doctorado_id;
            $lapso->save();
        }
        Alert::success('Estudiante', '¡Actualizado exitosamente!');
        return back();

        } catch (\Exception $e){
            Alert::error('Estudiante', '¡Error durante la actualización!');
            return redirect()->back();
        }
    }

     public function destroy($id)
    {
        try {
            $estudiante = Estudiante::find($id);
            $estudiante->delete();
            Alert::success('Usuario', 'Eliminado exitosamente!');
            return back();
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante a eliminación!');
            return redirect()->back();
        }
    }


    public function createEstudiante($ced)
    {
        $lapso = Lapso::get()->pluck('name','id');
        $paises = Pais::get()->pluck('name','id');
        $prefijo = Pais::get()->pluck('Prefijo','id');
        $doctorados = Doctorado::get()->pluck('name','id');
        $sedes = Sede::get()->pluck('name','id');
        return view('pages.estudiantes.create',compact('lapso','paises','prefijo','ced','doctorados','sedes'));
    }

    public function documentStore(Request $request, $id)
    {
        try{
        $file1 = $request->post1_extenso;
        $file2 = $request->post1_carta;
        $date = Carbon::now();
        $email = Auth::user()->email;

        if($file1 !=null && $file2 !=null){

            $path = public_path().'/document/';
            $extension = $file1->getClientOriginalExtension();
            $fileName = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension;
            $file1->move($path, $fileName);

            $extension2 = $file2->getClientOriginalExtension();
            $fileName2 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension2;
            $file2->move($path, $fileName2);


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
        try{
        $file1 = $request->post2_extenso;
        $file2 = $request->post2_carta;
        $date = Carbon::now();
        $email = Auth::user()->email;

        if($file1 !=null && $file2 !=null){

            $path = public_path().'/document/';
            $extension = $file1->getClientOriginalExtension();
            $fileName = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension;
            $file1->move($path, $fileName);

            $extension2 = $file2->getClientOriginalExtension();
            $fileName2 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension2;
            $file2->move($path, $fileName2);


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

    public function presentationStore(Request $request, $id)
    {
        try{

        $file1 = $request->extenso;
        $file2 = $request->carta_aceptacion;
        $file3 = $request->poster;
        $file4 = $request->certificado;
        $date = Carbon::now();
        $email = Auth::user()->email;

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null){

            $path = public_path().'/document/';
            $extension = $file1->getClientOriginalExtension();
            $fileName = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension;
            $file1->move($path, $fileName);

            $extension2 = $file2->getClientOriginalExtension();
            $fileName2 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension2;
            $file2->move($path, $fileName2);

            $extension3 = $file3->getClientOriginalExtension();
            $fileName3 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension3;
            $file3->move($path, $fileName3);

            $extension4 = $file4->getClientOriginalExtension();
            $fileName4 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension4;
            $file4->move($path, $fileName4);


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
                            ->where('users.doctorado_id',1)
                            ->get();
        $sedes = Sede::all();
        return view('pages.estudiantes.report',compact('estudiantes','sedes'));
    }

    public function documents_store($file1,$file2, $id)
    {
        $date = Carbon::now();
        $email = Auth::user()->email;

        if($file1 !=null && $file2 !=null){

            $path = public_path().'/document/';
            $extension = $file1->getClientOriginalExtension();
            $fileName = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension;
            $file1->move($path, $fileName);

            $extension2 = $file2->getClientOriginalExtension();
            $fileName2 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension2;
            $file2->move($path, $fileName2);


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

    public function documents_store_ciencia($file1,$file2,$file3,$file4,$file5,$file6,$file7,$file8, $id)
    {
        $date = Carbon::now();
        $email = Auth::user()->email;

        if($file1 !=null && $file2 !=null && $file3 !=null && $file4 !=null
        && $file5 !=null && $file6 !=null && $file7 !=null && $file8 !=null){

            $path = public_path().'/document/';
            $extension = $file1->getClientOriginalExtension();
            $fileName = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension;
            $file1->move($path, $fileName);

            $extension2 = $file2->getClientOriginalExtension();
            $fileName2 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension2;
            $file2->move($path, $fileName2);

            $post1 = new Post1();
            $post1->student_id = $id;
            $post1->extenso = $fileName;
            $post1->carta_aceptacion = $fileName2;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();

            $path = public_path().'/document/';
            $extension3 = $file3->getClientOriginalExtension();
            $fileName3 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension3;
            $file3->move($path, $fileName3);

            $extension4 = $file4->getClientOriginalExtension();
            $fileName4 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension4;
            $file4->move($path, $fileName4);


            $post1 = new Post2();
            $post1->student_id = $id;
            $post1->extenso = $fileName3;
            $post1->carta_aceptacion = $fileName4;
            $post1->created_at = $date;
            $post1->updated_at = $date;
            $post1->save();

            $path = public_path().'/document/';
            $extension5 = $file5->getClientOriginalExtension();
            $fileName5 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension5;
            $file5->move($path, $fileName5);

            $extension6 = $file6->getClientOriginalExtension();
            $fileName6 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension6;
            $file6->move($path, $fileName6);

            $extension7 = $file7->getClientOriginalExtension();
            $fileName7 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension7;
            $file7->move($path, $fileName7);

            $extension8 = $file8->getClientOriginalExtension();
            $fileName8 = uniqid().'_user_'.$email.'_'.date('Y-m-d').'.'.$extension8;
            $file8->move($path, $fileName8);

            $pre = new Presentation();
            $pre->student_id = $id;
            $pre->extenso = $fileName5;
            $pre->carta_aceptacion = $fileName6;
            $pre->poster = $fileName7;
            $pre->certificado = $fileName8;
            $pre->created_at = $date;
            $pre->updated_at = $date;
            $pre->save();

        }else{
            return back();
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
        $post1 = Post1::where('student_id',$id)->first();
        $post2 = Post2::where('student_id',$id)->first();
        $pre = Presentation::where('student_id',$id)->first();
        $calificacion = ['0'=>'Seleccione calificación','1'=>'Aprobado','2'=>'No Aprobado'];
        return view('pages.estudiantes.calificacion',compact('estudiante','post1','post2','pre','calificacion'));

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

    public function downloadDocument($file)
    {
         $path = public_path().'/document';
        if($file == ''){
            Session::flash('message-error','No posee documento');
            return back();
        }else{
            $doc = $path.'/'.$file;
            return response()->download($doc);
        }
    }

    public function downloadSedePdf($id)
    {
            $users = Sede::leftjoin('users', 'sedes.id', '=', 'users.sede_id')
            ->leftjoin('students','users.id','students.user_id')
            ->leftjoin('posts_1','students.id','posts_1.student_id')
            ->select('posts_1.id AS id','posts_1.created_at AS created_at')
            ->where('sedes.id',$id)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });;
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

        $posts2 = Sede::leftjoin('users', 'sedes.id', '=', 'users.sede_id')
        ->leftjoin('students','users.id','students.user_id')
        ->leftjoin('posts_2','students.id','posts_2.student_id')
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

        $pres = Sede::leftjoin('users', 'sedes.id', '=', 'users.sede_id')
        ->leftjoin('students','users.id','students.user_id')
        ->leftjoin('presentations','students.id','presentations.student_id')
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

            $post1 = User::leftjoin('students','users.id','students.user_id')
            ->leftjoin('posts_1','students.id','posts_1.student_id')
            ->select('posts_1.id AS id')
            ->where('users.identification_card',$id)
            ->first();

            if(isset($post1)){
                $p1 = 2;
            }
            $post2 = User::leftjoin('students','users.id','students.user_id')
            ->leftjoin('posts_2','students.id','posts_2.student_id')
            ->select('posts_2.id AS id')
            ->where('users.identification_card',$id)
            ->first();

            if(isset($post2)){
                $p2 = 2;
            }
            $pre = User::leftjoin('students','users.id','students.user_id')
            ->leftjoin('presentations','students.id','presentations.student_id')
            ->select('presentations.id AS id')
            ->where('users.identification_card',$id)
            ->first();

            if(isset($pre)){
                $p3 = 4;
            }

            $total = $p1 + $p2 + $p3;

        $user = DB::table('users')->where('identification_card',$id)->first();
        $pdf = PDF::loadView('pages.reports.student', compact('total','user'));

        return $pdf->stream('report-estudiante.pdf');
    }
}
