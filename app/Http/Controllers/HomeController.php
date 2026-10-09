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
        if(Auth::user()->rol_id == 4){
            $student = Estudiante::where('user_id',Auth::user()->id)->first();
            if($student){
                return redirect()->route('estudiantes.show',$student->id);
            }else{
                return redirect()->route('estudiantes.create');
            }
        }

        $count_estudiante = Estudiante::count();
        $count_dir = User::where('rol_id','1')->count();
        $count_coord = User::where('rol_id','2')->count();
        $count_asis = User::where('rol_id','3')->count();
        $count_usuarios = User::count();

        return view('home.administrador', compact('count_estudiante','count_coord','count_asis','count_dir','count_usuarios'));

    }
}
