<?php

namespace App\Http\Controllers;

use App\Estudiante;
use App\Lapso;
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
        $lapso = Lapso::latest('id')->first();
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
}
