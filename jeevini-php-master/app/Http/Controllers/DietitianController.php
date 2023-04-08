<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\dietitianlogin;
use DB;

class DietitianController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth:doctorlogin',['except'=>['doctorlogin']]);
       Auth::shouldUse('dietitianlogin');

    }

    // public function register2(Request $request){
        
    //     $validator=Validator::make($request->all(),[
    //         'Email_id'=>'required|email',
    //         'Password'=>'required|string|min:6'
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors()->toJson(),400);
    //     }
    //     $doctor=doctorlogin::create(array_merge(
    //         $validator->validated(),
    //         ['Password'=>bcrypt($request->password)]
    //     ));
    //     return response()->json([
    //         'message'=>'user registered',
    //         'doctorlogin'=>$doctor
    //     ],201);
    // }
    
    
    
   
    public function dietitianlogin(Request $request)
    {
        
        $validator=Validator::make($request->all(),[
            'D_Email_id'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors(),422);
        }
        if(!$token=auth()->attempt($validator->validated()))
        {
            return response()->json(['error'=>'D_Email_id or password is incorrect'],401);
        }
        return $this->createNewToken2($token);
    }
    public function createNewToken2($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60,
            //'adminlogin'=>auth()->adminlogin()
            //'doctorlogin'=>DB::table('tbl_doctor')->select('Dr_Email_id')->get()

        ]);

    }
}
