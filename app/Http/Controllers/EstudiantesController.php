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
}
