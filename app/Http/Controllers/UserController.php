<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::latest()->paginate(5);
        $roles = ['0'=>'Administrativo','1'=>'Operativo','2'=>'Directivo'];
        return view('pages.usuarios.index',compact('usuarios','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function indexAdministrativo()
    {
        $usuarios = User::where('rol_id','0')->latest()->paginate(5);
        $roles = ['0'=>'Administrativo','1'=>'Operativo','2'=>'Directivo'];
        return view('pages.usuarios.index',compact('usuarios','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function indexDirectivo()
    {
        $usuarios = User::where('rol_id','2')->latest()->paginate(5);
        $roles = ['0'=>'Administrativo','1'=>'Operativo','2'=>'Directivo'];
        return view('pages.usuarios.index',compact('usuarios','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

     public function indexOperativo()
    {
        $usuarios = User::where('rol_id','1')->latest()->paginate(5);
        $roles = ['0'=>'Administrativo','1'=>'Operativo','2'=>'Directivo'];
        return view('pages.usuarios.index',compact('usuarios','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
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
       User::create($request->all());
   
       return redirect()->route('user.index')
                        ->with('success','Usuario guardado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = ['0'=>'Administrativo','1'=>'Operativo','2'=>'Directivo'];
        return view('pages.usuarios.edit',compact('user','roles'));
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
        //dd($request->all());
        $data = $request->all();
        $user = User::find($id);
        $user->update($data);
        $user->save();
        return redirect()->route('user.index')
                        ->with('success','Usuario editado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = User::find($id);
      $user->delete();
      return back();
    }
}