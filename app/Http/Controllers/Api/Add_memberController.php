<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\add_member;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class Add_memberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = add_member::all();
        return PostResource::collection($posts);
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
     * @param  \App\Models\add_member  $add_member
     * @return \Illuminate\Http\Response
     */
    public function show(add_member $add_member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\add_member  $add_member
     * @return \Illuminate\Http\Response
     */
    public function edit(add_member $add_member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\add_member  $add_member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, add_member $add_member)
    {
        $add_member->update($request->all());
        
        return new PostResource($add_member);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\add_member  $add_member
     * @return \Illuminate\Http\Response
     */
    public function destroy(add_member $add_member)
    {
        //
    }
}
