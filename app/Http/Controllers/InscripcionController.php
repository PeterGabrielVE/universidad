<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lapso_Estudiante;

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //dd($request->all());
        $id = $request->id;
        $data = $request->all();
        $recambio = Lapso_Estudiante::find($id);
        $recambio->update($data);
        
    }
}
