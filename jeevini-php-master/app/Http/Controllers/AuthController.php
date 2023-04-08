<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\adminlogin;
//use App\Models\doctorlogin;
use DB;

class AuthController extends Controller
{
    public function __construct(){
       //$this->middleware('auth:api',['except'=>['adminlogin1']]);
       Auth::shouldUse('adminlogin');

    }

    // public function register(Request $request){
        
    //     $validator=Validator::make($request->all(),[
    //         'username'=>'required|unique:adminlogin',
    //         'password'=>'required|string|min:6'
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors()->toJson(),400);
    //     }
    //     $admin=adminlogin::create(array_merge(
    //         $validator->validated(),
    //         ['password'=>bcrypt($request->password)]
    //     ));
    //     return response()->json([
    //         'message'=>'user registered',
    //         'adminlogins'=>$admin
    //     ],201);
    // }
    
    
    
    public function adminlogin1(Request $request){
        
        $validator=Validator::make($request->all(),[
            'Email_id'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        if(!$token=auth()->attempt($validator->validated())){
            return response()->json(['error'=>'Email_id or password is incorrect'],401);
        }
        return $this->createNewToken($token);
    }
    public function createNewToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60,
            //'adminlogin'=>auth()->adminlogin()
            //'adminlogin'=>DB::table('adminlogin')->select('Email_id')->get()

        ]);

    }


}
