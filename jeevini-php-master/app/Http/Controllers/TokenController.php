<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Token;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->_token = new Token;
    }
    
    //Listing all tokens created
    public function ListAllTokens()
    {
     $values = $this->_token->listalltoken();
     return json_encode($values);
    }

    public function countpendingattending()
    {
     $values = $this->_token->countisAttended();
     return json_encode($values);
    }
    
    //IsAttended status is updating like pending to attending
    public function isAttendedUpdate(Request $request)
    {
        $id = $request->ID;
        
       $resp_data= DB::table('info_from_customers')
      ->where('ID', $id)
      ->update(['isAttended' => 'A']);
     
	  return array("ID"=>$id,'msg'=>"updated");
    }

    //updating token phases like whatsapp token to doctor token soo on
    public function UpdateTokenStatus(Request $request)
    {
        $patient_id = $request->ID;
        $status = $request->token_status;
       $resp_data= DB::table('info_from_customers')
      ->where('ID', $patient_id)
      ->update(['token_status' => $status]);
     
	  return array("ID"=>$patient_id,'msg'=>"updated");
    }

    //Admin adding customercare details
    public function AddCustomercare(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['FirstName'];
        $LastName = $raw_data['LastName'];
        $Location  = $raw_data['Location'];
        $speciality = $raw_data['speciality'];
        $customercare_phone = $raw_data['customercare_phone'];
        $validator = Validator::make($request->all(),[
           'customercare_phone' => 'required|unique:diatitian_customer_care',
        ]);
        if ($validator->fails()) 
        {
            $response = [
            'message' => "User Exist",
            'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
        $Age = $raw_data['Age']; 
        $Email_id = $raw_data['Email_id'];
        $validator = Validator::make($request->all(),[
            'Email_id' => 'required|unique:diatitian_customer_care',
         ]);
         if ($validator->fails()) 
         {
             $response = [
             'message' => "User Exist",
             'key' => 0,
             ];
             return response()->json($validator->errors(), 400);
         }
        $password = $raw_data['password'];
   
        $insert_customer_data = array
        (
                   "customercare_id" => null,
                   "FirstName" => $raw_data['FirstName'],
                   "LastName" => $raw_data['LastName'],
                   "Location" => $raw_data['Location'],
                   "speciality" => $raw_data['speciality'],
                   "customercare_phone"=>$raw_data['customercare_phone'],
                   "Age" => $raw_data['Age'],
                   "Email_id" => $raw_data['Email_id'],
                   "password" => bcrypt($raw_data['password']),
        );
        $customerID = $this->_token->addCustomercare($insert_customer_data);
           
        return array('customercare_id'=>$customerID,'msg'=>"customercare data inserted successfully");
    }

    public function UpdateCustomercare(Request $request)
    {
          $customercare_id=$request->input('customercare_id');
          $FirstName = $request->input('FirstName');
          $LastName = $request->input('LastName');
          $Location = $request->input('Location');
          $speciality = $request->input('speciality');
          $customercare_phone = $request->input('customercare_phone');
          $Age = $request->input('Age');
          
          
          $update_customer_data = array(
                  
                  "FirstName" => $FirstName,
                  "LastName" => $LastName,
                  "Location" =>  $Location,
                  "speciality" => $speciality,
                  "customercare_phone" => $customercare_phone,
                  "Age" => $Age,
                  
        
          );
          $customer= $this->_token->updateCustomercare($update_customer_data,$customercare_id);  
          return json_encode("Updated Successfull");

        }

        public function customercareDelete(Request $request){
            $customercare_id = $request->customercare_id;
            $del = DB::table('diatitian_customer_care')->where('customercare_id',$customercare_id)->delete();
            return  $resp = array('status'=>'1','msg'=>'Deleted Successfully');
        }


    //list all customercare details
    public function customercarelist()
    {
     $values = $this->_token->listcustomercare();
     return json_encode($values);
    }

    //customeragent adding comment
    public function AddComment(Request $request)
    {
        if(empty($request->token_id) || empty($request->customercare_id)||empty($request->customercare_comment)|| empty($request->doctor_id)|| empty($request->token_status))
        {
            $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
        }
        else
        {
            $detail_data = array();
            $detail_data['token_id'] = $request->token_id;
            $detail_data['doctor_id'] = $request->doctor_id;
            $detail_data['customercare_id'] = $request->customercare_id;
            $detail_data['customercare_comment'] = $request->customercare_comment;
            $detail_data['token_status'] = $request->token_status;
            $detail_data['created_at'] = Carbon::now('Asia/Kolkata');
            $detail_data['updated_at'] = Carbon::now('Asia/Kolkata');
            $detailID = DB::table('token_history')->insertgetID($detail_data);

            $resp = array('msg'=>'Comment Added Successfully','data'=>$detailID);
        }

        echo json_encode($resp);
        exit;
    }

     //checking whether comment added or not
    public function ListToken(Request $request)
    {
        if(empty($request->token_id) || empty($request->customercare_id))
        {
            $token_data = $this->_token->allToken();
        }
        else
        {       
           $token_data = $this->_token->allToken($request->token_id,$request->customercare_id); 
        }
    	
        if(!empty($token_data))
    	{
    		$resp = array('code'=>'200','data'=>$token_data);
    	}
    	else
    	{
    		$resp = array('code'=>'400','data'=>null);
    	}

    	echo json_encode($resp);
    }

    //Admin adding doctor details
    public function AddDoctor(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['Dr_FirstName'];
        $LastName = $raw_data['Dr_LastName'];
        $Location  = $raw_data['Dr_Location'];
        $speciality = $raw_data['Dr_speciality'];
        $doctor_phone = $raw_data['doctor_phone'];
        $validator = Validator::make($request->all(),[
            'doctor_phone' => 'required|unique:tbl_doctor',
        ]);
        if ($validator->fails()) 
        {
            $response = [
                'message' => "User Exist",
                'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
        $Age = $raw_data['Dr_Age']; 
        $Dr_Email_id = $raw_data['Dr_Email_id'];
        $validator = Validator::make($request->all(),[
            'Dr_Email_id' => 'required|unique:tbl_doctor',
         ]);
         if ($validator->fails()) 
         {
             $response = [
             'message' => "User Exist",
             'key' => 0,
             ];
             return response()->json($validator->errors(), 400);
         }
        $password = $raw_data['password'];
        $Dr_Register_no = $raw_data['Dr_Register_no'];
        $validator = Validator::make($request->all(),[
            'Dr_Register_no' => 'required|unique:tbl_doctor',
         ]);
         if ($validator->fails()) 
         {
             $response = [
             'message' => "User Exist",
             'key' => 0,
             ];
             return response()->json($validator->errors(), 400);
         }


       $insert_doctor_data = array(
                "doctor_id" => uniqid(),
                "Dr_FirstName" => $raw_data['Dr_FirstName'],
                "Dr_LastName" => $raw_data['Dr_LastName'],
                "Dr_Location" => $raw_data['Dr_Location'],
                "Dr_speciality" => $raw_data['Dr_speciality'],
                "doctor_phone"=>$raw_data['doctor_phone'],
                "Dr_Age" => $raw_data['Dr_Age'],
                "Dr_Email_id" => $raw_data['Dr_Email_id'],
                "password" => bcrypt($raw_data['password']),
                "Dr_Register_no" => $raw_data['Dr_Register_no'],

                );
        $doctorID = $this->_token->addDoctor($insert_doctor_data);
        
        return array('msg'=>"Doctor data inserted successfully");

    }

    public function Updatedoctor(Request $request)
    {
          $doctor_id=$request->input('doctor_id');
          $Dr_FirstName = $request->input('Dr_FirstName');
          $Dr_LastName = $request->input('Dr_LastName');
          $Dr_Location = $request->input('Dr_Location');
          $Dr_speciality = $request->input('Dr_speciality');
          $doctor_phone = $request->input('doctor_phone');
          $Dr_Age = $request->input('Dr_Age');
          
          
          $update_doctor_data = array(
                  
                  "Dr_FirstName" => $Dr_FirstName,
                  "Dr_LastName" => $Dr_LastName,
                  "Dr_Location" =>  $Dr_Location,
                  "Dr_speciality" => $Dr_speciality,
                  "doctor_phone" => $doctor_phone,
                  "Dr_Age" => $Dr_Age,
                  );
          $doctor= $this->_token->updatedoctor($update_doctor_data,$doctor_id);  
          return json_encode("Updated Successfull");

        }

        public function doctorDelete(Request $request){
            $doctor_id = $request->doctor_id;
            $del = DB::table('tbl_doctor')->where('doctor_id',$doctor_id)->delete();
            return  $resp = array('status'=>'1','msg'=>'Deleted Successfully');
        }


    //retrive all doctor data
    public function docotrlist()
    {
     $values = $this->_token->listdocotr();
     return json_encode($values);
    }

    public function updatedoctorisAttended(Request $request)
    {
        $token_id = $request->token_id;
        //$isAttended = $request->isAttended;
       $resp_data= DB::table('doctor_token_history')
      ->where('token_id', $token_id)
      ->update(['isAttended' => 'A']);
     
	  return array("token_id"=>$token_id,'msg'=>"isAttende updated");
    }

    public function countdoctorpendingattending()
    {
     $values = $this->_token->countdoctorisAttended();
     return json_encode($values);
    }


    //Doctor adding comment or prescription
    public function AdddoctorComment(Request $request)
    {
        $token_id=$request->token_id;
        $ID=$request->ID;
        $doctor_id=$request->doctor_id;
        
        if(empty($token_id) || empty($ID) || empty($doctor_id)||empty($request->doctor_comment) || empty($request->dietitian_id) || empty($request->token_status))
        {
            $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
        }
        else
        {
            $doctor_data = DB::table('token_history as t')
            ->select("t.*")         
           ->where('t.token_id',$token_id)
            ->where('t.doctor_id',$doctor_id)
            ->get();

            //return $doctor_data;
            $detail_data = array();
            $detail_data['ID'] = $ID;
            $detail_data['token_id'] = $token_id;
            $detail_data['doctor_id'] = $doctor_id;
            $detail_data['doctor_comment'] = $request->doctor_comment;
            //$detail_data['customercare_id'] = $doctor_data->customercare_id;
            $detail_data['dietitian_id'] = $request->dietitian_id;
            $detail_data['token_status'] = $request->token_status;
            $detail_data['created_at'] = Carbon::now('Asia/Kolkata');
            $detail_data['updated_at'] = Carbon::now('Asia/Kolkata');
            $detailID = DB::table('doctor_token_history')->insertgetID($detail_data);

            $resp = array('msg'=>'Comment Added Successfully','data'=>$detailID);
        }

        echo json_encode($resp);
        exit;
    }

    //check doctor comment added in array or not and also customer comment added
    public function ListdoctorToken(Request $request)
    {
        if(empty($request->ID) || empty($request->token_id))
        {
            $token_data = $this->_token->alldoctorToken();
        }
        else
        {       
           $token_data = $this->_token->alldoctorToken($request->ID,$request->token_id); 
        }
    	
        if(!empty($token_data))
    	{
    		$resp = array('code'=>'200','data'=>$token_data);
    	}
    	else
    	{
    		$resp = array('code'=>'400','data'=>null);
    	}

    	echo json_encode($resp);
    }

   //admin adding dietitian details
    public function AddDietitian(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['D_FirstName'];
        $LastName = $raw_data['D_LastName'];
        $Location  = $raw_data['D_Location'];
        $speciality = $raw_data['D_speciality'];
        $dietitian_phone = $raw_data['dietitian_phone'];
        $validator = Validator::make($request->all(),[
            'dietitian_phone' => 'required|unique:tbl_dietitian',
        ]);
        if ($validator->fails()) 
        {
            $response = [
                'message' => "User Exist",
                'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
        $Age = $raw_data['D_Age']; 
        $Email_id = $raw_data['D_Email_id'];
        $validator = Validator::make($request->all(),[
            'D_Email_id' => 'required|unique:tbl_dietitian',
         ]);
         if ($validator->fails()) 
         {
             $response = [
             'message' => "User Exist",
             'key' => 0,
             ];
             return response()->json($validator->errors(), 400);
         }
        $password = $raw_data['password'];

       $insert_dietitian_data = array(
                "dietitian_id" => null,
                "D_FirstName" => $raw_data['D_FirstName'],
                "D_LastName" => $raw_data['D_LastName'],
                "D_Location" => $raw_data['D_Location'],
                "D_speciality" => $raw_data['D_speciality'],
                "dietitian_phone"=>$raw_data['dietitian_phone'],
                "D_Age" => $raw_data['D_Age'],
                "D_Email_id" => $raw_data['D_Email_id'],

                "password" => bcrypt($raw_data['password']),

                );
        $dietitianID = $this->_token->addDietitian($insert_dietitian_data);
        
        return array('Dietitian_id'=>$dietitianID,'msg'=>"Diatitian data inserted successfully");
    }

    public function Updatedietitian(Request $request)
    {
          $dietitian_id=$request->input('dietitian_id');
          $D_FirstName = $request->input('D_FirstName');
          $D_LastName = $request->input('D_LastName');
          $D_Location = $request->input('D_Location');
          $D_speciality = $request->input('D_speciality');
          $dietitian_phone = $request->input('dietitian_phone');
          $D_Age = $request->input('D_Age');
          
          
          $update_dietitian_data = array(
                  
                  "D_FirstName" => $D_FirstName,
                  "D_LastName" => $D_LastName,
                  "D_Location" =>  $D_Location,
                  "D_speciality" => $D_speciality,
                  "dietitian_phone" => $dietitian_phone,
                  "D_Age" => $D_Age,
                  );
          $doctor= $this->_token->updatedietitian($update_dietitian_data,$dietitian_id);  
          return json_encode("Updated Successfull");

        }

        public function dietitianDelete(Request $request){
            $dietitian_id = $request->dietitian_id;
            $del = DB::table('tbl_dietitian')->where('dietitian_id',$dietitian_id)->delete();
            return  $resp = array('status'=>'1','msg'=>'Deleted Successfully');
        }


    //retrive all dietitian data
    public function dietitianlist()
    {
     $values = $this->_token->listdietitian();
     return json_encode($values);
    }

    public function updatedietitianisAttended(Request $request)
    {
        $token_id = $request->token_id;
        //$isAttended = $request->isAttended;
       $resp_data= DB::table('dietitian_token_history')
      ->where('token_id', $token_id)
      ->update(['isAttended' => 'A']);
     
	  return array("token_id"=>$token_id,'msg'=>"isAttende updated");
    }

    public function countdietitianpendingattending()
    {
     $values = $this->_token->countdietitianisAttended();
     return json_encode($values);
    }


    //Adding dietitian comment
    public function AdddietitianComment(Request $request)
    {
        $token_id=$request->token_id;
        $ID=$request->ID;
        $dietitian_id=$request->dietitian_id;
        if(empty($token_id) || empty($ID) || empty($dietitian_id)|| empty($request->dietitian_comment) || empty($request->Feildagent_id) || empty($request->token_status))
        {
            $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
        }
        else
        {
            $dietitian_data = DB::table('doctor_token_history as t')
            ->select("t.*")         
           ->where('t.token_id',$token_id)
            ->where('t.dietitian_id',$dietitian_id)
            ->get();

            //return $dietitian_data;
            $detail_data = array();
            $detail_data['ID'] = $ID;
            $detail_data['token_id'] = $token_id;
            $detail_data['dietitian_id'] = $dietitian_id;
            $detail_data['dietitian_comment'] = $request->dietitian_comment;
            $detail_data['Feildagent_id'] = $request->Feildagent_id;
            $detail_data['token_status'] = $request->token_status;
            $detail_data['created_at'] = Carbon::now('Asia/Kolkata');
            $detail_data['updated_at'] = Carbon::now('Asia/Kolkata');
            $detailID = DB::table('dietitian_token_history')->insertgetID($detail_data);

            $resp = array('msg'=>'Comment Added Successfully','data'=>$detailID);
        }
         echo json_encode($resp);
        exit;
    }
    //check whether dietitian comment added or not
    public function ListdietitianToken(Request $request)
    {
        if(empty($request->ID) || empty($request->token_id))
        {
            $token_data = $this->_token->alldietitianToken();
        }
        else
        {       
           $token_data = $this->_token->alldietitianToken($request->ID,$request->token_id); 
        }
    	
        if(!empty($token_data))
    	{
    		$resp = array('code'=>'200','data'=>$token_data);
    	}
    	else
    	{
    		$resp = array('code'=>'400','data'=>null);
    	}

    	echo json_encode($resp);
    }

    //admin adding feildagent details
    public function AddFeildAgent(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['F_FirstName'];
        $LastName = $raw_data['F_LastName'];
        $Location  = $raw_data['F_Location'];
        $speciality = $raw_data['F_speciality'];
        $Feildagent_phone = $raw_data['Feildagent_phone'];
        $validator = Validator::make($request->all(),[
           'Feildagent_phone' => 'required|unique:feild_agent',
        ]);
        if ($validator->fails()) 
        {
            $response = [
            'message' => "User Exist",
            'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
        $Age = $raw_data['F_Age']; 
        $F_Email_id = $raw_data['F_Email_id'];
        $validator = Validator::make($request->all(),[
            'F_Email_id' => 'required|unique:feild_agent',
         ]);
         if ($validator->fails()) 
         {
             $response = [
             'message' => "User Exist",
             'key' => 0,
             ];
             return response()->json($validator->errors(), 400);
         }
       
        $password = $raw_data['password'];
   
        $insert_feildagent_data = array
        (
                "Feildagent_id" => null,
                "F_FirstName" => $raw_data['F_FirstName'],
                "F_LastName" => $raw_data['F_LastName'],
                "F_Location" => $raw_data['F_Location'],
                "F_speciality" => $raw_data['F_speciality'],
                "Feildagent_phone"=>$raw_data['Feildagent_phone'],
                "F_Age" => $raw_data['F_Age'],
                "F_Email_id" => $raw_data['F_Email_id'],
                "password" => bcrypt($raw_data['password']),
        );
        $agentID = $this->_token->addFeildagent($insert_feildagent_data);
           
        return array('Feildagent_id'=>$agentID,'msg'=>"FeildagenT data inserted successfully");    
    }

    public function Updatefeildagent(Request $request)
    {
          $Feildagent_id=$request->input('Feildagent_id');
          $F_FirstName = $request->input('F_FirstName');
          $F_LastName = $request->input('F_LastName');
          $F_Location = $request->input('F_Location');
          $F_speciality = $request->input('F_speciality');
          $Feildagent_phone = $request->input('Feildagent_phone');
          $F_Age = $request->input('F_Age');
          
          
          $update_feildagent_data = array(
                  
                  "F_FirstName" => $F_FirstName,
                  "F_LastName" => $F_LastName,
                  "F_Location" =>  $F_Location,
                  "F_speciality" => $F_speciality,
                  "Feildagent_phone" => $Feildagent_phone,
                  "F_Age" => $F_Age,
                  );
          $doctor= $this->_token->updatefeildagent($update_feildagent_data,$Feildagent_id);  
          return json_encode("Updated Successfull");

        }

        public function feildagentDelete(Request $request){
            $Feildagent_id = $request->Feildagent_id;
            $del = DB::table('feild_agent')->where('Feildagent_id',$Feildagent_id)->delete();
            return  $resp = array('status'=>'1','msg'=>'Deleted Successfully');
        }


    //retrieveing all feildagent details
    public function feildagentlist()
    {
     $values = $this->_token->listfeildagent();
     return json_encode($values);
    }

    public function updateagentisAttended(Request $request)
    {
        $token_id = $request->token_id;
        //$isAttended = $request->isAttended;
       $resp_data= DB::table('agent_token_history')
      ->where('token_id', $token_id)
      ->update(['isAttended' => 'A']);
     
	  return array("token_id"=>$token_id,'msg'=>"isAttende updated");
    }

    public function countagentpendingattending()
    {
     $values = $this->_token->countagentisAttended();
     return json_encode($values);
    }


    public function AddfeildagentComment(Request $request)
    {
        $ID=$request->ID;
        $token_id=$request->token_id;
        $Feildagent_id=$request->Feildagent_id;
        if(empty($token_id) || empty($ID) || empty($Feildagent_id)||empty($request->agent_comment) || empty($request->token_status))
        {
            $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
        }
        else
        {
            /*$dietitian_data = DB::table('doctor_token_history as t')
            ->select("t.*")         
           ->where('t.token_id',$token_id)
            ->where('t.dietitian_id',$dietitian_id)
            ->get();*/

            //return $dietitian_data;
            $detail_data = array();
            $detail_data['ID'] = $ID;
            $detail_data['token_id'] = $token_id;
            $detail_data['Feildagent_id'] = $Feildagent_id;
            $detail_data['agent_comment'] = $request->agent_comment;
            $detail_data['token_status'] = $request->token_status;
            $detail_data['created_at'] = Carbon::now('Asia/Kolkata');
            $detail_data['updated_at'] = Carbon::now('Asia/Kolkata');
            $detailID = DB::table('agent_token_history')->insertgetID($detail_data);

            $resp = array('msg'=>'Comment Added Successfully','data'=>$detailID);
        }

        echo json_encode($resp);
        exit;
    }


    public function ListFEToken(Request $request)
    {
        if(empty($request->ID) || empty($request->token_id))
        {
            $token_data = $this->_token->allfeildagentToken();
        }
        else
        {       
           $token_data = $this->_token->allfeildagentToken($request->ID,$request->token_id); 
        }
    	
        if(!empty($token_data))
    	{
    		$resp = array('code'=>'200','data'=>$token_data);
    	}
    	else
    	{
    		$resp = array('code'=>'400','data'=>null);
    	}

    	echo json_encode($resp);
    }

    public function AddAdmin(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['FirstName'];
        $LastName = $raw_data['LastName'];
        $admin_phone = $raw_data['admin_phone'];
        $validator = Validator::make($request->all(),[
            'admin_phone' => 'required|unique:tbl_admin',
        ]);
        if ($validator->fails()) 
        {
            $response = [
                'message' => "Admin Exist",
                'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
         
        $Email_id = $raw_data['Email_id'];
        $validator = Validator::make($request->all(),[
            'Email_id' => 'required|unique:tbl_admin',
         ]);
         if ($validator->fails()) 
         {
             $response = [
             'message' => "admin Exist",
             'key' => 0,
             ];
             return response()->json($validator->errors(), 400);
         }
        $password = $raw_data['password'];
        
        $insert_admin_data = array(
                "admin_id" => uniqid(),
                "FirstName" => $raw_data['FirstName'],
                "LastName" => $raw_data['LastName'],
                "admin_phone"=>$raw_data['admin_phone'],
                "Email_id" => $raw_data['Email_id'],
                "password" => bcrypt($raw_data['password']),
                
                );
        $adminID = $this->_token->AddAdmin($insert_admin_data);
        
        return array('msg'=>"Admin data inserted successfully");

    }   
}
