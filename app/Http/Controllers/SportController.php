<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Sport;

class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        
    }
    public function index(Request $request)
    {
        //
        $limit = $request->query('limit');
        if ($limit == '' || $limit == null) $limit = 10;
        return Sport::paginate($limit);
    }
    public function match_count()
    {
        return Sport::count();
    }

    public function get_item_date(Request $request)
    {

        $m_date = $request->post('m_date');
        $type = $request->post('type');
        $get_type = $request->post('get_type');

        if($m_date == '')
        {
            $m_date = '2021-07-11';  //////temp date
            if($get_type == 'count') //////get number of item
            {
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` >='$m_date'")->count();
                }
                else
                {
                    $items = Sport::selectRaw('MID')->whereRaw("`M_Date` >='$m_date'")->count();
                }

                return $items;
            }

            if($get_type == '')  //////get data
            {
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` >='$m_date'")->get();
                }
                else
                {
                    $items = Sport::selectRaw('*')->whereRaw("`M_Date` >='$m_date'")->get();
                }

                return $items;
            }
        }
        else
        {
            if($get_type == 'count') //////get number of item
            {
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` = '$m_date'")->count();
                }
                else
                {
                    $items = Sport::selectRaw('MID')->whereRaw("`M_Date` = '$m_date'")->count();
                }

                return $items;
            }

            if($get_type == '')  //////get data
            {
                
                if($type != '')
                {
                    $items = Sport::selectRaw('*')->whereRaw("Type='$type' and `M_Date` = '$m_date'")->get();
                }
                else
                {
                    $items = Sport::selectRaw('*')->whereRaw("`M_Date` = '$m_date'")->get();
                }

                return $items;
            }
        }

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
        //
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
        $sport = Sport::findOrFail($id);
        return $sport;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function edit(Sport $sport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sport $sport)
    {
        //
    }
}
