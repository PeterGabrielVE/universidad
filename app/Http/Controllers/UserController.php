<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Estudiante;
use App\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
Use Alert;

class UserController extends Controller
{
    /** Campos que el formulario de usuarios puede asignar (evita asignación masiva). */
    private const EDITABLE_FIELDS = [
        'name', 'last_name', 'identification_card', 'phone', 'email',
        'rol_id', 'sede_id', 'country_id', 'doctorado_id',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::latest()->paginate(5);
        $roles = Role::get()->pluck('name','id');
        return view('pages.usuarios.index',compact('usuarios','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function indexAdministrativo()
    {
        $usuarios = User::where('rol_id','1')->latest()->paginate(5);
        $rol = '1';
        $roles = Role::get()->pluck('name','id');
        return view('pages.usuarios.index',compact('usuarios','rol','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function indexDirectivo()
    {
        $usuarios = User::where('rol_id','2')->latest()->paginate(5);
        $rol = '2';
        $roles = Role::get()->pluck('name','id');
        return view('pages.usuarios.index',compact('usuarios','rol','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

     public function indexAsistente()
    {
        $usuarios = User::where('rol_id',3)->latest()->paginate(5);
        $rol = '3';
        $roles = Role::get()->pluck('name','id');
        return view('pages.usuarios.index',compact('usuarios','rol','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.usuarios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       try {
       DB::beginTransaction();
       $data = $request->only(self::EDITABLE_FIELDS);
       $data['password'] = bcrypt($request->password);
       $user = User::create($data);

        switch ($request->rol_id) {
            case 1:
                $user->assignRole('Director');
                break;
            case 2:
                $user->assignRole('Coordinador');
                break;
            case 3:
                $user->assignRole('Asistente');
                break;
            case 4:
                $user->assignRole('Estudiante');

                // El resto del expediente (lapso, país, etc.) lo completa el estudiante en estudiantes.create
                $student =  new Estudiante();
                $student->user_id = $user->id;
                $student->created_at = Carbon::now();
                $student->updated_at =Carbon::now();
                $student->save();

                break;
            case 5:
                $user->assignRole('Administrador');
                break;
        }
        DB::commit();
        Alert::success('Usuario', '¡Creado exitosamente!');
        return redirect()->back();
        } catch (\Exception $e){
            DB::rollBack();
            report($e);
            Alert::error('Usuario', '¡Error durante la creación!');
            return redirect()->back();
        }
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
        $roles = Role::get()->pluck('name','id');
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
        try {
        $data = $request->only(self::EDITABLE_FIELDS);
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $user = User::findOrFail($id);

        DB::transaction(function () use ($user, $data) {
            $user->update($data);

            // users.rol_id y el rol de Spatie deben coincidir: las rutas autorizan con Spatie.
            if (array_key_exists('rol_id', $data)) {
                $roleName = Role::whereKey($data['rol_id'])->value('name');
                $user->syncRoles($roleName !== null ? [$roleName] : []);
            }
        });

        $usuarios = User::where('rol_id',$request->rol_id)->latest()->paginate(5);
        $rol = '1';
        $roles = Role::get()->pluck('name','id');
        Alert::success('Usuario', '¡Actualizado exitosamente!');
        return view('pages.usuarios.index',compact('usuarios','rol','roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
        } catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante a actualización!');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
      try {
            $user = User::find($id);
            $user->delete();
            Alert::success('Usuario', 'Eliminado exitosamente!');
            return back();
        }catch (\Exception $e){
            Alert::error('Usuario', '¡Error durante a eliminación!');
            return redirect()->back();
        }
    }
}
