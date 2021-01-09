<?php

namespace App\Http\Controllers;

use App\Estudiante;
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
        return view('pages.estudiantes.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	dd($request->all());
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
  
        Estudiante::create($request->all());
   
        return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
    }
}
