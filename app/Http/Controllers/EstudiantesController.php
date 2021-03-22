<?php

namespace App\Http\Controllers;

use App\Asignatura;
use App\Estudiante;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Pais;
use Illuminate\Http\Request;

class EstudiantesController extends Controller
{
    public function index()
    {
    	$estudiantes = Estudiante::latest()->paginate(5);
  
        return view('pages.estudiantes.index',compact('estudiantes'))
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
        return view('pages.estudiantes.create',compact('lapso','paises'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	    //dd($request->all());
            if($request->equivalency == '1'){

                $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'identification_card' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'lapse_id' => 'required',
                'country_id' => 'required',
                'equivalency' => 'required',
            ]);
      
            Estudiante::create($request->all());
            toastr()->success('¡El Estudiante fue creado exitosamente!');
            $estudiante = Estudiante::latest('id')->first();
            $asignaturas = Asignatura::orderBy('id','ASC')->get();

            return view('pages.estudiantes.equivalencia',compact('asignaturas','estudiante'));
        }


        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'identification_card' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'lapse_id' => 'required',
            'country_id' => 'required',
            'equivalency' => 'required',
        ]);
  
        Estudiante::create($request->all());

        
   
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
        return view('pages.estudiantes.edit',compact('estudiante','lapso','paises'));

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
        return response()->json($estudiante);
    }
}
