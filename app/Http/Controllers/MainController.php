<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
class MainController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function register(){
        return view('auth.register');
    }
    public function save(Request $request){
        // return $request->input();
        $request->validate([
            'name'=> 'required',
            'email'=> 'required|email|unique:admins',
            'password' => 'required|min:5|max:12'
        ]);

        // insert data into database
        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);

        $save = $admin->save();
        if($save){
            return back()->with('success','You have been successfully registered');
        }else{
            return back()->with('failed','Something went wrong');
        }

    }

    public function check(Request $request){
        // return $request->input();

        // validate request
         $request->validate([
             'email'=> 'required',
             'password'=> 'required|min:5|max:12'
         ]);
         // check if email exist in the database
         $userInfo = Admin::where('email','=',$request->email)->first();

         if(!$userInfo){
             return back()->with('fail', 'We do not recognize your email address');
         }else{
             // check password
             if(Hash::check($request->password, $userInfo->password)){
                 $request->session()->put('LoggedUser', $userInfo->id);
                 return redirect('admin/dashboard');
             }else{
                 return back()->with('fail','Incorrect password');
             }
         }

    }
    public function logout(){
        if(session()->has('LoggedUser')){
            session()->pull('LoggedUser');
            return redirect('/auth/login');
        }
    }

    public function dashboard(){
        $data = ['LoggedUserInfo' => Admin::where('id','=', session('LoggedUser'))->first()];
        return view('admin.dashboard',$data);
    }

    public function settings(){
        $data = ['LoggedUserInfo' => Admin::where('id','=', session('LoggedUser'))->first()];
        return view('admin.settings',$data);
    }
    public function profile(){
        $data = ['LoggedUserInfo' => Admin::where('id','=', session('LoggedUser'))->first()];
        return view('admin.profile',$data);
    }

    public function staff(){
        $data = ['LoggedUserInfo' => Admin::where('id','=', session('LoggedUser'))->first()];
        return view('admin.staff',$data);
    }
}
