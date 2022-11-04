<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Parents;
use Illuminate\Support\Facades\Storage;
use Auth;

class ParentController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $parents = Parents::paginate(10);

        return view('pages.parent.index')->with('parents', $parents);
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
            return view('pages.parent.create');
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
                                    'email' => 'required|email|string|max:255|unique:users',
                                    'password' => 'required|string|max:4',
                                    'phone' => 'required|string',
                                    'gender' => 'required|string',
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
  

         $user->parent()->create([
            'gender'            => $request->input('gender'),
            'phone'             => $request->input('phone'),
            'address'           => $request->input('address')
        ]);

        $user->assignRole('Parent');

        return redirect()->route('parent.index')->with('status', 'Added');
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
            $parent = Parents::find($id);
            return view('pages.parent.edit')->with('parent', $parent);
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
        $parent = Parents::find($id);

        $this->validate($request, [ 'name' => 'required|string|max:255',
                                    'email' => 'required|email|string|max:255',
                                    'phone' => 'required|string',
                                    'gender' => 'required|string',
                                    'address' => 'required|string',  
    ]);
    

    $user = User::findOrFail($parent->user_id);


    if($request->hasfile('profile_picture'))
    {
        $fileNameWithExt = $request->file('profile_picture')->getClientOriginalName();

         $ext = $request->file('profile_picture')->getClientOriginalExtension();

         $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

         $fileNameToStore = $fileName.'_'.time().'.'.$ext;   
         
         $path = $request->file('profile_picture')->storeAs('public/profile_pictures', $fileNameToStore);

         Storage::delete('public/profile_pictures'. $parent->profile_picture);

         $user->profile_picture = $fileNameToStore;
    }  


        $user->name = $request->input('name');
        $user->email = $request->input('email');  
        $user->update();

        $parent->gender = $request->input('gender');
        $parent->phone = $request->input('phone');
        $parent->address = $request->input('address');
        $parent->update();


        
        return redirect()->route('parent.index')->with('status', 'Updated');

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
            $parent = Parents::find($id);
            $user = User::findOrFail($parent->user_id);
            $user->removeRole('Parent');    
            if($parent->profile_picture != 'profile.png')
            {
               Storage::delete('public/profile_pictures'. $parent->profile_picture);
            }
             
            $parent->delete();
            $user->delete();
    
            return back()->with('status', 'Deleted'); 
        }
        else
        {
            return back()->with('status', 'No Access');
        }
       
    }
}
