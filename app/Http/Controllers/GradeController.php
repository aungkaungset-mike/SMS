<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = Grade::withCount('students')->latest()->paginate(10);

        return view('pages.grade.index')->with('classes', $classes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teachers = Teacher::all();

        return view('pages.grade.create')->with('teachers', $teachers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [ 'class_name' => 'required|string|max:255',
                                    'class_code' => 'required|numeric',
                                    'class_description' => 'required|string|max:255',  
                                    'teacher_id'    => 'required|numeric',                    
        ]);

        $grade = new Grade();

        $grade->class_name = $request->input('class_name');
        $grade->class_code = $request->input('class_code');
        $grade->teacher_id = $request->input('teacher_id');
        $grade->class_description = $request->input('class_description');
        $grade->save();

        return redirect()->route('class.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teachers = Teacher::all();
        $class = Grade::find($id);

        return view('pages.grade.edit')->with('teachers', $teachers)->with('class', $class);
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
        $this->validate($request, [ 'class_name' => 'required|string|max:255',
                                    'class_code' => 'required|numeric',
                                    'class_description' => 'required|string|max:255',  
                                    'teacher_id'    => 'required|numeric',                    
        ]);  
        
        $grade = Grade::find($id);

        $grade->class_name = $request->input('class_name');
        $grade->class_code = $request->input('class_code');
        $grade->teacher_id = $request->input('teacher_id');
        $grade->class_description = $request->input('class_description');
        $grade->update();

        return redirect()->route('class.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $class = Grade::find($id);
        $class->subjects()->detach();
        $class->delete();

        return back();
    }

    public function assignSubject($classid)
    {
        $subjects   = Subject::latest()->get();
        $assigned   = Grade::with(['subjects','students'])->findOrFail($classid);

        return view('pages.grade.assign-subject')->with('classid', $classid)->with('subjects',$subjects)->with('assigned', $assigned);
    }

    /*
     * Add Assigned Subjects to Grade 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAssignedSubject(Request $request, $id)
    {
        $class = Grade::findOrFail($id);

        $class->subjects()->sync($request->selectedsubjects);

        return redirect()->route('class.index');
    }
}
