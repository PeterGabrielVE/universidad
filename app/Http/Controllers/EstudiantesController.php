<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Asignatura;
use App\Estudiante;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Pais;
use App\Post1;
use App\Post2;
use App\Doctorado;
use App\Sede;
use App\Rol;
use App\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


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

      /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lapso = Lapso::get()->pluck('name','id');
        $paises = Pais::get()->pluck('name','id');
        $prefijo = Pais::get()->pluck('Prefijo','id');
        $doctorados = Doctorado::get()->pluck('name','id');
        $sedes = Sede::get()->pluck('name','id');
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
    	    //dd($request->all());
            /*if($request->equivalency == '1'){

                $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'identification_card' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'lapse_id' => 'required',
                'country_id' => 'required',
                'sede_id' => 'required'
            ]);

            $user = User::find(Auth::id());
            dd($user);
            Estudiante::create($request->all());
            toastr()->success('¡El Estudiante fue creado exitosamente!');
            $estudiante = Estudiante::latest('id')->first();
            $asignaturas = Asignatura::orderBy('id','ASC')->get();

            return view('pages.estudiantes.equivalencia',compact('asignaturas','estudiante'));
        }*/
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

        return redirect()->route('estudiantes.index')
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

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $estudiante = Estudiante::find($id);
        $estudiante->update($data);
        $estudiante->save();
        return back();
    }

     public function destroy(Request $request, $id)
    {
      $estudiante = Estudiante::find($id);
      $estudiante->delete();
      return back();
    }

    public function buscarEstudiantePorCedula(Request $request){

        $query = $request->cedula;
        $estudiante = Estudiante::where('identification_card',$query)->first();
        if($estudiante == null){
            $estudiante = 'null';
            return response()->json($estudiante);
        }else{
             return response()->json($estudiante);
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
            return redirect()->route('estudiante.document.post2',$id);
        }else{
            return back();
        }

    }

    public function documentStorePost2(Request $request, $id)
    {
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
            return redirect()->route('estudiante.presentation',$id);
        }else{
            return back();
        }
    }

    public function presentation($id)
    {
        $estudiante = Estudiante::find($id);
        return view('pages.estudiantes.presentation',compact('estudiante'));

    }
}
