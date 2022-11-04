<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Teacher;
use Auth;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjects = Subject::paginate(10);

        return view('pages.subject.index')->with('subjects',$subjects);
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

             return view('pages.subject.create')->with('teachers',$teachers);
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
        $this->validate($request, [ 'subject_name' => 'required|string|max:255',
                                    'subject_code' => 'required|numeric',
                                    'subject_description' => 'required|string|max:255',  
                                    'teacher_id'    => 'required|numeric',                    
        ]);

        $subject = new Subject();

        $subject->subject_name = $request->input('subject_name');
        $subject->subject_code = $request->input('subject_code');
        $subject->teacher_id = $request->input('teacher_id');
        $subject->subject_description = $request->input('subject_description');
        $subject->save();

        return redirect()->route('subject.index')->with('status', 'Added');
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
        if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Teacher'))
        {
           
             $teachers = Teacher::all();
             $subject = Subject::find($id);

             return view('pages.subject.edit')->with('teachers', $teachers)->with('subject', $subject);
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
        $this->validate($request, [ 'subject_name' => 'required|string|max:255',
                                    'subject_code' => 'required|numeric',
                                    'subject_description' => 'required|string|max:255',  
                                    'teacher_id'    => 'required|numeric',                    
        ]);

        $subject = Subject::find($id);

        $subject->subject_name = $request->input('subject_name');
        $subject->subject_code = $request->input('subject_code');
        $subject->teacher_id = $request->input('teacher_id');
        $subject->subject_description = $request->input('subject_description');
        $subject->update();

        return redirect()->route('subject.index')->with('status', 'Updated');
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
           
              $subject = Subject::find($id);
   
              $subject->delete();

              return back()->with('status', 'Deleted'); 
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
    }
}
