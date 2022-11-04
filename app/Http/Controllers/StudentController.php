<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Parents;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $students = Student::paginate(10);

        return view('pages.student.index')->with('students', $students);
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
            $classes = Grade::all();
            $parents = Parents::all();
    
            return view('pages.student.create')->with('classes', $classes)->with('parents', $parents);
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
        $this->validate($request,[ 'name'              => 'required|string|max:255',
                                   'email'             => 'required|string|email|max:255|unique:users',
                                   'password'          => 'required|string|min:4',
                                   'parent_id'         => 'required|numeric',
                                   'class_id'          => 'required|numeric',
                                   'roll_number'       => 'required|numeric',           
                                   'gender'            => 'required|string',
                                   'phone'             => 'required|string|max:255',
                                   'dateofbirth'       => 'required|date',
                                   'address'           => 'required|string|max:255',
        ]);

        $fileName = $request->file('profile_picture')->getClientOriginalName();

        $ext = $request->file('profile_picture')->getClientOriginalExtension();

        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        $fileNameToStore = $fileName.'_'.time().'.'.$ext;   
        
        $path = $request->file('profile_picture')->storeAs('public/profile_pictures', $fileNameToStore);

        
        $user = new User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->profile_picture = $fileNameToStore;
      
        $user->save();

        $user->student()->create([
            'parent_id'         => $request->input('parent_id'),
            'class_id'          => $request->input('class_id'),
            'roll_number'       => $request->input('roll_number'),
            'gender'            => $request->input('gender'),
            'phone'             => $request->input('phone'),
            'dateofbirth'       => $request->input('dateofbirth'),
            'address'           => $request->input('address')
        ]);

        $user->assignRole('Student');

        return redirect()->route('student.index')->with('status', 'Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Teacher'))
        {
            $class = Grade::with('subjects')->where('id', $student->class_id)->first();

           return view('pages.student.show')->with('class', $class)->with('student', $student);
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
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
            $student = Student::find($id);
            $classes = Grade::all();
            $parents = Parents::all();
    
            return view('pages.student.edit')->with('classes', $classes)->with('parents', $parents)->with('student', $student);
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
        $this->validate($request,[ 'name'              => 'required|string|max:255',
                                   'email'             => 'required|string|email|max:255',
                                   'parent_id'         => 'required|numeric',
                                   'class_id'          => 'required|numeric',
                                   'roll_number'       => 'required|numeric',           
                                   'gender'            => 'required|string',
                                   'phone'             => 'required|string|max:255',
                                   'dateofbirth'       => 'required|date',
                                   'address'           => 'required|string|max:255',
        ]);
    

    $student = Student::find($id);

    $user = User::findOrFail($student->user_id);


    if($request->hasfile('profile_picture'))
    {
        $fileNameWithExt = $request->file('profile_picture')->getClientOriginalName();

         $ext = $request->file('profile_picture')->getClientOriginalExtension();

         $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

         $fileNameToStore = $fileName.'_'.time().'.'.$ext;   
         
         $path = $request->file('profile_picture')->storeAs('public/profile_pictures', $fileNameToStore);

         Storage::delete('public/profile_pictures'. $student->profile_picture);

         $user->profile_picture = $fileNameToStore;
    }  


        $user->name = $request->input('name');
        $user->email = $request->input('email');  
        $user->update();

        $student->parent_id = $request->input('parent_id');
        $student->class_id = $request->input('class_id');
        $student->roll_number = $request->input('roll_number');
        $student->dateofbirth = $request->input('dateofbirth');
        $student->gender = $request->input('gender');
        $student->phone = $request->input('phone');
        $student->address = $request->input('address');
        $student->update();

        return redirect()->route('student.index')->with('status', 'Updated');  
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
            $student = Student::find($id);
            $user = User::findOrFail($student->user_id);
            $user->removeRole('Student');    
            if($student->profile_picture != 'profile.png')
            {
               Storage::delete('public/profile_pictures'. $student->profile_picture);
            }
             
            $student->delete();
            $user->delete();
    
            return back()->with('status', 'Deleted'); 
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
    }
}
