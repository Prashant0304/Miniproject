<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\customerlogin;
use DB;

class CustomerController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth:doctorlogin',['except'=>['doctorlogin']]);
       Auth::shouldUse('customerlogin');

    }

    public function customerlogin(Request $request)
    {
        
        $validator=Validator::make($request->all(),[
            'Email_id'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors(),422);
        }
        if(!$token=auth()->attempt($validator->validated()))
        {
            return response()->json(['error'=>'Email_id or password is incorrect'],401);
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
