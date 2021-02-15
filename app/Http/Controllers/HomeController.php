<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Estudiante;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $count_estudiante = Estudiante::select('id')->count();
        return view('home', compact('count_estudiante'));
    }
}
