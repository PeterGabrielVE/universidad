<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Asignatura;
use App\Estudiante;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.inscripcion.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //dd($id);
        $aprobadas = Lapso_Estudiante::where('student_id',$id)->where('status','Aprobado')->pluck('course_id');
        $asignaturas = Asignatura::whereNotIn('id',$aprobadas)->take(7)->get();
        $estudiante = Estudiante::find($id);
        return view('pages.inscripcion.create',compact('asignaturas','estudiante'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
         
         $asig_array = explode(",",$request->asignaturas);
         $lapse = Lapso::latest()->first();
         $lapse_id = $lapse->id;
          foreach ($asig_array as $key =>$value) {

            $lapso = new Lapso_Estudiante();
            $lapso->student_id = $id;
            $lapso->lapse_id = $lapse_id;
            $lapso->course_id = $value;
            $lapso->note = '0';
            $lapso->status = 'Cursando';
            $lapso->save();
        }
        $data = '200';
        return response()->json($data);
    }

    public function storeEquivalency(Request $request, $id)
    {
         
         $asig_array = explode(",",$request->asignaturas);
         $lapse = Lapso::latest()->first();
         $lapse_id = $lapse->id;
          foreach ($asig_array as $key =>$value) {

            $lapso = new Lapso_Estudiante();
            $lapso->student_id = $id;
            $lapso->lapse_id = $lapse_id;
            $lapso->course_id = $value;
            $lapso->note = '60';
            $lapso->status = 'Aprobado';
            $lapso->save();
        }
        $data = '200';
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getLapso($id){
        $carga =Lapso_Estudiante::find($id);

        $data = [];
        //dd($carga->id);
       
            $data[] = [
            'id' => $carga->id,
            'course_id' => $carga->asignatura->name,
            'note' => $carga->note
            ];

        return response()->json($data);
    }

    public function updateLapso(Request $request)
    {
        $id = $request->id;
        $data = $request->all();
        $recambio = Lapso_Estudiante::find($id);
        $recambio->update($data);
        
    }
}
