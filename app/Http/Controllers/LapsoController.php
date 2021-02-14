<?php

namespace App\Http\Controllers;

use App\Lapso;
use App\Lapso_estudiante;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class LapsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lapsos = Lapso::latest()->paginate(5);
        
        return view('pages.lapsos.index',compact('lapsos'))
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
        $data = $request->all();
        $fecha1 =  strtotime($request->start_lapse);
        $mes1 = date("m", $fecha1);
        $mes1 = $this->obtenerMes($mes1);
        $anno1 = date("Y", $fecha1);

        $fecha2 =  strtotime($request->end_lapse);
        $mes2 = date("m", $fecha2);
        $mes2 = $this->obtenerMes($mes2);
        $anno2 = date("Y", $fecha2);
    
        $name = 'Lapso '.$mes1.' '.$anno1.'-'.$mes2.' '.$anno2;
        
        $name = array('name'=>$name);
        $data = array_replace($data,$name);

        $start = array('start_lapse'=>$this->setDateInDB($request->start_lapse));
        $data = array_replace($data,$start);

        $end = array('end_lapse'=>$this->setDateInDB($request->end_lapse));
        $data = array_replace($data,$end);

        $lapso = Lapso::create($data);

        $lapsos = Lapso::latest()->paginate(5);
  
        return view('pages.lapsos.index',compact('lapsos'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
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
        $data = Lapso::find($id);
        return response()->json($data);
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
        $data = $request->all();
        $lapso = Lapso::find($id);
        $lapso->update($data);
        $lapso->save();

        if($request->status == '0'){
            $lapse = Lapso_Estudiante::where('note','>', '49')->update(['status'=>'Aprobado']);
            $lapse = Lapso_Estudiante::where('note','<', '50')->update(['status'=>'Reprobado']);
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $Lapso = Lapso::find($id);
      $Lapso->delete();
      return back();
    }

    public function setDateInDB($date_val){
        //$date_val = str_replace("'", "", $get_date_val);

        if ( ( $date_val === '0000/00/00' ) || ( $date_val === '0000/00/00 00:00:00' ) ){
            $date_val_0 = date("Y-m-d H:i:s", strtotime("1970-10-02 02:02:02"));
            //$date_val = '';
            return $date_val_0;
        } else if ( ( $date_val === '0000-00-00' ) || ( $date_val === '0000-00-00 00:00:00' ) ){
            $date_val_0 = date("Y-m-d H:i:s", strtotime("1970-10-02 02:02:02"));
            //$date_val = '';
            return $date_val_0;
        } else if ( strlen($date_val) < 1 ){
            $date_val_0 = date("Y-m-d H:i:s", strtotime("1970-10-02 02:02:02"));
            //$date_val = '';
            return $date_val_0;
        } else {
            $date_val_1 = str_replace("'", "", $date_val);
            $date_val_0 = date("Y-m-d H:i:s", strtotime($date_val_1));
            return $date_val_0;
        }
    }

    public function obtenerMes($mes){

        switch ($mes) {
            case 01:
                return "Enero";
                break;
            case 02:
                return "Febrero";
                break;
            case 03:
                return "Marzo";
                break;
            case 04:
                return "Abril";
                break;
            case 05:
                return "Mayo";
                break;
             case 06:
                return "Junio";
                break;
            case 07:
                return "Julio";
                break;
            case '08':
                return "Agosto";
                break;
             case '09':
                return "Septiembre";
                break;
            case '10':
                return "Octubre";
                break;
            case '11':
                return "Noviembre";
                break;
             case '12':
                return "Diciembre";
                break;
            default:
                break;   
        }
    }

    public function getPensum(){
        
        $asignaturas = DB::table('courses')->orderBy('semester_id','ASC')->get();
        return view('pages.lapsos.pensum',compact('asignaturas'));
    }    

}