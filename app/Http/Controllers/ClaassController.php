<?php

namespace App\Http\Controllers;

use App\Claass;
use Illuminate\Http\Request;
use DB;
use Validator;

class ClaassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $claasses = DB::table('claasses')->get();

        return $claasses;
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
        $input = [
            'name' => $request->name,
            'code' => $request->code ,
            'status' => $request->status,
            'description' => $request->description,
        ];

        $validator = Validator::make($input, [
            'code' => 'required',
            'name' => 'required',
            'status' => 'required',
        ]);
   
        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $input["maximum_students"] = 0;

        $claass = Claass::create($input);

        return $claass; 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Claass  $claass
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $claass = Claass::find($id);
        if ($claass == null ) {
            return response()->json('class not found.');
        }

        $students = DB::table('students')->where('class_id','=',$id)->get();

        $data=[
            "class"=> $claass,
            "students"=>$students
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Claass  $claass
     * @return \Illuminate\Http\Response
     */
    public function edit(Claass $claass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Claass  $claass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = [
            'name' => $request->name,
            'code' => $request->code ,
            'status' => $request->status,
            'description' => $request->description,
        ];

        $claass = Claass::find($id);
        if ($claass == null ) {
            return response()->json('class not found.');
        }


        $validator = Validator::make($input, [
            'code' => 'required',
            'name' => 'required',
            'status' => 'required',
        ]);
   
        if($validator->fails()){
            return response()->json($validator->errors());       
        }
   

        Claass::where('id', $id)->update($input);
        $claass = Claass::find($id);
        return $claass;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Claass  $claass
     * @return \Illuminate\Http\Response
     */
    public function destroy(Claass $claass)
    {
        //
    }
}
