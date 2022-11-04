<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Auth;

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
        if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Teacher'))
        {
            $teachers = Teacher::all();

             return view('pages.grade.create')->with('teachers', $teachers);
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
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

        return redirect()->route('class.index')->with('status', 'Added');
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
        if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Teacher'))
        {
            $teachers = Teacher::all();
            $class = Grade::find($id);
    
            return view('pages.grade.edit')->with('teachers', $teachers)->with('class', $class);
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
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

        return redirect()->route('class.index')->with('status', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Teacher'))
        {
             $class = Grade::find($id);
             $class->subjects()->detach();
             $class->delete();

             return back()->with('status', 'Deleted');
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
       
    }

    public function assignSubject($classid)
    {
        if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Teacher'))
        {
            $subjects   = Subject::all();
            $assigned   = Grade::with(['subjects','students'])->find($classid);
    
            return view('pages.grade.assign-subject')->with('classid', $classid)->with('subjects',$subjects)->with('assigned', $assigned);
        }
        else
        {
            return back()->with('status', 'No Access');
        }
    }

    /*
     * Add Assigned Subjects to Grade 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAssignedSubject(Request $request, $id)
    {
        $class = Grade::find($id);

        $class->subjects()->sync($request->selectedsubjects);

        return redirect()->route('class.index')->with('status', 'Assigned Subject');
    }
}
