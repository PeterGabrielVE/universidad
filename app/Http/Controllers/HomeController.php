<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Estudiante;
use App\User;

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
        $count_admin = User::where('rol_id','0')->select('id')->count();
        $count_dir = User::where('rol_id','2')->select('id')->count();
        $count_ope = User::where('rol_id','1')->select('id')->count();

        if(Auth::user()->roles->first()->id == 4){
            return redirect()->route('estudiantes.create');
        }else{
            return view('home', compact('count_estudiante','count_admin','count_ope','count_dir'));
        }

    }
}
