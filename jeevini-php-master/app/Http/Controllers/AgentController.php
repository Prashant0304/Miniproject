<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\agentlogin;
use DB;

class AgentController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth:doctorlogin',['except'=>['doctorlogin']]);
       Auth::shouldUse('agentlogin');   //Force assign to use required guard else default guard will be used.
                                        //mention guards in auth.php
    }
   
    public function agentlogin(Request $request)
    {
        
        $validator=Validator::make($request->all(),[
            'F_Email_id'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors(),422);
        }
        if(!$token=auth()->attempt($validator->validated()))
        {
            return response()->json(['error'=>'F_Email_id or password is incorrect'],401);
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
            //'agentlogin'=>DB::table('feild_agent')->select('F_Email_id')->get()

        ]);

    }
}
