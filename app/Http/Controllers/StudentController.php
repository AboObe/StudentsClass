<?php

namespace App\Http\Controllers;

use App\Student;
use App\Claass;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;

class StudentController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = DB::table('students')
                    ->leftjoin('claasses','claasses.id','=','students.class_id')
                    ->select('students.*','claasses.name as class_name')->get();

        return $students;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $claasses = DB::table('claasses')->where('maximum_students','<',10)->get();

        return $claasses;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = $request->date_of_birth['day'].'/'.$request->date_of_birth['month'].'/'.$request->date_of_birth['year'];
        
        $input = [
            'first_name' => trim($request->first_name),
            'last_name' => $request->last_name ,
            'date_of_birth' => Carbon::parse($date ),
            'class_id' => $request->class_id,
        ];

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required',
            'class_id' => 'required|numeric'
        ]);
   
        if($validator->fails()){
            return response()->json($validator->errors());        
        }

        $temp = DB::table('claasses')
                ->where('id','=',$input['class_id'])
                ->where('maximum_students','<',10)->first();

        if($temp == null)
        {
            return response()->json("the class is full");
        }
        else
        {
            $student = Student::create($input);
            Claass::where('id', $input['class_id'])->update(["maximum_students"=>$temp->maximum_students+1]);
            return $student;
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = DB::table('students')
                    ->leftjoin('claasses','claasses.id','=','students.class_id')
                    ->select('students.*','claasses.name as class_name')
                    ->where('students.id','=',$id)->get();

        $claasses = DB::table('claasses')->where('maximum_students','<',10)->get();

        $data=[
            "claasses"=>$claasses,
            "student"=>$student];
        return response()->json($student);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = [
            'first_name' => trim($request->first_name),
            'last_name' => $request->last_name ,
            'date_of_birth' => Carbon::parse($request->date_of_birth),
            'class_id' => $request->class_id,
        ];

        $student = Student::find($id);
        if ($student == null ) {
            return response()->json('student not found.');
        }

        $old_class = DB::table('claasses')->where('id','=',$student->class_id)->first();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required',
            'class_id' => 'required|numeric'
        ]);
   
        if($validator->fails()){
            return response()->json($validator->errors());        
        }

        if($old_class->id != $input['class_id']){
            $temp = DB::table('claasses')->where('id','=',$input['class_id'])->where('maximum_students','<',10)->first();
            if($temp == null)
            {
                return response()->json("the class is full");
            }
            else
            {
                Student::where('id', $id)->update($input);
                Claass::where('id', $input['class_id'])->update(["maximum_students"=>$temp->maximum_students+1]);
                Claass::where('id', $old_class->id)->update(["maximum_students"=>$old_class->maximum_students-1]);
                $student = Student::find($id);
                return $student;
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
