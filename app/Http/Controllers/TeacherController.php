<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use Auth;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::paginate(10);

        return view('pages.teacher.index')->with('teachers', $teachers);
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
            return view('pages.teacher.create');
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
        $this->validate($request, [ 'name' => 'required|string|max:255',
                                    'email' => 'required|email|string|max:255',
                                    'password' => 'required|string|max:4',
                                    'phone' => 'required|string',
                                    'gender' => 'required|string',
                                    'dateofbirth' => 'required|date',
                                    'address' => 'required|string',                         
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
  

         $user->teacher()->create([
            'gender'            => $request->input('gender'),
            'phone'             => $request->input('phone'),
            'dateofbirth'       => $request->input('dateofbirth'),
            'address'           => $request->input('address')
        ]);

        $user->assignRole('Teacher');

        return redirect()->route('teacher.index')->with('status', 'Added');
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
            $teacher = Teacher::find($id);
            return view('pages.teacher.edit')->with('teacher', $teacher);
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

        $this->validate($request, [ 'name' => 'required|string|max:255',
                                    'email' => 'required|email|string|max:255',
                                    'phone' => 'required|string',
                                    'gender' => 'required|string',
                                    'address' => 'required|string',  
    ]);
    $teacher = Teacher::find($id);

    $user = User::findOrFail($teacher->user_id);


    if($request->hasfile('profile_picture'))
    {
        $fileNameWithExt = $request->file('profile_picture')->getClientOriginalName();

         $ext = $request->file('profile_picture')->getClientOriginalExtension();

         $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

         $fileNameToStore = $fileName.'_'.time().'.'.$ext;   
         
         $path = $request->file('profile_picture')->storeAs('public/profile_pictures', $fileNameToStore);

         Storage::delete('public/profile_pictures'. $teacher->profile_picture);

         $user->profile_picture = $fileNameToStore;
    }  


        $user->name = $request->input('name');
        $user->email = $request->input('email');  
        $user->update();

        $teacher->gender = $request->input('gender');
        $teacher->phone = $request->input('phone');
        $teacher->address = $request->input('address');
        $teacher->update();


        
        return redirect()->route('teacher.index')->with('status', 'Updated');

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
            
             $teacher = Teacher::find($id);
             $user = User::findOrFail($teacher->user_id);
             $user->removeRole('Teacher');    
             if($teacher->profile_picture != 'profile.png')
             {
                Storage::delete('public/profile_pictures'. $teacher->profile_picture);
             }
         
             $teacher->delete();
             $user->delete();

             return back()->with('status', 'Deleted');  
        }
        else
        {
            return back()->with('status', 'No Access');
        }
      
    }
}
