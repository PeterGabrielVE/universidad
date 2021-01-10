<?php

namespace App\Http\Controllers;

use App\Lapso;
use Illuminate\Http\Request;

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
        $name = 'Lapso Enero 2021-Marzo 2021';
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
}
