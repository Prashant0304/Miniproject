<?php

namespace App\Http\Controllers;
ob_start();
use DB;
use App\Helpers\TokenHelper;
use App\DietitianSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
//use Illuminate\Support\Str;
use Carbon\Carbon;

class DietitianSupportController extends Controller
{
    public function __construct()
    {
        $this->_dietitiansupport = new DietitianSupport;

    }

    public function RegisterDietitian(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['FirstName'];
        $LastName = $raw_data['LastName'];
        $mobile_no = $raw_data['mobile_no'];
        $role=$raw_data['role'];
        $user=DB::table('tbl_dietitiansupport')->where('mobile_no',$mobile_no)->get();
        // $validator = Validator::make($request->all(),[
        //     'role' => 'required|unique:tbl_dietitiansupport',
        // ]);
        //echo $user;
        foreach($user as $user)
        {
            if($user->role==$role)
            {
                $status=1;
                break;
            }
            else{
                $status=0;
            }
        }
        if ($status==1)
        {
            $response = [
                'message' => "Role already Exist",
                'key' => $status,
            ];
            return $response;
        }
        $Location  = $raw_data['Location'];
        $registration_number=$raw_data['registration_number'];

       $insert_dietitian = array(
                "d_id" => null,
                "FirstName" => $raw_data['FirstName'],
                "LastName" => $raw_data['LastName'],
                "mobile_no"=>$raw_data['mobile_no'],
                "Location" => $raw_data['Location'],
                "role"=>$raw_data['role'],
                "registration_number"=>$raw_data['registration_number'],

                );
        $dietitianID = $this->_dietitiansupport->addDietitian($insert_dietitian);

        $dietitianDetails = $this->_dietitiansupport->getDietitianDetail($dietitianID);
        $response = [
            'status'=>"Registration successfully",
            'dietitianDetails' => $dietitianDetails,

        ];
        return json_encode($response);

       //return array('Dietitian_id'=>$dietitianID,'status'=>"Registration successfully");


   }

   public function RegisterDoctor(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['FirstName'];
        $LastName = $raw_data['LastName'];
        $mobile_no = $raw_data['mobile_no'];
        $user=DB::table('tbl_dietitiansupport')->where('mobile_no',$mobile_no)->get();
        // $validator = Validator::make($request->all(),[
        //     'mobile_no' => 'required|unique:tbl_dietitiansupport',
        // ]);
        // if ($validator->fails())
        // {
        //     $response = [
        //         'message' => "User Exist",
        //         'key' => 0,
        //     ];
        //     return response()->json($validator->errors(), 400);
        // }
        $Location  = $raw_data['Location'];
        $role=$raw_data['role'];
        foreach($user as $user)
        {
            if($user->role==$role)
            {
                $status=1;
                break;
            }
            else{
                $status=0;
            }
        }
        if ($status==1)
        {
            $response = [
                'message' => "Role already Exist",
                'key' => $status,
            ];
            return $response;
        }
        $registration_number=$raw_data['registration_number'];

       $insert_dietitian = array(
                "d_id" => null,
                "FirstName" => $raw_data['FirstName'],
                "LastName" => $raw_data['LastName'],
                "mobile_no"=>$raw_data['mobile_no'],
                "Location" => $raw_data['Location'],
                "role"=>$raw_data['role'],

                "registration_number"=>$raw_data['registration_number'],

                );
        $dietitianID = $this->_dietitiansupport->addDietitian($insert_dietitian);

        $dietitianDetails = $this->_dietitiansupport->getDietitianDetail($dietitianID);
        $response = [
            'status'=>"Registration successfully",
            'dietitianDetails' => $dietitianDetails,

        ];
        return json_encode($response);

       //return array('Dietitian_id'=>$dietitianID,'status'=>"Registration successfully");


   }

   public function sendSMS($purpose,$mobile_no, $code){
    $_h = curl_init();
    curl_setopt($_h, CURLOPT_HEADER, 1);
    curl_setopt($_h, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($_h, CURLOPT_HTTPGET, 1);
    curl_setopt($_h, CURLOPT_URL, 'https://api.twilio.com' );
    curl_exec($_h);
    $sid    = 'AC8f399632103095855a1cc7360907cf43';
    $token  = '3df7ce99b76be9205b4dff5c7077fa13';
    $serviceID = 'MG13aab5e7bdeb8dc05674959b377042bf';
    $from_phone_number  = '+15672803373';
    $client = new Client($sid, $token);
    if($purpose=='code')
    {
        $client->messages->create(
            "+".$mobile_no ,
            array(
                'from' => $from_phone_number,
                "body" => "Login Authentication Code "." ".$code,
            )
        );
    }
    else{
        $client->messages->create(
            "+".$mobile_no ,
            array(
                'from' => $from_phone_number,
                "body" => "Dear Customer,\nCan diabetes be cured without medicines?\nClick on the link to watch video "." ".$code,
            )
        );
    }

}


   public function LoginOtp(Request $request){
    $mobile_no = $request->mobile_no;
    $code = rand(100000, 999999);
   // if(empty($token_id) || empty($ID)
    $exist = $this->_dietitiansupport->check_existance($mobile_no);
    if($exist == 1){
        // echo "exsit";
         $verified = $this->_dietitiansupport->updateLoginOTP($code, $mobile_no);
         $this->sendSMS('code',$mobile_no,$code);

         $result=$this->_dietitiansupport->getdetails($mobile_no);
         //$name=$result->FirstName;
        $response = [
            'message' => "Verification Code Sent Successfully",
            'key' => 1,
            'name'=>$result,

        ];
        return json_encode($response);
    }
    elseif ($exist == 0) {
        $response = [
            'message' => "User doesn't Exist",
            'key' => 0,
        ];
        return json_encode($response);
    }
}

public function  OTPverificationlogin(Request $request){
    $code =  $request->code;
    $mobile_no = $request->mobile_no;
    $data = $this->_dietitiansupport->verifyCodeLogin($code, $mobile_no);
    if (!empty($data)) {
        $payload = array(
            'user_info' => array(
                    'userId' =>  $data[0]->d_id,
                )
            );
            $token = TokenHelper::generateToken($payload);
            $response = [
                'key' => 1,
                'message' => 'Verification Done Successfully!',
                'accessToken' => $token,
                'data' => $data,
            ];
        return json_encode($response);
    }
    else {
        $response = [
                'message' => 'Invalid Verification Code',
                'key' => 0,
            ];
        return json_encode($response);
    }

}

public function  assigntodoctor(Request $request){
    $d_id=$request->d_id;
    $registration_number=$request->registration_number;
    $n=$request->input('n');
    $m=$request->input('m');

    /*if(empty($d_id) || empty($registration_number) || empty($n) || empty($m))
    {
        $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
    }*/
    $exist = $this->_dietitiansupport->check_existancedoctor($d_id,$registration_number);
    if($exist == 1)
    {
        $sql=DB::table('info_from_customers')->select('id')->skip($n)->take($m)->get()->pluck('id');
        $detaildata=array();
        $detaildata['patient_id']=$sql;
        $detaildata['d_id']=$d_id;
        $detaildata['registration_number']=$registration_number;
        $detailID= DB::table('dietitiansupport_history')->insertgetID($detaildata);
        $resp = array('msg'=>'Assigned Successfully','data'=>$detailID);
        return $resp;

    }
    elseif ($exist == 0) {
        $response = [
            'message' => "Doctor doesn't Exist",
            'key' => 0,
        ];
        return json_encode($response);
    }

}
public function  doctorlist(Request $request)
{
    $values = $this->_dietitiansupport->doctorlist();
     return json_encode($values);
}

public function  addingpatientdetails(Request $request){
    $id=$request->id;
    $patientName=$request->input('patientName');
    $mobile_no=$request->input('mobile_no');
    $old_medicine=$request->input('old_medicine');
    $new_medicine=$request->input('new_medicine');
    $exercise=$request->input('exercise');
    $Remark=$request->input('Remark');


       $insert=array();
       $insert['id']=$id;
       $insert['patientName']=$patientName;
       $insert['mobile_no']=$mobile_no;
       $insert['old_medicine']=$old_medicine;
       $insert['new_medicine']=$new_medicine;
       $insert['exercise']=$exercise;
       $insert['Remark']=$Remark;
       $insertID= DB::table('patient_details')->insertgetID($insert);
       $resp = array('msg'=>'Inserted Successfully','data'=>$insertID);
       return $resp;

}


public function  updatestatus(Request $request){
    $d_id=$request->d_id;
    $registration_number=$request->registration_number;
    $ID=$request->ID;
    $assignedTo=$request->assignedTo;
    $CallSid=$request->CallSid;
    $CallFrom=$request->CallFrom;

    $resp_data= DB::table('info_from_customers')
    ->where('ID', $ID)
    ->update(['status' => 'A']);

    $resp_data1= DB::table('info_from_customers1')
    ->where('ID', $ID)
    ->update(['status' => 'A']);

 /* //$item_data = $this->_dietitiansupport->getItemData($request->ID);
            $details=DB::table('info_from_customers')->select('ID','token_id','patientName','mobile_no','medicines','age','gender','city','height','weight','profession','Diabetes_duration',
      'FBS','PPBS','HBA1C','RBS','insulin_injection','morning_iu','afternoon_iu','evening_iu','knee_join_pain','patientComplent','Prescription_image','Blood_test_image','token_status','isAttended','date')->where('ID',$ID)
  ->first();*/
  //$data=array();
  $data['d_id']=$d_id;
  $data['registration_number']=$registration_number;
  $data['patientId']=$ID;
  $data['assignedTo']=$assignedTo;
  $data['CallSid']=$CallSid;
  $data['CallFrom']=$CallFrom;
  $insertID= DB::table('assign_history')->insertgetID($data);

  $remove=DB::table('info_from_customers1')->where('ID', $ID)->delete();

  $resp = array('msg'=>'Assigned Successfully','data'=>$ID);
  return $resp;

}


public function  listassignedpatient(){
    $details=DB::table('info_from_customers1')->select('ID','CallSid','token_id','patientName','isTablets','noOfTabs','isInsulin','noOfInsulin','mobile_no','tablets','Mg','times','age','gender','city','height','weight','profession','Diabetes_duration',
      'FBS','PPBS','HBA1C','RBS','insulin_injection','morning_iu','afternoon_iu','evening_iu','thyroid','bp','burning_foot','patientComplent','Prescription_image','Blood_test_image','token_status','isAttended','date','stage','CallAt')
  ->get();

  return $details;
}

public function  dietitianform(Request $request){
    $ID=$request->ID;
    $mobile_no=$request->input('mobile_no');
    $prev_break=$request->input('prev_break');
    $prev_lunch=$request->input('prev_lunch');
    $prev_snacks=$request->input('prev_snacks');
    $prev_dinner=$request->input('prev_dinner');
    $teacoffee=$request->input('teacoffee');
    $recc_break=$request->input('recc_break');
    $recc_lunch=$request->input('recc_lunch');
    $recc_snacks=$request->input('recc_snacks');
    $recc_dinner=$request->input('recc_dinner');


       $insert=array();
       $insert['ID']=$ID;
       $insert['mobile_no']=$mobile_no;
       $insert['prev_break']=$prev_break;
       $insert['prev_lunch']=$prev_lunch;
       $insert['prev_snacks']=$prev_snacks;
       $insert['prev_dinner']=$prev_dinner;
       $insert['teacoffee']=$teacoffee;
       $insert['recc_break']=$recc_break;
       $insert['recc_lunch']=$recc_lunch;
       $insert['recc_snacks']=$recc_snacks;
       $insert['recc_dinner']=$recc_dinner;
       $insertID= DB::table('dietitian_form')->insertgetID($insert);
       $insertID1= DB::table('dietitian_form1')->insertgetID($insert);
       $resp = array('msg'=>'Inserted Successfully');
       return $resp;

}

public function  dietitianassign(Request $request){
    $d_id=$request->d_id;
    $ID=$request->ID;

    $resp_data= DB::table('dietitian_form')
    ->where('ID', $ID)
    ->update(['status' => 'A']);

    $resp_data1= DB::table('dietitian_form1')
    ->where('ID', $ID)
    ->update(['status' => 'A']);

 /* //$item_data = $this->_dietitiansupport->getItemData($request->ID);
            $details=DB::table('info_from_customers')->select('ID','token_id','patientName','mobile_no','medicines','age','gender','city','height','weight','profession','Diabetes_duration',
      'FBS','PPBS','HBA1C','RBS','insulin_injection','morning_iu','afternoon_iu','evening_iu','knee_join_pain','patientComplent','Prescription_image','Blood_test_image','token_status','isAttended','date')->where('ID',$ID)
  ->first();*/
  //$data=array();
  $data['d_id']=$d_id;
  $data['ID']=$ID;

  $insertID= DB::table('assign_dietitian_history')->insertgetID($data);

  $remove=DB::table('dietitian_form1')->where('ID', $ID)->delete();

  $resp = array('msg'=>'Assigned Successfully','data'=>$ID);
  return $resp;

}

public function exotelGetCallDetails(Request $request)
    {
        // $raw_data = $request->json()->all();
        // $CallSid=$raw_data['CallSid'];
        // $CallFrom=$raw_data['CallFrom'];
        // $CallTo=$raw_data['CallTo'];
        // $DialCallStatus=$raw_data['DialCallStatus'];
        // $Direction=$raw_data['Direction'];
        // $Created=$raw_data['Created'];
        // $DialCallDuration=$raw_data['DialCallDuration'];
        // $flow_id=$raw_data['flow_id'];
        // $ForwardedFrom=$raw_data['ForwardedFrom'];
        // $ProcessStatus=$raw_data['ProcessStatus'];
        // $RecordingUrl=$raw_data['RecordingUrl'];
        // $StartTime=$raw_data['StartTime'];
        // $tenant_id=$raw_data['tenant_id'];
        // $EndTime=$raw_data['EndTime'];
        // $CallType=$raw_data['CallType'];
        // $DialWhomNumber=$raw_data['DialWhomNumber'];
        // $From=$raw_data['From'];
        // $To=$raw_data['To'];
        // $CurrentTime=$raw_data['CurrentTime'];
        // $Legs=$raw_data['Legs'];
        // $digits=$raw_data['digits'];
        // $CustomField=$raw_data['CustomField'];
        // $OutgoingPhoneNumber=$raw_data['OutgoingPhoneNumber'];

        $CallSid=$request->input('CallSid');
        $CallFrom=$request->input('CallFrom');
        $CallTo=$request->input('CallTo');
        $DialCallStatus=$request->input('DialCallStatus');
        $Direction=$request->input('Direction');
        $Created=$request->input('Created');
        $DialCallDuration=$request->input('DialCallDuration');
        $flow_id=$request->input('flow_id');
        $ForwardedFrom=$request->input('ForwardedFrom');
        $ProcessStatus=$request->input('ProcessStatus');
        $RecordingUrl=$request->input('RecordingUrl');
        $StartTime=$request->input('StartTime');
        $tenant_id=$request->input('tenant_id');
        $EndTime=$request->input('EndTime');
        $CallType=$request->input('CallType');
        $DialWhomNumber=$request->input('DialWhomNumber');
        $From=$request->input('From');
        $To=$request->input('To');
        $CurrentTime=$request->input('CurrentTime');
        $Legs=$request->input('Legs');
        $digits=$request->input('digits');
        $CustomField=$request->input('CustomField');
        $OutgoingPhoneNumber=$request->input('OutgoingPhoneNumber');


        $insertData=array(
            'CallSid'=>$CallSid,
            'CallFrom'=>$CallFrom,
            'CallTo'=>$CallTo,
            'Direction'=>$Direction,
            'Created'=>$Created,
            'DialCallDuration'=>$DialCallDuration,
            'StartTime'=>$StartTime,
            'EndTime'=>$EndTime,
            'CallType'=>$CallType,
            'DialWhomNumber'=>$DialWhomNumber,
            'From'=>$From,
            'To'=>$To,
            'CurrentTime'=>$CurrentTime,
            'ForwardedFrom'=>$ForwardedFrom,
            'RecordingUrl'=>$RecordingUrl,
            'DialCallStatus'=>$DialCallStatus,
            'ProcessStatus'=>$ProcessStatus,
            'flow_id'=>$flow_id,
            'tenant_id'=>$tenant_id,
            'Legs'=>$Legs,
            'digits'=>$digits,
            'CustomField'=>$CustomField,
            'OutgoingPhoneNumber'=>$OutgoingPhoneNumber
        );

        $str=substr($CallFrom, 1);
        //echo "91".$str;
        $this->sendSMS("link","91".$str,"https://youtu.be/btVvFCnF4KY");

        $exist=DB::table('customer_details_exotel')->where('CallFrom',$CallFrom)->exists();

        $status= $this->_dietitiansupport->insertUserDetailsExotel($insertData,$exist);

        if($status==1)
        {

            return ['status'=>'successfully inserted new entry'];
        }
        else
        {
            return ['status'=>'failed to insert'];
        }
    }

    public function getMealDetails(Request $request)
    {
         $mob_num=$request->input('mob_num');
         $meal_id=$request->input('meal_id');
         $days=$request->input('days');
        $patient=DB::table('patients')->join('food_consume','patients.patientID','=','food_consume.patient_id')->where('phone',$mob_num)->get(['patientID','phone']);
        $details=DB::table('patients')->select('meal_type_id','item_id','item_occurance','date')->join('food_consume','patients.patientID','=','food_consume.patient_id')->where('phone',$mob_num)
        ->where('meal_type_id',$meal_id)->whereDate('date','>=', Carbon::now()->subDays($days))->whereDate('date','<', Carbon::now())->get();

        return [
            "patient_details"=>$patient[0],
            "status"=>$details
        ];
    }

    public function doctor_forms(Request $request)
    {

        $raw_data = $request->json()->all();
        $CallSid=$raw_data['CallSid'];
        $token_id = $raw_data['token_id'];
        $mobile_no  = $raw_data['mobile_no'];
        // $str = $mobile_no ;
        // $str2 = substr($str,2);
        // $validator = Validator::make($request->all(),[
        //     'mobile_no' => 'required|unique:doctor_form',
        //  ]);
        //  if ($validator->fails())
        //  {
        //      $response = [
        //      'message' => "User Exist",
        //      'key' => 0,
        //      ];
        //      return response()->json($validator->errors(), 400);
        //  }

        $name = $raw_data['name'];
        $diabetes_stage = $raw_data['diabetes_stage'];
        if($diabetes_stage==1)
        {
            $new_med='Morning Feed,Before Food,Voglimore:1';
        }
        else if($diabetes_stage==2)
        {
            $new_med='Morning Feed,Before Food,Voglimore:2';
        }
        else if($diabetes_stage==3)
        {
            $new_med='Morning Feed,Before Food,Voglimore:3';
        }
        else
        {
            $new_med='Morning Feed,Before Food,Dapacan(10),Voglimore:4';
        }
        $stop_med = $raw_data['stop_med'];
        $continue_med=$raw_data['continue_med'];

        $reccBreakfast=$raw_data['reccBreakfast'];
        $reccLunch=$raw_data['reccLunch'];
        $reccSnacks=$raw_data['reccSnacks'];
        $reccDinner=$raw_data['reccDinner'];


        $insert_doctor_form = array
        (
                   "CallSid"=>$CallSid,
                   "token_id" => $raw_data['token_id'],
                   "mobile_no" => $raw_data['mobile_no'],
                   "name" => $raw_data['name'],
                   "diabetes_stage" => $raw_data['diabetes_stage'],
                   "stop_med" => $raw_data['stop_med'],
                   "continue_med"=>$raw_data['continue_med'],
                   "new_med" =>$new_med,
                   "reccBreakfast"=>$raw_data['reccBreakfast'],
                    "reccLunch"=>$raw_data['reccLunch'],
                    "reccSnacks"=>$raw_data['reccSnacks'],
                    "reccDinner"=>$raw_data['reccDinner'],
                    "remarks"=>'empty'
                  //  "createdAt"=>$raw_data['createdAt'],
                   // "updatedAt"=>$raw_data['updatedAt']
        );

        $chair_squats=$raw_data['chair_squats'];
        $chair_leg_raise=$raw_data['chair_leg_raise'];
        $chair_shoulder_raise=$raw_data['chair_shoulder_raise'];
        $half_squats=$raw_data['half_squats'];
        $stair_climb =$raw_data['stair_climb'];


        $insert_exercise_data = array
        (
            "CallSid"=>$CallSid,
            "token_id"=>$token_id,
            "mobile_no"=>$mobile_no,
            "chair_squats"=>$chair_squats,
            "chair_leg_raise"=>$chair_leg_raise,
            "chair_shoulder_raise"=>$chair_shoulder_raise,
            "half_squats"=>$half_squats,
            "stair_climb"=>$stair_climb
        );

        $submitted = array(

            "CallSid"=>$CallSid,
            "CallFrom" => $raw_data['mobile_no'],

        );

        $ID = $this->_dietitiansupport->exercise_insert($insert_exercise_data);
        $ID1 = $this->_dietitiansupport->doctor_form($insert_doctor_form);
        $ID2= $this->_dietitiansupport->submitted_details($submitted);
        return array('msg'=>"inserted successfully");

      }

        // public function exercise(Request $request)
        // {
        //     $raw_data=$request->json()->all();
        //     $token_id =$raw_data['token_id'];
        //     $mobile_no =$raw_data['mobile_no'];
        //     $chair_squats=$raw_data['chair_squats'];
        //     $chair_leg_raise=$raw_data['chair_leg_raise'];
        //     $chair_shoulder_raise=$raw_data['chair_shoulder_raise'];
        //     $half_squats=$raw_data['half_squats'];
        //     $stair_climb =$raw_data['stair_climb'];

        //     $insert_exercise_data = array
        //     (

        //         "token_id"=>$token_id,
        //         "mobile_no"=>$mobile_no,
        //         "chair_squats"=>$chair_squats,
        //         "chair_leg_raise"=>$chair_leg_raise,
        //         "chair_shoulder_raise"=>$chair_shoulder_raise,
        //         "half_squats"=>$half_squats,
        //         "stair_climb"=>$stair_climb
        //     );

        //     $ID = $this->_dietitiansupport->exercise_insert($insert_exercise_data);
        //     return array('id'=>$ID,'msg'=>"inserted successfully");

        //  }

        public function return_info_from_customer(Request $request){

            $number=$request->input('mobile_no');
            $Data=DB::table('info_from_customers')->where('mobile_no',$number)->first();
            return response()->json($Data);
          }


        public function incorrect_cust_detail(Request $request)
        {
            //test commit
            $CallSid=$request->input('CallSid');
            $number=$request->input('mobile_no');
            $remarks=$request->input('remarks');
            $Data=DB::table('info_from_customers')->select('*')
            ->where('mobile_no',$number)
            ->where('CallSid',$CallSid)
            ->first();
            $insert_incorrect = array
             (
                    "CallSid"=>$CallSid,
                    "mobile_no"=>$number,
                    "token_id"=>$Data->token_id,
                    "patientName"=>$Data->patientName,
                    "tablets"=> $Data->tablets,
                    "Mg"=>$Data->Mg,
                    "times"=>$Data->times,
                    "isTablets"=>$Data->isTablets,
                    "noOfTabs"=>$Data->noOfTabs,
                    "isInsulin"=>$Data->isInsulin,
                    "noOfInsulin"=>$Data->noOfInsulin,
                    "age"=>$Data->age,
                    "gender"=>$Data->gender,
                    "city"=>$Data->city,
                    "height"=>$Data->height,
                    "weight"=>$Data->weight,
                    "profession"=>$Data->profession,
                    "Diabetes_duration"=>$Data->Diabetes_duration,
                    "FBS"=>$Data->FBS,
                    "PPBS"=>$Data->PPBS,
                    "HBA1C"=>$Data->HBA1C,
                    "RBS"=>$Data->RBS,
                    "insulin_injection"=>$Data->insulin_injection,
                    "morning_iu"=>$Data->morning_iu,
                    "afternoon_iu"=>$Data->afternoon_iu,
                    "evening_iu"=>$Data->evening_iu,
                    "knee_join_pain"=>$Data->knee_join_pain,
                    "patientComplent"=>$Data->patientComplent,
                    "date"=>$Data->date,
                    "remarks"=>$remarks

          );

                $start=$this->_dietitiansupport->incorrect($insert_incorrect);
                return array ('msg'=>"inserted successfully");
            }

             public function firstFollowup(Request $request){

                $raw_data=$request->json()->all();
                $token_id = $raw_data['token_id'];
                $mobile_no=$raw_data['mobile_no'];
                $oldMedStopped=$raw_data['oldMedStopped'];
                $JAPTreatmentStarted=$raw_data['JAPTreatmentStarted'];
                $appInstallation=$raw_data['appInstallation'];
                $nextFollowupDate=$raw_data['nextFollowupDate'];
                $treatmentInformed=$raw_data['treatmentInformed'];
                $exerciseInformed=$raw_data['exerciseInformed'];
                $followupDateInformed=$raw_data['followupDateInformed'];

                $insert_firstFollowUp = array
                (
                    "token_id"=>$token_id,
                    "mobile_no"=>$mobile_no,
                    "oldMedStopped"=>$oldMedStopped,
                    "JAPTreatmentStarted"=>$JAPTreatmentStarted,
                    "appInstallation"=>$appInstallation,
                    "nextFollowupDate"=>$nextFollowupDate,
                    "treatmentInformed"=>$treatmentInformed,
                    "exerciseInformed"=>$exerciseInformed,
                    "followupDateInformed"=>$followupDateInformed
                );

                $ID = $this->_dietitiansupport->followupdate($insert_firstFollowUp);

                return array('msg'=>"inserted successfully");
              }

             public function infoFromDoctorForm(Request $request)
             {
                 $mobile_no=$request->input('mobile_no');
                 $Data=DB::table('doctor_form as d')->select("d.reccBreakfast","d.reccLunch","d.reccSnacks",
                 "d.reccDinner","d.new_med", "e.chair_squats","e.chair_leg_raise","e.chair_shoulder_raise",
                 "e.half_squats","e.stair_climb")
                 ->join('exercise as e','e.token_id', '=' ,'d.token_id')
                 ->where('d.mobile_no',$mobile_no)
                 ->first();
                 return response()->json($Data);
             }

             public function listAssignedExotelCustomers()
             {
                $data=DB::table('customer_details_exotel1')->get();
                return response()->json($data);
             }

             public function sixthDayFollowup(Request $request){

                 $raw_data=$request->json()->all();
                 $token_id=$raw_data['token_id'];
                 $mobile_no=$raw_data['mobile_no'];
                 $name=$raw_data['name'];
                 $antiGravityExercise=$raw_data['antiGravityExercise'];
                 $exerciseRemarks=$raw_data['exerciseRemarks'];
                 $medicinesConsumed=$raw_data['medicinesConsumed'];
                 $medicinesRemarks=$raw_data['medicinesRemarks'];
                 $nextSugarTestDate=$raw_data['nextSugarTestDate'];

                 $insert_sixthDayFollowup = array (

                    'token_id'=>$token_id,
                    'mobile_no'=>$mobile_no,
                    'name'=>$name,
                    'antiGravityExercise'=>$antiGravityExercise,
                    'exerciseRemarks'=>$exerciseRemarks,
                    'medicinesConsumed'=>$medicinesConsumed,
                    'medicinesRemarks'=>$medicinesRemarks,
                    'nextSugarTestDate'=>$nextSugarTestDate

                 );

                 $Data=$this->_dietitiansupport->insertSixthDayFollowup($insert_sixthDayFollowup);
                 return array('msg'=>"inserted successfully");

             }

             public function assignPatientToCA(Request $request){

                $d_id=$request->input('d_id');
                $assignedTo=$request->input('assignedTo');
                $CallSid=$request->input('CallSid');
                $CallFrom=$request->input('CallFrom');

               $insertData=array(
                        'd_id'=>$d_id,
                        'assignedTo'=>$assignedTo,
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom
                    // 'CallSid'=>$CallSid,
                    // 'CallFrom'=>$CallFrom,
                    // 'CallTo'=> $resp_data->CallTo,
                    // 'Direction'=> $resp_data->Direction,
                    // 'Created'=> $resp_data->Created,
                    // 'DialCallDuration'=> $resp_data->DialCallDuration,
                    // 'StartTime'=> $resp_data->StartTime,
                    // 'EndTime'=> $resp_data->EndTime,
                    // 'CallType'=> $resp_data->CallType,
                    // 'DialWhomNumber'=> $resp_data->DialWhomNumber,
                    // 'From'=> $resp_data->From,
                    // 'To'=> $resp_data->To,
                    // 'CurrentTime'=> $resp_data->CurrentTime,
                    // 'ForwardedFrom'=> $resp_data->ForwardedFrom,
                    // 'RecordingUrl'=> $resp_data->RecordingUrl,
                    // 'DialCallStatus'=> $resp_data->DialCallStatus,
                    // 'ProcessStatus'=> $resp_data->ProcessStatus,
                    // 'flow_id'=> $resp_data->flow_id,
                    // 'tenant_id'=> $resp_data->tenant_id,
                    // 'Legs'=> $resp_data->Legs,
                    // 'digits'=> $resp_data->digits,
                    // 'CustomField'=> $resp_data->CustomField,
                    // 'OutgoingPhoneNumber'=> $resp_data->OutgoingPhoneNumber
                );

                 $item = DB::table('customer_details_exotel1')
                 ->where('CallSid', $CallSid)
                 ->where('CallFrom',$CallFrom)
                 ->first();

                 if($item){
                    $insertdata=DB::table('exotel_assign_history')->insert($insertData);
                    $remove = DB::table('customer_details_exotel1')
                 ->where('CallSid', $CallSid)
                 ->where('CallFrom',$CallFrom)
                 ->delete();
                 $result  = array('msg'=>"Assigned Successfully");
                 return $result;
                 }
               else{
                $result  = array('msg'=>"failed to add");
                return $result;
                }

               }

               public function offerRejectedFromCA(Request $request)
               {
                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $remarks=$request->input('remarks');

                    $data=DB::table('customer_details_exotel')
                    ->where('CallSid',$CallSid)
                    ->where('CallFrom',$CallFrom)
                    ->first();


                     $insertData = array (
                            'd_id'=>$d_id,
                            'assignedTo'=>$assignedTo,
                            'CallSid'=>$CallSid,
                            'CallFrom'=>$CallFrom,
                            'remarks'=>$remarks
                    );

                    if($data)
                    {
                        $insert=DB::table('offerRejectedListFromCA')->insert($insertData);

                        $removeData=DB::table('exotel_assign_history')
                        ->where('CallSid',$CallSid)
                        ->where('CallFrom',$CallFrom)
                        ->delete();

                        $removeData1=DB::table('customer_details_exotel')
                        ->where('CallFrom',$CallFrom)
                        ->delete();

                    if($insert && $removeData && $removeData1)
                    {
                        $result  = array('msg'=>"Assigned to rejected list");
                        return $result;
                    }
                    else{
                        $result  = array('msg'=>"failed to add");
                        return $result;
                    }

                    }
                    else{
                        $result  = array('msg'=>"failed to add");
                        return $result;
                    }


               }

               public function offerRejectedFromDoctor(Request $request)
               {
                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $registration_number=$request->input('registration_number');
                    $remarks=$request->input('remarks');


                    $data=DB::table('info_from_customers')
                     ->where('CallSid',$CallSid)
                    ->where('mobile_no',$CallFrom)
                    ->first();

                     $insertData = array (
                            'd_id'=>$d_id,
                            'registration_number'=>$registration_number,
                            'assignedTo'=>$assignedTo,
                            'CallSid'=>$CallSid,
                            'CallFrom'=>$CallFrom,
                            'remarks'=>$remarks
                    );

                    if($data)
                    {

                        $insert=DB::table('offerRejectedListFromDoctor')->insert($insertData);
                        $removeData=DB::table('assign_history')
                        ->where('registration_number',$registration_number)->where('d_id',$d_id)
                        ->delete();

                        // $removeData1=DB::table('info_from_customers')
                        // ->where('CallSid',$CallSid)
                        // ->where('mobile_no',$CallFrom)
                        // ->delete();


                        if($insert && $removeData)
                        {
                            $result  = array('msg'=>"Assigned to rejected list");
                            return $result;
                        }
                        else{
                            $result  = array('msg'=>"failed to add");
                            return $result;
                        }

                        }


               }

               public function callLaterCA(Request $request){

                $d_id=$request->input('d_id');
                $assignedTo=$request->input('assignedTo');
                $CallSid=$request->input('CallSid');
                $CallFrom=$request->input('CallFrom');
                $type=$request->input('type');
                $CallAt=$request->input('CallAt');


               $resp_data= DB::table('customer_details_exotel')
               ->where('CallFrom',$CallFrom)
               ->where('CallSid',$CallSid)->first();

               $now= array (
                'd_id'=>$d_id,
                'assignedTo'=>$assignedTo,
                'CallSid'=>$CallSid,
                'CallFrom'=>$CallFrom,
                'type'=>$type,
                'CallAt'=>$CallAt
               );

               $insertData = array (
                         'CallSid'=>$CallSid,
                         'CallFrom'=>$CallFrom,
                         'CallTo'=> $resp_data->CallTo,
                         'Direction'=> $resp_data->Direction,
                         'Created'=> $resp_data->Created,
                         'DialCallDuration'=> $resp_data->DialCallDuration,
                         'StartTime'=> $resp_data->StartTime,
                         'EndTime'=> $resp_data->EndTime,
                         'CallType'=> $resp_data->CallType,
                         'DialWhomNumber'=> $resp_data->DialWhomNumber,
                         'From'=> $resp_data->From,
                         'To'=> $resp_data->To,
                         'CurrentTime'=> $resp_data->CurrentTime,
                         'ForwardedFrom'=> $resp_data->ForwardedFrom,
                         'RecordingUrl'=> $resp_data->RecordingUrl,
                         'DialCallStatus'=> $resp_data->DialCallStatus,
                         'ProcessStatus'=> $resp_data->ProcessStatus,
                         'flow_id'=> $resp_data->flow_id,
                         'tenant_id'=> $resp_data->tenant_id,
                         'Legs'=> $resp_data->Legs,
                         'digits'=> $resp_data->digits,
                         'CustomField'=> $resp_data->CustomField,
                         'OutgoingPhoneNumber'=> $resp_data->OutgoingPhoneNumber,
                         'CallAt'=>$CallAt
                     );
                     if($resp_data)
                     {
                         $insert=DB::table('customer_details_exotel1')->insert($insertData);
                         $insert1=DB::table('call_later_from_ca')->insert($now);
                         $removeData=DB::table('exotel_assign_history')
                          ->where('CallSid',$CallSid)
                          ->where('CallFrom',$CallFrom)
                          ->delete();

                         if($insert && $removeData && $insert1)
                         {
                             $result  = array('msg'=>"Assigned to call later list");
                             return $result;
                         }
                         else{
                             $result  = array('msg'=>"failed to add");
                             return $result;
                         }

                    }
                }

                public function callLaterDoctor(Request $request){

                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $registration_number=$request->input('registration_number');
                    $type=$request->input('type');
                    $CallAt=$request->input('CallAt');

                    $data= DB::table('info_from_customers')
                    ->where('CallSid', $CallSid)
                    ->where('mobile_no',$CallFrom)
                    ->first();

                  //  return response()->json($data);
                   $now= array (
                        'd_id'=>$d_id,
                        'assignedTo'=>$assignedTo,
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'registration_number'=>$registration_number,
                        'type'=>$type,
                        'CallAt'=>$CallAt
                  );

                    $insertData = array (

                            'CallSid'=>$CallSid,
                            'token_id'=> $data->token_id,
                            'patientName' => $data->patientName,
                            'mobile_no'=>$data->mobile_no,
                            'isTablets'=> $data->isTablets,
                            'noOfTabs' => $data->noOfTabs,
                            'isInsulin' => $data->isInsulin,
                            'noOfInsulin'=> $data->noOfInsulin,
                            'tablets'=>$data->tablets,
                            'Mg'=>$data->Mg,
                            'times'=>$data->times,
                            'age'=>$data->age,
                            'gender'=>$data->gender,
                            'city'=>$data->city,
                            'height'=>$data->height,
                            'weight'=>$data->weight,
                            'profession'=>$data->profession,
                            'Diabetes_duration'=> $data->Diabetes_duration,
                            'FBS'=>$data->FBS,
                            'PPBS'=>$data->PPBS,
                            'HBA1C'=>$data->HBA1C,
                            'RBS'=>$data->RBS,
                            'insulin_injection'=>$data->insulin_injection,
                            'morning_iu'=>$data->morning_iu,
                            'afternoon_iu'=>$data->afternoon_iu,
                            'evening_iu'=>$data->evening_iu,
                            'thyroid' => $data->thyroid,
                            'bp'=>$data->bp,
                            'burning_foot'=>$data->burning_foot,
                            'Prescription_image'=>$data->Prescription_image,
                            'Blood_test_image'=>$data->Blood_test_image,
                            'patientComplent'=>$data->patientComplent,
                            'token_status'=>$data->token_status,
                            'isAttended'=>$data->isAttended,
                            'date'=>$data->date,
                            'status'=>$data->status,
                            'stage'=>$data->stage,
                            'CallAt'=>$CallAt
                    );

                    if($data)
                    {
                        $insert=DB::table('info_from_customers1')->insert($insertData);
                        $insert1=DB::table('call_later_from_doctor')->insert($now);
                        $removeData=DB::table('assign_history')
                         ->where('CallSid',$CallSid)
                         ->where('CallFrom',$CallFrom)
                         ->delete();

                        if($insert && $removeData && $insert1)
                        {
                            $result  = array('msg'=>"Assigned to call later list");
                            return $result;
                        }
                        else{
                            $result  = array('msg'=>"failed to add");
                            return $result;
                        }

                   }

                  }

                  public function getDetailsFromDoctorForm(Request $request){
                        $CallSid=$request->input('CallSid');
                        $mobile_no=$request->input('mobile_no');

                        $data=DB::table('doctor_form')->where('CallSid',$CallSid)
                        ->where('mobile_no',$mobile_no)->first();

                       if($data){

                            $result = array('details'=>$data);
                            return $result;
                        }
                        else{

                            $result = array('msg'=>"Failed to fetch data");
                            return $result;
                        }
                  }

                  public function getSubmittedPatientDetails()
                  {


                    $list=DB::table('submitted_patient_details')->get();
                     // return response()->json($Data);

                     $dataset=array();
                      foreach ($list as $element){

                        $Data=DB::table('doctor_form as d')->select("i.*","d.CallSid","d.id","d.stop_med","d.continue_med","d.new_med","d.reccBreakfast","d.reccLunch","d.reccSnacks",
                        "d.reccDinner","d.remarks","e.chair_squats","e.chair_leg_raise","e.chair_shoulder_raise","e.half_squats","e.stair_climb")
                        ->join('exercise as e','e.mobile_no', '=' ,'d.mobile_no')
                        ->join('info_from_customers as i','i.mobile_no','=','d.mobile_no')
                        ->orderby('d.id')
                        ->where('d.CallSid',$element->CallSid)
                        ->get();

                        if(!empty($Data))
                        {
                            array_push($dataset,$Data);
                        }
                        else{
                            continue;
                        }

                      }

                      return $dataset;

                      }


                //   public function  ustatus(Request $request){

                //     $mobile_no=$request->mobile_no;


                //     $resp_data= DB::table('info_from_customers')
                //     ->where('mobile_no', $mobile_no)
                //     ->update(['CallSid' => '999']);

                //     if($resp_data)
                //     {
                //         return "updated successfully";
                //     }
                //     else {
                //         return "Failed to update";
                //     }

                //   }
                // public function removedata(Request $request){

                //     $mobile_no=$request->input('mobile_no');

                //     $str = $mobile_no ;
                //     $str2 = substr($str,2);
                //     return $str2;

                //   }

        public function physicalConsultation(Request $request){

                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $registration_number=$request->input('registration_number');
                    $date=$request->input('date');
                    $time=$request->input('time');

                    $data=DB::table('info_from_customers')
                     ->where('CallSid',$CallSid)
                     ->where('mobile_no',$CallFrom)
                     ->first();
                    //echo $data;
                     $insertData = array (
                            'd_id'=>$d_id,
                            'registration_number'=>$registration_number,
                            'assignedTo'=>$assignedTo,
                            'CallSid'=>$CallSid,
                            'CallFrom'=>$CallFrom,
                            'date'=>$date,
                            'time'=>$time
                            );

                     if($data){

                        $insert=DB::table('physical_consultation')->insert($insertData);
                        $removeData=DB::table('assign_history')
                        ->where('registration_number',$registration_number)->where('d_id',$d_id)
                        ->delete();
                    if($insert && $removeData)
                    {
                        $result  = array('msg'=>"Assigned to physical consultation list");
                        return $result;
                    }
                    else{
                        $result  = array('msg'=>"failed to add");
                        return $result;
                    }
                }

                 }

                 public function empSubmittedRecordCA(Request $request){

                    $d_id=$request->input('d_id');
                    $mobile_no=$request->input('mobile_no');
                    $date=$request->input('date');

                    $data=DB::table("offerRejectedListFromCA")
                     ->where('d_id',$d_id)->count();

                    $data1=DB::table("exotel_assign_history")
                     ->where('d_id',$d_id)->count();

                    $data2=DB::table("offerRejectedListFromCA")
                     ->where('d_id',$d_id)
                     ->where('createdAt','>',$date)->count();

                    $data3=DB::table("exotel_assign_history")
                     ->where('d_id',$d_id)
                     ->where('createdAt','>',$date)->count();

                    if($data || $data1 || $data2 || $data3)
                    {
                        $result  = array('Submitted_count'=> $data1,'Rejected_Count'=>$data,'Submitted_count1'=> $data3,'Rejected_Count1'=>$data2);
                        return $result;
                    }
                    else{

                        $result  = array('msg'=>"No Match");
                        return $result;
                    }

                 }

                 public function empSubmittedRecordDoctor(Request $request){

                    $d_id=$request->input('d_id');
                    $mobile_no=$request->input('mobile_no');
                    $date=$request->input('date');

                    $data=DB::table("offerRejectedListFromDoctor")
                     ->where('d_id',$d_id)->count();


                    $data1=DB::table("assign_history")
                     ->where('d_id',$d_id)->count();

                     $data2=DB::table("offerRejectedListFromDoctor")
                     ->where('d_id',$d_id)
                     ->where('createdAt','>',$date)->count();

                    $data3=DB::table("assign_history")
                     ->where('d_id',$d_id)
                     ->where('createdAt','>',$date)->count();

                    if($data || $data1 || $data2 || $data3)
                    {
                        $result  = array('Submitted_count'=> $data1,'Rejected_Count'=>$data,'Submitted_count1'=> $data3,'Rejected_Count1'=>$data2);
                        return $result;
                    }
                    else{

                        $result  = array('msg'=>"NO MATCH");
                        return $result;
                    }
                }

                    public function monthlyEarnings(Request $request){

                        $raw_data=$request->json()->all();
                        $d_id=$raw_data['d_id'];
                        $role=$raw_data['role'];
                        $name=$raw_data['name'];
                        $month=$raw_data['month'];
                        $year=$raw_data['year'];
                        $submitted=$raw_data['submitted'];
                        $rejected=$raw_data['rejected'];
                        $money_earned=$raw_data['money_earned'];

                        $insertdata = array (

                            'd_id'=> $d_id,
                            'role'=> $role,
                            'name'=>$name,
                            'month'=>$month,
                            'year'=>$year,
                            'submitted'=> $submitted,
                            'rejected'=>$rejected,
                            'money_earned'=>$money_earned
                     );

                     $data=DB::table("monthly_earnings")->insert($insertdata);

                     if($data){

                        $result = array ('msg'=>'inserted sucessfully');
                        return $result;
                     }
                     else {
                        $result = array ('msg'=>'insertion failed');
                        return $result;

                     }
                 }

                 public function trialKitDeliveryList(Request $request){

                    $raw_data=$request->json()->all();
                    $CallSid=$raw_data['CallSid'];
                    $CallFrom=$raw_data['CallFrom'];
                    $date_of_delivery=$raw_data['date_of_delivery'];


                    $insertdata = array (
                        'CallSid'=> $CallSid,
                        'CallFrom'=> $CallFrom,
                        'date_of_delivery'=>$date_of_delivery,

                 );

                 $data=DB::table("trial_kit_delivered_list")->insert($insertdata);
                 $remove=DB::table("submitted_patient_details")->where('CallSid',$CallSid)
                 ->where('CallFrom',$CallFrom)
                 ->delete();
                // return $remove;

                 if($data && $remove){

                    $result = array ('msg'=>'inserted sucessfully');
                    return $result;
                 }
                 else {
                    $result = array ('msg'=>'insertion failed');
                    return $result;

                 }
             }


             public function firstFollowupDietitianList(){

                $data=DB::table('trial_kit_delivered_list')->get();
                //return $data;
                // echo $data->CallSid;
                $data_insert=null;
                $data_insert1=null;
                $today=now()->format('Y-m-d');
                $date1 = Carbon::createFromFormat('Y-m-d', $today);
                 $daysToAdd = 1;
                 $date = $date1->addDays($daysToAdd);
                $x=explode(" ",$date1);
                $createdDate=$today;
                $expiryDate=$x[0];
                // echo $createdDate;
                // echo $expiryDate;

                foreach($data as $oldDate){

                    $date1 = Carbon::createFromFormat('Y-m-d', $oldDate->date_of_delivery);
                    // $daysToAdd = 1;
                    // $date = $date1->addDays($daysToAdd);
                    $x=explode(" ",$date1);
                    // $today=now()->format('Y-m-d');

                    if($x[0] == $today)
                     {
                        //echo "entered\n";
                         $dataArr = array(
                            'CallSid'=>$oldDate->CallSid,
                            'CallFrom'=>$oldDate->CallFrom,
                            'created_date'=>$createdDate,
                            'expiry_date'=>$expiryDate
                            );
                            $exist=DB::table('list_from_dietitian_one')->where('CallSid',$oldDate->CallSid)->exists();
                            if ($exist) {
                                //echo "entered1\n";
                                continue;
                             }
                             else {
                                //echo "entered\n";
                                $data_insert=DB::table('list_from_dietitian_one')->insert($dataArr);
                                $data_insert1=DB::table('list_from_dietitian_one_copy')->insert($dataArr);
                             }

                       }

                    }
                    // echo $data_insert."\n";
                    // echo $data_insert1."\n";
                        if($data_insert!=null && $data_insert1!=null){

                            $result = array ('msg'=>'inserted sucessfully');
                            return $result;
                        }
                        else {
                            $result = array ('msg'=>'insertion failed');
                            return $result;

                            }

                 }

                 public function firstFollowupList(Request $request){

                    $Data=DB::table('list_from_dietitian_one_copy')->get();

                    $Dataarr=array();
                    foreach($Data as $data){

                        $today=now()->format('Y-m-d');

                       // $result = array ('data'=>$data);

                       $arrdata = array(
                        'id'=>$data->id,
                        'CallSid'=>$data->CallSid,
                        'CallFrom'=>$data->CallFrom,
                        'date'=> $today,
                        'CallAt'=>$data->CallAt
                       );

                        array_push($Dataarr,$arrdata);
                      }

                        $resp = array('Data'=>$Dataarr);
                        return $resp;

              }

                 public function assignPatientToDietitian1(Request $request){

                    $d_id=$request->d_id;
                    $registration_number=$request->registration_number;
                    $assignedTo=$request->assignedTo;
                    $patientId=$request->patientId;
                    $CallSid=$request->CallSid;
                    $CallFrom=$request->CallFrom;


                    $data = array(
                        'd_id'=>$d_id,
                        'registration_number'=>$registration_number,
                        'assignedTo'=>$assignedTo,
                        'patientId'=>$patientId,
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom
                    );


                  $insert= DB::table('dietitian_one_assign_history')->insert($data);

                  $remove=DB::table('list_from_dietitian_one_copy')->where('CallSid', $CallSid)->delete();

                  if($insert && $remove){
                         $resp = array('msg'=>'Assigned Successfully');
                         return $resp;
                  }
                  else {
                    $resp = array('msg'=>'failed to assign');
                    return $resp;
                  }
                }

                public function callLaterDietitian1(Request $request){

                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $registration_number=$request->input('registration_number');
                    $type=$request->input('type');
                    $CallAt=$request->input('CallAt');

                    $data= DB::table('list_from_dietitian_one')
                    ->where('CallSid', $CallSid)
                    ->where('CallFrom',$CallFrom)
                    ->first();


                   $now= array (
                        'd_id'=>$d_id,
                        'assignedTo'=>$assignedTo,
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'registration_number'=>$registration_number,
                        'type'=>$type,
                        'CallAt'=>$CallAt
                  );


                    $dataArr = array(
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'CallAt'=>$CallAt
                        );


                    if($data)
                    {
                        $insert=DB::table('list_from_dietitian_one_copy')->insert($dataArr);
                        $insert1=DB::table('call_later_dietitian1')->insert($now);
                        $removeData=DB::table('dietitian_one_assign_history')
                         ->where('CallSid',$CallSid)
                         ->where('CallFrom',$CallFrom)
                         ->delete();

                        if($insert && $insert1 && $removeData)
                        {
                            $result  = array('msg'=>"Assigned to call later list");
                            return $result;
                        }
                        else{
                            $result  = array('msg'=>"failed to add");
                            return $result;
                        }

                   }
                }

                public function patientDetailsForDietitian1(Request $request){

                    $CallSid=$request->input('CallSid');
                   // $CallFrom=$request->input('mobile_no');

                    $data=DB::table('doctor_form as d')->select("d.*","e.chair_squats","e.chair_leg_raise","e.chair_shoulder_raise","e.half_squats","e.stair_climb")
                    ->join('exercise as e','e.mobile_no', '=' ,'d.mobile_no')
                    ->where('d.CallSid',$CallSid)
                    //->where('d.mobile_no',$CallFrom)
                    ->first();
                    $result = array('data'=> $data);
                    return $result;

                }

                public function patientRemarksForDietitian1(Request $request){

                    $CallSid=$request->input('CallSid');

                     $Data=DB::table('doctor_form as d')->select("i.patientComplent","d.remarks")
                    ->join('info_from_customers as i','i.mobile_no','=','d.mobile_no')
                    ->where('d.CallSid',$CallSid)
                    ->first();

                    $result = array('data'=> $Data);
                    return $result;

                }

                public function info_from_dietitian1(Request $request){

                    $raw_data=$request->json()->all();
                    $CallSid=$raw_data['CallSid'];
                    $CallFrom=$raw_data['CallFrom'];
                    $oldMedStopped=$raw_data['oldMedStopped'];
                    $JAPTreatmentStarted=$raw_data['JAPTreatmentStarted'];
                    $reccTreatment=$raw_data['reccTreatment'];
                    $reccFood=$raw_data['reccFood'];
                    $reccExcercise=$raw_data['reccExcercise'];
                    $appInstalled=$raw_data['appInstalled'];
                    $PPBSNextDate=$raw_data['PPBSNextDate'];
                    $audioSendTo=$raw_data['audioSendTo'];
                    $nextDateInformed=$raw_data['nextDateInformed'];
                    $remarks=$raw_data['remarks'];
                    $followupNumber=$raw_data['followupNumber'];
                    $attended=$raw_data['attended'];
                    $followupDate=$raw_data['followupDate'];
                    $followupExpiryDate=$raw_data['followupExpiryDate'];
                    $next_followup_number=$raw_data['next_followup_number'];
                    $next_followup_date=$raw_data['next_followup_date'];
                    $next_followup_expiryDate=$raw_data['next_followup_expiryDate'];


                    $insertData= array(

                       'CallSid'=>$CallSid,
                       'CallFrom'=>$CallFrom,
                       'oldMedStopped'=>$oldMedStopped,
                       'JAPTreatmentStarted'=>$JAPTreatmentStarted,
                       'reccTreatment'=>$reccTreatment,
                       'reccFood'=>$reccFood,
                       'reccExcercise'=>$reccExcercise,
                       'appInstalled'=>$appInstalled,
                       'PPBSNextDate'=>$PPBSNextDate,
                       'audioSendTo'=>$audioSendTo,
                       'nextDateInformed'=>$nextDateInformed,
                       'remarks'=>$remarks,
                       'followupNumber'=>$followupNumber,
                       'attended'=>$attended,
                       'followupDate'=>$followupDate,
                       'followupExpiryDate'=>$followupExpiryDate

                     );

                    //  $dataarr1= array(
                    //     'CallSid'=>$CallSid,
                    //     'CallFrom'=> $CallFrom,
                    //     'dietitian2_followup_number'=>$followupNumber,
                    //     'dietitian2_followup_date'=>$followupDate,
                    //     'dietitian2_followup_expiryDate'=>$followupExpiryDate,
                    //     'doctor_followup_number'=>$doctor_followup_number,
                    //     'doctor_followup_date'=>$doctor_followup_date,
                    //     'doctor_followup_expiryDate'=>$doctor_followup_expiryDate
                    // );

                    $dataarr1= array(
                        'CallSid'=>$CallSid,
                        'CallFrom'=> $CallFrom,
                        'followupNum'=>$next_followup_number,
                        'followupDate'=>$next_followup_date,
                        'followupExpiry'=>$next_followup_expiryDate,
                        //'attended'=>$attended
                    );




                   $now=DB::table('info_from_dietitian1')->insert($insertData);
                   $now1=DB::table('info_from_dietitian1_copy')->insert($insertData);
                    // $insertdata2=DB::table('common_tbl_for_dietitian1_doctor')->insert($dataarr1);
                    // $insertdata3=DB::table('common_tbl_for_dietitian1_doctor_copy')->insert($dataarr1);

                    $insertdata2=DB::table('list_for_dietitian2')->insert($dataarr1);
                    $insertdata3=DB::table('list_for_dietitian2_copy')->insert($dataarr1);

                        if($now && $now1 && $insertdata2 && $insertdata3){
                            $resp = array('msg'=>'inserted Successfully');
                            return $resp;
                        }
                        else {
                            $resp = array('msg'=>'failed to insert');
                            return $resp;
                        }

                }

                public function removeExpiredElements(){

                    $today=now()->format('Y-m-d');

                    $data=DB::table('list_from_dietitian_one_copy')->select('CallSid','CallFrom')
                    ->where('expiry_date','<=',$today)
                    ->get();
                  //  echo $data;
                     $insert=null;
                     $remove=null;
                     $insertdata2=null;
                     $insert2=null;

                     $yesterday=Carbon::createFromFormat('Y-m-d', $today)->subDays(1);
                     $z=explode(" ",$yesterday);

                     $date = Carbon::createFromFormat('Y-m-d', $today)->addDays(2);
                     $x=explode(" ",$date);


                     $date1 = Carbon::createFromFormat('Y-m-d', $today)->addDays(3);
                     $y=explode(" ",$date1);



                    foreach($data as $data){


                    $dataarr=array(
                        'CallSid'=>$data->CallSid,
                        'CallFrom'=>$data->CallFrom,
                        'oldMedStopped'=>'empty',
                        'JAPTreatmentStarted'=>'empty',
                        'reccTreatment'=>'empty',
                        'reccFood'=>'empty',
                        'reccExcercise'=>'empty',
                        'appInstalled'=>'empty',
                        'PPBSNextDate'=>'empty',
                        'audioSendTo'=>'empty',
                        'nextDateInformed'=>'empty',
                        'remarks'=>'empty',
                        'followupNumber'=>1,
                        'attended'=>'no',
                        'followupDate'=>$z[0],
                        'followupExpiryDate'=>$today
                    );

                    $dat=array(
                        "CallSid"=>$data->CallSid,
                        "CallFrom"=>$data->CallFrom,
                        "dietitian_followup_count"=>1,
                        "doctor_followup_count"=>0
                    );

                    $dataarr1= array(
                        'CallSid'=>$data->CallSid,
                        'CallFrom'=> $data->CallFrom,
                        'followupNum'=>2,
                        'followupDate'=>$x[0],
                        'followupExpiry'=>$y[0],
                        //'attended'=>$attended
                    );



                    $insert=DB::table('info_from_dietitian1')->insert($dataarr);
                    $insert1=DB::table('info_from_dietitian1_copy')->insert($dataarr);
                    $insert2=DB::table('followup_count')->insert($dat);

                    $insertdata2=DB::table('list_for_dietitian2')->insert($dataarr1);
                    $insertdata3=DB::table('list_for_dietitian2_copy')->insert($dataarr1);
                    $remove=DB::table('list_from_dietitian_one_copy')->where( 'CallSid',$data->CallSid)->delete();
                }


                    if($insert && $insert1 && $remove && $insertdata2 && $insert2){
                        $resp = array('msg'=>'Pushed expired elements to Submitted list');
                        return $resp;
                    }
                    else {
                        $resp = array('msg'=>'No expired elements');
                        return $resp;
                    }


                }

                public function assignPatientToDietitian2(Request $request){

                    $d_id=$request->d_id;
                    $registration_number=$request->registration_number;
                    $assignedTo=$request->assignedTo;
                    $patientId=$request->patientId;
                    $CallSid=$request->CallSid;
                    $CallFrom=$request->CallFrom;

                    $remove=null;
                    $remove1=null;

                    $followupNum= DB::table('followup_count')->where('CallSid',$CallSid)->first();
                    //echo $followupNum->dietitian_followup_count."\n";

                    $data = array(
                        'd_id'=>$d_id,
                        'registration_number'=>$registration_number,
                        'assignedTo'=>$assignedTo,
                        'patientId'=>$patientId,
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'followupNum'=>$followupNum->dietitian_followup_count
                    );



                  //echo $followupNum->dietitian_followup_count."yo\n";
                  $exist=DB::table('info_from_followup_doctor_copy')->where('CallSid', $CallSid)->exists();
                  //echo $exist."yes\n";
                  if(!$exist)
                  {
                    //echo "entered\n";
                    DB::beginTransaction();
                    $insert= DB::table('dietitian2_assign_history')->insert($data);
                    $remove=DB::table('info_from_dietitian1_copy')->where('CallSid', $CallSid)->where('followupNumber', $followupNum->dietitian_followup_count)->delete();

                    $remove1=DB::table('list_for_dietitian2_copy')->where('CallSid', $CallSid)->where('followupNum', $followupNum->dietitian_followup_count+1)->delete();
                    DB::commit();
                    // echo $remove."r\n";
                    // echo $remove1."r1\n";
                }
                  else{
                   // echo "entered1\n";
                    DB::beginTransaction();
                    $insert= DB::table('dietitian2_assign_history')->insert($data);
                    $remove=DB::table('info_from_followup_doctor_copy')->where('CallSid', $CallSid)->where('followupNumber', $followupNum->doctor_followup_count)->delete();

                    $remove1=DB::table('list_for_dietitian2_copy')->where('CallSid', $CallSid)->where('followupNum', $followupNum->dietitian_followup_count+1)->delete();
                    DB::commit();
                    // echo $remove."r\n";
                    // echo $remove1."r1\n";
                }

                  //echo $remove."\n";
                 // echo $remove1."\n";
                  if($insert && $remove && $remove1){
                         $resp = array('msg'=>'Assigned Successfully');
                         return $resp;
                  }
                  else {
                    $resp = array('msg'=>'failed to assign');
                    return $resp;
                  }
                }

                public function followupDietitianList(){


                    //$Data=DB::table('info_from_dietitian1_copy')->get();
                    //$Data1=DB::table('common_tbl_for_dietitian1_doctor')->get();

                    $Data1=DB::table('list_for_dietitian2_copy')->get();
                   // echo  $Data1;

                    $Dataarr=array();
                   // $Dataarr1=array();


                    // foreach($Data1 as $data){


                    //     $today=now()->format('Y-m-d');

                    //     if($today == $data->followupDate){
                    //             array_push($Dataarr,$data);

                    //           }


                    //     }



                        foreach($Data1 as $data1){

                            $today=now()->format('Y-m-d');
                            $date2 = Carbon::createFromFormat('Y-m-d', $today);
                            $daysToAdd = 1;
                            $date1 = $date2->addDays($daysToAdd);
                            $y=explode(" ",$date1);

                            if($today >= $data1->followupExpiry){
                                $dataarr=array (
                                    'CallSid'=>$data1->CallSid,
                                    'CallFrom'=> $data1->CallFrom,
                                    'follows_food_recc'=>'empty',
                                    'follows_excercise_recc'=>'empty',
                                    'med_consumed'=>'empty',
                                    'complaint'=>'empty',
                                    'sugar_test_informed'=>'empty',
                                    'remarks'=> 'empty',
                                    'followupNumber'=>$data1->followupNum,
                                    'attended'=>'no',
                                    'followupDate'=>$data1->followupDate,
                                    'followupExpiryDate'=>$data1->followupExpiry,
                                    'subscribe'=>'empty'

                                );
                                $followNum=DB::table('followup_count')->where('CallSid',$data1->CallSid)->first();
                                $dataarr1= array(
                                    'CallSid'=>$data1->CallSid,
                                    'CallFrom'=> $data1->CallFrom,
                                    'followupNum'=>$followNum->doctor_followup_count+1,
                                    'followupDate'=>$today,
                                    'followupExpiry'=>$y[0],

                                );
                                $dataAssign = array(
                                    'd_id'=>"empty",
                                    'registration_number'=>"empty",
                                    'assignedTo'=>"empty",
                                    'patientId'=>"empty",
                                    'CallSid'=>$data1->CallSid,
                                    'CallFrom'=>$data1->CallFrom,
                                    'followupNum'=>$data1->followupNum
                                );

                                DB::table('followup_count')->where('CallSid',$data1->CallSid)->update(['dietitian_followup_count'=>$followNum->dietitian_followup_count+1]);


                                $insertdata=DB::table('info_from_dietitian2')->insert( $dataarr);
                                $insertdata1=DB::table('info_from_dietitian2_copy')->insert($dataarr);

                                $insertdata2=DB::table('list_for_followupDoctor')->insert($dataarr1);
                                $insertdata3=DB::table('list_for_followupDoctor_copy')->insert($dataarr1);

                                $insert= DB::table('dietitian2_assign_history')->insert($dataAssign);
                                $ex=DB::table('info_from_dietitian1_copy')->where('CallSid', $data1->CallSid)->where('followupNumber', $data1->followupNum)->exists();
                                if($ex)
                                {
                                    $remove=DB::table('info_from_dietitian1_copy')->where('CallSid', $data1->CallSid)->where('followupNumber', $data1->followupNum)->delete();
                                }
                                else{
                                    $remove=DB::table('info_from_followup_doctor_copy')->where('CallSid', $data1->CallSid)->where('followupNumber', $followNum->doctor_followup_count)->delete();
                                }

                                $remove=DB::table('info_from_dietitian1_copy')->where('CallSid', $data1->CallSid)->where('followupNumber', $data1->followupNum)->delete();
                                $remove1=DB::table('list_for_dietitian2_copy')->where('CallSid', $data1->CallSid)->where('followupNum', $data1->followupNum)->delete();
                              }
                            else if($today == $data1->followupDate){
                                    array_push($Dataarr,$data1);
                                  }

                             }

                              $resp = array('Data'=>$Dataarr);
                              return $resp;

                    }
                    // $result = array('data'=>$Data);
                    // return $result;
                         // $time = strtotime('6 june 2003');

                    //  $newformat = date('Y-m-d',$time);

                    //   echo $newformat;



                public function callLaterDietitian2(Request $request){

                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $registration_number=$request->input('registration_number');
                    $type=$request->input('type');
                    $CallAt=$request->input('CallAt');
                    $followupNum=$request->input('followupNum');
                    $followupNum=$followupNum-1;
                    //echo $followupNum."1\n";
                    $insert2=null;

                    if($followupNum==1)
                    {
                        $data= DB::table('info_from_dietitian1')
                        ->where('CallSid', $CallSid)
                        ->where('CallFrom',$CallFrom)->first();

                        $dataArr1 = array(
                            'CallSid'=>$CallSid,
                            'CallFrom'=>$CallFrom,
                            'oldMedStopped'=>$data->oldMedStopped,
                            'JAPTreatmentStarted'=>$data->JAPTreatmentStarted,
                            'reccTreatment'=>$data->reccTreatment,
                            'reccFood'=>$data->reccFood,
                            'reccExcercise'=>$data->reccExcercise,
                            'appInstalled'=>$data->appInstalled,
                            'PPBSNextDate'=>$data->PPBSNextDate,
                            'audioSendTo'=>$data->audioSendTo,
                            'nextDateInformed'=>$data->nextDateInformed,
                            'remarks'=>$data->remarks,
                            'followupNumber'=>$data->followupNumber,
                            'attended'=>$data->attended,
                            'followupDate'=>$data->followupDate,
                            'followupExpiryDate'=>$data->followupExpiryDate,
                            'CallAt'=>$CallAt
                            );

                            $insert2=DB::table('info_from_dietitian1_copy')->insert($dataArr1);
                    }
                    else
                    {
                        $count= DB::table('followup_count')->where('CallSid', $CallSid)->first();
                        $data= DB::table('info_from_followup_doctor')
                        ->where('CallSid', $CallSid)
                        ->where('CallFrom',$CallFrom)->where('followupNumber',$count->doctor_followup_count)
                        ->first();


                        $dataarr = array (
                            'CallSid'=>$CallSid,
                            'CallFrom'=> $CallFrom,
                            'reccTreatment'=>$data->reccTreatment,
                            'reccFood'=>$data->reccFood,
                            'bp'=>$data->bp,
                            'thyroid'=>$data->thyroid,
                            'burningFoot'=>$data->burningFoot,
                            'complaint'=> $data->complaint,
                            'remarks'=>$data->remarks,
                            'attended'=>$data->attended,
                            'followupNumber'=>$data->followupNumber,
                            'followupDate'=>$data->followupDate,
                            'followupExpiryDate'=>$data->followupExpiryDate,
                       );

                        $insert2=DB::table('info_from_followup_doctor_copy')->insert($dataarr);
                    }


                    $data1= DB::table('list_for_dietitian2')
                    ->where('CallSid', $CallSid)
                    ->where('followupNum',$followupNum+1)
                    ->first();
                    //echo $data1->CallSid;

                   $now= array (
                        'd_id'=>$d_id,
                        'assignedTo'=>$assignedTo,
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'registration_number'=>$registration_number,
                        'type'=>$type,
                        'CallAt'=>$CallAt,
                        'followupNum'=>$followupNum
                  );




                    $dataArr = array(
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'followupNum'=>$data1->followupNum,
                        'followupDate'=>$data1->followupDate,
                        'followupExpiry'=>$data1->followupExpiry,
                        'CallAt'=>$CallAt,
                        );

                        $insert=DB::table('list_for_dietitian2_copy')->insert($dataArr);
                        $insert1=DB::table('call_later_dietitian2')->insert($now);
                        $removeData=DB::table('dietitian2_assign_history')
                         ->where('CallSid',$CallSid)
                         ->where('followupNum',$followupNum)
                         ->delete();

                        if($insert && $insert1 && $removeData && $insert2)
                        {
                            $result  = array('msg'=>"Assigned to call later list");
                            return $result;
                        }
                        else{
                            $result  = array('msg'=>"failed to add");
                            return $result;
                        }



                }

                public function info_from_dietitian2(Request $request){

                    $raw_data=$request->json()->all();
                    $CallSid=$raw_data['CallSid'];
                    $CallFrom=$raw_data['CallFrom'];
                    $follows_food_recc=$raw_data['follows_food_recc'];
                    $follows_excercise_recc=$raw_data['follows_excercise_recc'];
                    $med_consumed=$raw_data['med_consumed'];
                    $complaint=$raw_data['complaint'];
                    $sugar_test_informed=$raw_data['sugar_test_informed'];
                    $remarks=$raw_data['remarks'];
                    $followupNumber=$raw_data['followupNumber'];
                    $attended=$raw_data['attended'];
                    $followupDate=$raw_data['followupDate'];
                    $followupExpiryDate=$raw_data['followupExpiryDate'];
                    $subscribe=$raw_data['subscribe'];
                    $doctor_followup_number=$request->input('doctor_followup_number');
                    $doctor_followup_date=$request->input('doctor_followup_date');
                    $doctor_followup_expiryDate=$request->input('doctor_followup_expiryDate');


                    //below code is to update doctor treatment

                    $times=0;
                    $subScenario='empty';
                    $subScenario1='empty';
                    $stop=false;

                    $exis= DB::table('patient_PPBS_report')->where('mob_num',$CallFrom)->where('createdAt','>=',now()->subDays()->setTime(0, 0, 0)->toDateTimeString())->exists();
                    // echo $CallFrom."\n";
                    // echo "entered"."\n";
                    // echo $exis."\n";

                    if($exis)
                    {
                        //echo "entered"."\n";

                        $diabetes_stage= DB::table('doctor_form')->where('CallSid',$CallSid)->first()->diabetes_stage;


                        $scene4=DB::table('S0_stage4_medications')->where('id',1)->first();
                        $scene3=DB::table('S0_stage3_medications')->where('id',1)->first();
                        $scene2=DB::table('S0_stage2_medications')->where('id',1)->first();
                        $scene1=DB::table('S0_stage1_medications')->where('id',1)->first();
                        $scene0=DB::table('s0_s6_medications')->where('id',1)->first();
                        $scene5=DB::table('diabetes_stage_medications')->where('id',1)->first();


                        $ppbs= DB::table('patient_PPBS_report')->where('mob_num',$CallFrom)->latest('createdAt')->first();
                        $NP=$ppbs->PPBS;

                       // echo $NP;

                    if($NP<=200)
                    {
                        $S1=true;
                        $times=1;
                        $scenario='S1';
                        $subScenario='S1';
                        $msg=$scene0->S1;
                    }
                    else if($NP>200 && $NP<250)
                    {
                        $S2=true;
                        $times=1;
                        $scenario='S2';
                        $subScenario='S2';
                        $msg=$scene0->S2;
                    }
                    else if($NP>=250 && $NP<300)
                    {
                        $S3=true;
                        $times=1;
                        $scenario='S3';
                        $subScenario='S3';
                        $msg=$scene0->S3;
                    }
                    else if($NP>=300 && $NP<350)
                    {
                        $S4=true;
                        $times=1;
                        $scenario='S4';

                        if($diabetes_stage==1)
                        {
                            $subScenario='S41';
                            $msg=$scene0->S41;
                        }
                        else if($diabetes_stage==2)
                        {
                            $subScenario='S42';
                            $msg=$scene0->S42;
                        }
                        else if($diabetes_stage==3)
                        {
                            $subScenario='S43';
                            $msg=$scene0->S43;
                        }
                        else if($diabetes_stage==4)
                        {
                            $subScenario='S44';
                            $msg=$scene0->S44;
                        }
                    }
                    else if($NP>=350 && $NP<400)
                    {
                        $S5=true;
                        $scenario='S5';
                        $times=1;
                        if($diabetes_stage==1)
                        {
                            $subScenario='S51';
                            $msg=$scene0->S51;
                        }
                        else if($diabetes_stage==2)
                        {
                            $subScenario='S52';
                            $msg=$scene0->S52;
                        }
                        else if($diabetes_stage==3)
                        {
                            $subScenario='S53';
                            $msg=$scene0->S53;
                        }
                        else if($diabetes_stage==4)
                        {
                            $subScenario='S54';
                            $msg=$scene0->S54;
                        }

                    }
                    else if($NP>=400 && $NP<450)
                    {
                        $S6=true;
                        $scenario='S6';
                        $subScenario='S6';
                        $times=1;
                        $msg='';
                    }
                    else
                    {
                        $S7=true;
                        $scenario='S7';
                        $subScenario='S7';
                        $times=1;
                        $msg='';
                    }
                    $patData=DB::table('followup_treatment')->where('CallSid',$CallSid)->first();

                    if($patData)
                    {

                        //echo $patData->scenario."\n";
                        $currStage=DB::table('followup_stage')->where('CallSid',$CallSid)->first()->stage;


                            if($patData->scenario==$scenario)
                            {
                                $times=$patData->times;
                                $times=$times+1;
                                if($times==3)
                                {
                                    if($scenario=='S1')
                                  {
                                    //echo $diabetes_stage."dbstage\n";

                                    if($diabetes_stage==4)
                                    {
                                        $subScenario1='S04';
                                        $msg=$scene4->S04;

                                    }
                                    else if($diabetes_stage==3)
                                    {
                                        $subScenario1='S03';
                                        $msg=$scene3->S03;
                                    }
                                    else if($diabetes_stage==2)
                                    {
                                        $subScenario1='S02';
                                        $msg=$scene2->S02;
                                    }
                                    else
                                    {
                                        $subScenario1='S01';
                                        $msg=$scene1->S01;
                                    }
                                    //echo $subScenario1."sub\n";
                                    $arr=[
                                        "isPPBS"=>'yes',
                                        "scenario"=>$scenario,
                                        "times"=>$times,
                                        "message"=>$msg,
                                        'subScenario'=>$subScenario1,
                                        "oldTreatment"=>$patData->message
                                    ];
                                    $stop=true;
                                    DB::beginTransaction();
                                    DB::table('followup_treatment')->where('CallFrom',$CallFrom)->update($arr);
                                    DB::table('followup_stage')->where('CallSid',$CallSid)->update(array("stage"=>$subScenario1));
                                    DB::commit();
                                  }
                                }
                            }
                            else if(($patData->subScenario=='S2'|| $patData->subScenario=='S3'||$patData->subScenario=='S4' || $patData->subScenario=='1'|| $patData->subScenario=='2'|| $patData->subScenario=='3'|| $patData->subScenario=='4') && $scenario=='S1')
                            {
                                $times=1;
                                DB::table('followup_treatment')->where('CallSid',$CallSid)->update(array("subScenario"=>$subScenario));
                            }

                         if($patData->subScenario=='S04')
                            {
                                if($patData->scenario=='S1' && $scenario=='S1')
                                {
                                    $times=$patData->times;
                                    $times=$times+1;
                                    if($times>=7 && $times<10)
                                    {
                                        $scenario='S1';
                                        $subScenario='S04a';
                                        $msg=$scene4->S04a;
                                    }
                                    else if($times>=10 && $times<14)
                                    {
                                        $scenario='S1';
                                        $subScenario='S04b';
                                        $msg=$scene4->S04b;
                                    }
                                    else if($times>=14 && $times<18)
                                    {
                                        $scenario='S1';
                                        $subScenario='S04c';
                                        $msg=$scene4->S04c;
                                    }
                                    else if($times>=18 && $times<21)
                                    {
                                        $scenario='S1';
                                        $subScenario='S04d';
                                        $msg=$scene4->S04d;
                                    }
                                    else if($times>=21 && $times<24)
                                    {
                                        $scenario='S1';
                                        $subScenario='S04e';
                                        $msg=$scene4->S04e;
                                    }
                                    else if($times>=24)
                                    {
                                        $scenario='S1';
                                        $subScenario='S04f';
                                        $msg=$scene4->S04f;
                                    }
                                }
                                else{
                                    if($currStage=='S04')
                                    {
                                        $subScenario='4';
                                        $msg=$scene5->stage4;
                                        DB::table('followup_treatment')->where('CallSid',$CallSid)->update(array("subScenario"=>$subScenario));
                                    }
                                    else if($currStage=='S04a')
                                    {
                                        $scenario='S1';
                                        $subScenario='S04';
                                        $times=3;
                                        $msg=$scene4->S04;
                                    }
                                    else if($currStage=='S04b')
                                    {
                                        $scenario='S1';
                                        $subScenario='S04a';
                                        $times=7;
                                        $msg=$scene4->S04a;
                                    }
                                    else if($currStage=='S04c')
                                    {
                                        $scenario='S1';
                                        $subScenario='S04b';
                                        $times=10;
                                        $msg=$scene4->S04b;
                                    }
                                    else if($currStage=='S04d')
                                    {
                                        $scenario='S1';
                                        $subScenario='S04c';
                                        $times=14;
                                        $msg=$scene4->S04c;
                                    }
                                    else if($currStage=='S04e')
                                    {
                                        $scenario='S1';
                                        $subScenario='S04d';
                                        $times=18;
                                        $msg=$scene4->S04d;
                                    }
                                    else if($currStage=='S04f')
                                    {
                                        $scenario='S1';
                                        $subScenario='S04e';
                                        $times=21;
                                        $msg=$scene4->S04e;
                                    }
                                }

                            }
                            else if($patData->subScenario=='S03')
                            {
                                //echo "here1\n";
                                if($patData->scenario=='S1' && $scenario=='S1')
                                {
                                    $times=$patData->times;
                                    $times=$times+1;
                                    if($times>=7 && $times<10)
                                    {
                                        $scenario='S1';
                                        $subScenario='S03a';
                                        $msg=$scene3->S03a;
                                    }
                                    else if($times>=10 && $times<14)
                                    {
                                        $scenario='S1';
                                        $subScenario='S03b';
                                        $msg=$scene3->S03b;
                                    }
                                    else if($times>=14 && $times<18)
                                    {
                                        $scenario='S1';
                                        $subScenario='S03c';
                                        $msg=$scene3->S03c;
                                    }
                                    else if($times>=18 && $times<21)
                                    {
                                        $scenario='S1';
                                        $subScenario='S03d';
                                        $msg=$scene3->S03d;
                                    }
                                    else if($times>=21 && $times<24)
                                    {
                                        $scenario='S1';
                                        $subScenario='S03e';
                                        $msg=$scene3->S03e;
                                    }
                                    else if($times>=24)
                                    {
                                        $scenario='S1';
                                        $subScenario='S03f';
                                        $msg=$scene3->S03f;
                                    }
                                }
                                else{
                                    if($currStage=='S03')
                                    {
                                        $subScenario='3';
                                        $msg=$scene5->stage3;
                                        DB::table('followup_treatment')->where('CallSid',$CallSid)->update(array("subScenario"=>$subScenario));
                                    }
                                    else if($currStage=='S03a')
                                    {
                                        $scenario='S1';
                                        $subScenario='S03';
                                        $times=3;
                                        $msg=$scene3->S03;
                                    }
                                    else if($currStage=='S03b')
                                    {
                                        $scenario='S1';
                                        $subScenario='S03a';
                                        $times=7;
                                        $msg=$scene3->S03a;
                                    }
                                    else if($currStage=='S03c')
                                    {
                                        //echo "2\n";
                                        $scenario='S1';
                                        $subScenario='S03b';
                                        $times=10;
                                        $msg=$scene3->S03b;
                                        //echo "yess"."\n";
                                    }
                                    else if($currStage=='S03d')
                                    {
                                        $scenario='S1';
                                        $subScenario='S03c';
                                        $times=14;
                                        $msg=$scene3->S03c;
                                    }
                                    else if($currStage=='S03e')
                                    {
                                        $scenario='S1';
                                        $subScenario='S03d';
                                        $times=18;
                                        $msg=$scene3->S03d;
                                    }
                                    else if($currStage=='S03f')
                                    {
                                        $scenario='S1';
                                        $subScenario='S03e';
                                        $times=21;
                                        $msg=$scene3->S03f;
                                    }
                                }

                            }
                            else if($patData->subScenario=='S02')
                            {
                                if($patData->scenario=='S1' && $scenario=='S1')
                                {
                                    $times=$patData->times;
                                    $times=$times+1;
                                    if($times>=7 && $times<10)
                                    {
                                        $scenario='S1';
                                        $subScenario='S02a';
                                        $msg=$scene2->S02a;
                                    }
                                    else if($times>=10 && $times<14)
                                    {
                                        $scenario='S1';
                                        $subScenario='S02b';
                                        $msg=$scene2->S02b;
                                    }
                                    else if($times>=14 && $times<18)
                                    {
                                        $scenario='S1';
                                        $subScenario='S02c';
                                        $msg=$scene2->S02c;
                                    }
                                    else if($times>=18 && $times<21)
                                    {
                                        $scenario='S1';
                                        $subScenario='S02d';
                                        $msg=$scene2->S02d;
                                    }
                                    else if($times>=21)
                                    {
                                        $scenario='S1';
                                        $subScenario='S02e';
                                        $msg=$scene2->S02e;
                                    }

                                }
                                else{
                                    if($currStage=='S02')
                                    {
                                        $subScenario='2';
                                        $msg=$scene5->stage2;
                                        DB::table('followup_treatment')->where('CallSid',$CallSid)->update(array("subScenario"=>$subScenario));
                                    }
                                    else if($currStage=='S02a')
                                    {
                                        $scenario='S1';
                                        $subScenario='S02';
                                        $times=3;
                                        $msg=$scene2->S02;
                                    }
                                    else if($currStage=='S02b')
                                    {
                                        $scenario='S1';
                                        $subScenario='S02a';
                                        $times=7;
                                        $msg=$scene2->S02a;
                                    }
                                    else if($currStage=='S02c')
                                    {
                                        $scenario='S1';
                                        $subScenario='S02b';
                                        $times=10;
                                        $msg=$scene2->S02b;
                                    }
                                    else if($currStage=='S02d')
                                    {
                                        $scenario='S1';
                                        $subScenario='S02c';
                                        $times=14;
                                        $msg=$scene2->S02c;
                                    }
                                    else if($currStage=='S02e')
                                    {
                                        $scenario='S1';
                                        $subScenario='S02d';
                                        $times=18;
                                        $msg=$scene2->S02d;
                                    }

                                }

                            }
                            else if($patData->subScenario=='S01')
                            {
                                if($patData->scenario=='S1' && $scenario=='S1')
                                {
                                    $times=$patData->times;
                                    $times=$times+1;
                                    if($times>=7 && $times<10)
                                    {
                                        $scenario='S1';
                                        $subScenario='S01a';
                                        $msg=$scene1->S01a;
                                    }
                                    else if($times>=10 && $times<14)
                                    {
                                        $scenario='S1';
                                        $subScenario='S01b';
                                        $msg=$scene1->S01b;
                                    }
                                    else if($times>=14 && $times<18)
                                    {
                                        $scenario='S1';
                                        $subScenario='S01c';
                                        $msg=$scene1->S01c;
                                    }
                                    else if($times>=18)
                                    {
                                        $scenario='S1';
                                        $subScenario='S01d';
                                        $msg=$scene1->S01d;
                                    }
                                }
                                else{
                                    if($currStage=='S01')
                                    {
                                        $subScenario='1';
                                        $msg=$scene5->stage1;
                                        DB::table('followup_treatment')->where('CallSid',$CallSid)->update(array("subScenario"=>$subScenario));
                                    }
                                    else if($currStage=='S01a')
                                    {
                                        $scenario='S1';
                                        $subScenario='S01';
                                        $times=3;
                                        $msg=$scene1->S01;
                                    }
                                    else if($currStage=='S01b')
                                    {
                                        $scenario='S1';
                                        $subScenario='S01a';
                                        $times=7;
                                        $msg=$scene1->S01a;
                                    }
                                    else if($currStage=='S01c')
                                    {
                                        $scenario='S1';
                                        $subScenario='S01b';
                                        $times=10;
                                        $msg=$scene1->S01b;
                                    }
                                    else if($currStage=='S01d')
                                    {
                                        $scenario='S1';
                                        $subScenario='S01c';
                                        $times=14;
                                        $msg=$scene1->S01c;
                                    }
                                }
                            }

                    }
                        else
                        {
                            $times=$times+1;
                        }
                        if(!$stop)
                        {
                        $ex= DB::table('followup_treatment')->where('CallSid',$CallSid)->exists();
                        //echo $subScenario."subby\n";
                        if($ex)
                        {
                            $arr=[
                                "isPPBS"=>'yes',
                                "scenario"=>$scenario,
                                "times"=>$times,
                                "message"=>$msg,
                                "oldTreatment"=>$patData->message
                            ];
                            DB::beginTransaction();
                                DB::table('followup_treatment')->where('CallSid',$CallSid)->update($arr);
                                DB::table('followup_stage')->where('CallSid',$CallSid)->update(array("stage"=>$subScenario));
                            DB::commit();
                        }
                        else{
                            $arr=[
                                "isPPBS"=>'yes',
                                "CallSid"=>$CallSid,
                                "CallFrom"=>$CallFrom,
                                "scenario"=>$scenario,
                                "subScenario"=>$subScenario,
                                "times"=>$times,
                                "message"=>$msg,
                                "oldTreatment"=>'no previous treatment history'
                            ];
                            $arr1=[
                                "CallSid"=>$CallSid,
                                "CallFrom"=>$CallFrom,
                                "stage"=>$subScenario,
                            ];
                            DB::beginTransaction();
                                 DB::table('followup_treatment')->insert($arr);
                                 DB::table('followup_stage')->insert($arr1);
                            DB::commit();
                        }
                    }

                    }

                    else
                    {
                        $ex= DB::table('followup_treatment')->where('CallSid',$CallSid)->exists();
                        if($ex)
                        {
                            $arr=[
                                "isPPBS"=>'no',
                            ];
                            DB::table('followup_treatment')->where('CallSid',$CallSid)->update($arr);
                        }
                        else{
                            $arr=[
                                "isPPBS"=>'no',
                                "CallSid"=>$CallSid,
                                "CallFrom"=>$CallFrom,
                                "scenario"=>'empty',
                                "subScenario"=>'empty',
                                "times"=>0,
                                "message"=>'empty',
                                "oldTreatment"=>'no previous treatment history'
                            ];
                            $arr1=[
                                "CallSid"=>$CallSid,
                                "CallFrom"=>$CallFrom,
                                "stage"=>'empty',
                            ];

                             DB::beginTransaction();

                                DB::table('followup_treatment')->insert($arr);

                                DB::table('followup_stage')->insert($arr1);

                             DB::commit();


                        }

                    }

                    //until here

                    $dataarr = array (

                        'CallSid'=>$CallSid,
                        'CallFrom'=> $CallFrom,
                        'follows_food_recc'=>$follows_food_recc,
                        'follows_excercise_recc'=>$follows_excercise_recc,
                        'med_consumed'=>$med_consumed,
                        'complaint'=>$complaint,
                        'sugar_test_informed'=>$sugar_test_informed,
                        'remarks'=> $remarks,
                        'followupNumber'=>$followupNumber,
                        'attended'=>$attended,
                        'followupDate'=>$followupDate,
                        'followupExpiryDate'=>$followupExpiryDate,
                        'subscribe'=>$subscribe

                   );

                    $dataarr1= array(
                        'CallSid'=>$CallSid,
                        'CallFrom'=> $CallFrom,
                        'followupNum'=>$doctor_followup_number,
                        'followupDate'=>$doctor_followup_date,
                        'followupExpiry'=>$doctor_followup_expiryDate,

                    );

                    if($subscribe=='no')
                    {
                        $insertdata=DB::table('info_from_dietitian2')->insert( $dataarr);
                        $resp = array('msg'=>'inserted Successfully');
                        return $resp;
                    }

                    $insertdata=DB::table('info_from_dietitian2')->insert($dataarr);
                    $insertdata1=DB::table('info_from_dietitian2_copy')->insert($dataarr);

                    $insertdata2=DB::table('list_for_followupDoctor')->insert($dataarr1);
                    $insertdata3=DB::table('list_for_followupDoctor_copy')->insert($dataarr1);

                    // $insertdata2=DB::table('common_tbl_for_dietitian1_doctor')->insert($dataarr1);
                    // $insertdata3=DB::table('common_tbl_for_dietitian1_doctor_copy')->insert($dataarr1);

                    if($insertdata && $insertdata1 && $insertdata2 && $insertdata3){
                        $resp = array('msg'=>'inserted Successfully');
                        return $resp;
                    }
                    else {
                        $resp = array('msg'=>'failed to insert');
                        return $resp;
                    }

                }


                public function patientListForFollowupDoctor(Request $request){

                    $Data=DB::table('list_for_followupDoctor_copy')->get();
                    // echo  $Data1;

                     $Dataarr=array();
                    // $Dataarr1=array();


                     foreach($Data as $data){


                         $today=now()->format('Y-m-d');

                            $date2 = Carbon::createFromFormat('Y-m-d', $today);

                            $date1 = Carbon::createFromFormat('Y-m-d', $today)->addDays(2);
                            $date = Carbon::createFromFormat('Y-m-d', $today)->addDays(1);
                            $x=explode(" ",$date1);
                            $y=explode(" ",$date);

                            if($today >= $data->followupExpiry){

                                $dataArr= array(
                                    'CallSid'=> $data->CallSid,
                                    'CallFrom'=>$data->CallFrom,
                                    'reccTreatment'=>'empty',
                                    'reccFood'=>'empty',
                                    'bp'=>'empty',
                                    'thyroid'=>'empty',
                                    'burningFoot'=>'empty',
                                    'complaint'=>'empty',
                                    'remarks'=>'empty',
                                    'followupNumber'=>$data->followupNum,
                                    'attended'=>'no',
                                    'followupDate'=>$data->followupDate,
                                    'followupExpiryDate'=>$data->followupExpiry
                                );
                                $followNum=DB::table('followup_count')->where('CallSid',$data->CallSid)->first();

                                $dataarr1= array(
                                    'CallSid'=>$data->CallSid,
                                    'CallFrom'=>$data->CallFrom,
                                    'followupNum'=>$followNum->dietitian_followup_count + 1,
                                    'followupDate'=>$y[0],
                                    'followupExpiry'=>$x[0],
                                );

                                $insertData=array(
                                    'd_id'=>'empty',
                                    'assignedTo'=>'empty',
                                    'CallSid'=>$data->CallSid,
                                    'CallFrom'=>$data->CallFrom,
                                    'registration_number'=>'empty',
                                    'patientId'=>'empty',
                                    'followupNum'=>$data->followupNum

                            );



                                DB::table('followup_count')->where('CallSid',$data->CallSid)->update(['doctor_followup_count'=>$followNum->doctor_followup_count + 1]);

                                $insertData4=DB::table('info_from_followup_doctor')->insert($dataArr);
                                $insertData1=DB::table('info_from_followup_doctor_copy')->insert($dataArr);
                                $insertData2=DB::table('list_for_dietitian2')->insert($dataarr1);
                                $insertData3=DB::table('list_for_dietitian2_copy')->insert($dataarr1);


                                $insertdata5=DB::table('followup_doctor_assign_history')->insert($insertData);
                                $remove = DB::table('list_for_followupDoctor_copy')->where('CallSid', $data->CallSid)->where('followupNum',$data->followupNum)->delete();
                                $remove1 = DB::table('info_from_dietitian2_copy')->where('CallSid', $data->CallSid)->where('followupNumber',$followNum->dietitian_followup_count)->delete();
                              }

                         if($today == $data->followupDate){
                                 array_push($Dataarr,$data);
                               }


                         }
                         $resp = array('Data'=>$Dataarr);
                         return $resp;

                 }

                 //Followup for Doctor
                 public function info_from_followup_doctor(Request $request){

                    $raw_data=$request->json()->all();
                    $CallSid=$raw_data['CallSid'];
                    $CallFrom=$raw_data['CallFrom'];
                    $reccTreatment=$raw_data['reccTreatment'];
                    $reccFood=$raw_data['reccFood'];
                    $bp=$raw_data['bp'];
                    $thyroid=$raw_data['thyroid'];
                    $burningFoot=$raw_data['burningFoot'];
                    $complaint=$raw_data['complaint'];
                    $remarks=$raw_data['remarks'];
                    $followupNumber=$raw_data['followupNumber'];
                    $attended=$raw_data['attended'];
                    $followupDate=$raw_data['followupDate'];
                    $followupExpiryDate=$raw_data['followupExpiryDate'];
                    $doctor_followup_number=$raw_data['doctor_followup_number'];
                    $doctor_followup_date=$raw_data['doctor_followup_date'];
                    $doctor_followup_expiryDate=$raw_data['doctor_followup_expiryDate'];

                    $dataArr= array(
                        'CallSid'=> $CallSid,
                        'CallFrom'=>$CallFrom,
                        'reccTreatment'=>$reccTreatment,
                        'reccFood'=>$reccFood,
                        'bp'=>$bp,
                        'thyroid'=>$thyroid,
                        'burningFoot'=>$burningFoot,
                        'complaint'=>$complaint,
                        'remarks'=>$remarks,
                        'followupNumber'=>$doctor_followup_number,
                        'attended'=>$attended,
                        'followupDate'=>$doctor_followup_date,
                        'followupExpiryDate'=>$doctor_followup_expiryDate
                    );

                    $dataarr1= array(
                        'CallSid'=>$CallSid,
                        'CallFrom'=> $CallFrom,
                        'followupNum'=>$followupNumber,
                        'followupDate'=>$followupDate,
                        'followupExpiry'=>$followupExpiryDate,
                    );

                    $insertData=DB::table('info_from_followup_doctor')->insert($dataArr);
                    $insertData1=DB::table('info_from_followup_doctor_copy')->insert($dataArr);
                    $insertData2=DB::table('list_for_dietitian2')->insert($dataarr1);
                    $insertData3=DB::table('list_for_dietitian2_copy')->insert($dataarr1);
                    if($insertData && $insertData1 && $insertData2 &&$insertData3){
                        $resp = array('msg'=>'inserted Successfully');
                        return $resp;
                    }
                    else {
                        $resp = array('msg'=>'failed to insert');
                        return $resp;
                    }
                }

                public function assignToFollowupDoc(Request $request)
                {
                    $d_id=$request->d_id;
                    $registration_number=$request->registration_number;
                    $assignedTo=$request->assignedTo;
                    $patientId=$request->patientId;
                    $CallSid=$request->CallSid;
                    $CallFrom=$request->CallFrom;

                    $item = DB::table('list_for_followupDoctor_copy')
                    ->where('CallSid', $CallSid)->first();
                    //echo $item->followupNum;
                       $insertData=array(
                                'd_id'=>$d_id,
                                'assignedTo'=>$assignedTo,
                                'CallSid'=>$CallSid,
                                'CallFrom'=>$CallFrom,
                                'registration_number'=>$registration_number,
                                'patientId'=>$patientId,
                                'followupNum'=>$item->followupNum

                        );

                         if($item){
                            $insertdata=DB::table('followup_doctor_assign_history')->insert($insertData);
                            $remove = DB::table('list_for_followupDoctor_copy')
                            ->where('CallSid', $CallSid)
                            ->where('followupNum',$item->followupNum)
                            ->delete();
                            $remove1 = DB::table('info_from_dietitian2_copy')
                            ->where('CallSid', $CallSid)
                            ->where('followupNumber',$item->followupNum)
                            ->delete();

                            $result  = array('msg'=>"Assigned Successfully");
                            return $result;
                          }
                         else{

                                $result  = array('msg'=>"failed to add");
                                return $result;

                             }

                 }




                public function callLaterFollowupDoctor(Request $request){

                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $type=$request->input('type');
                    $CallAt=$request->input('CallAt');
                    $followupNum=$request->input('followupNum');

                    $resp_data= DB::table('list_for_followupDoctor')
                    ->where('CallFrom',$CallFrom)
                    ->where('followupNum',$followupNum)->first();

                    $followupNum1=DB::table('followup_count')
                    ->where('CallSid',$CallSid)->first();

                    $patientData=DB::table('info_from_dietitian2')
                    ->where('CallSid',$CallSid)
                    ->where('followupNumber',$followupNum1->dietitian_followup_count)->first();

                   $now= array (
                    'd_id'=>$d_id,
                    'assignedTo'=>$assignedTo,
                    'CallSid'=>$CallSid,
                    'CallFrom'=>$CallFrom,
                    'type'=>$type,
                    'CallAt'=>$CallAt
                   );

                   $dataarr = array (

                    'CallSid'=>$CallSid,
                    'CallFrom'=> $CallFrom,
                    'follows_food_recc'=> $patientData->follows_food_recc,
                    'follows_excercise_recc'=> $patientData->follows_excercise_recc,
                    'med_consumed'=> $patientData->med_consumed,
                    'complaint'=>$patientData->complaint,
                    'sugar_test_informed'=>$patientData->sugar_test_informed,
                    'remarks'=> $patientData->remarks,
                    'followupNumber'=>$patientData->followupNumber,
                    'attended'=>$patientData->attended,
                    'followupDate'=>$patientData->followupDate,
                    'followupExpiryDate'=>$patientData->followupExpiryDate,
                    'subscribe'=>$patientData->subscribe

               );

                   $insertData = array (
                        'CallSid'=>$CallSid,
                        'CallFrom'=> $CallFrom,
                        'followupNum'=>$resp_data->followupNum,
                        'followupDate'=>$resp_data->followupDate,
                        'followupExpiry'=>$resp_data->followupExpiry,
                        'CallAt'=>$CallAt
                         );

                         if($resp_data)
                         {
                             $insert=DB::table('list_for_followupDoctor_copy')->insert($insertData);
                             $insert2=DB::table('info_from_dietitian2_copy')->insert($dataarr);
                             $insert1=DB::table('call_later_from_followup_doctor')->insert($now);
                             $removeData=DB::table('followup_doctor_assign_history')
                              ->where('CallSid',$CallSid)
                              ->where('followupNum',$followupNum)
                              ->delete();

                             if($insert && $removeData && $insert1)
                             {
                                 $result  = array('msg'=>"Assigned to call later list");
                                 return $result;
                             }
                             else{
                                 $result  = array('msg'=>"failed to add");
                                 return $result;
                             }

                        }
                    }




            public function offerRejectedFromFollowup(Request $request)
            {
                    $d_id=$request->input('d_id');
                    $assignedTo=$request->input('assignedTo');
                    $CallSid=$request->input('CallSid');
                    $CallFrom=$request->input('CallFrom');
                    $remarks=$request->input('remarks');
                $array=array(
                    'd_id'=> $d_id,
                    'assignedTo'=>$assignedTo,
                    'CallSid'=>$CallSid,
                    'CallFrom'=>$CallFrom,
                    'remarks'=>$remarks
                );
                $exist=DB::table('offer_rejected_from_followup')->where('CallSid',$CallSid)->exists();
                if($exist)
                {
                    $ret=array('data'=>'assigned to rejected list');
                    return $ret;
                }
                else{
                    $data=DB::table('offer_rejected_from_followup')->insert($array);
                }


                if($data)
                {
                    $ret=array('data'=>'assigned to rejected list');
                    return $ret;
                }
                else{
                    return $data;
                }

            }



            public function pushToDietitian2List()
            {
                $data=DB::table('list_for_followupDoctor_copy')->get();

                $today=now()->format('Y-m-d');
                $date1 = Carbon::createFromFormat('Y-m-d', $today);
                $date = $date1->addDays(4);
                $date2 = $date1->addDays(3);
                $x=explode(" ",$date);
                $y=explode(" ",$date2);
                $createdDate=$y[0];
                $expiryDate=$x[0];

                foreach($data as $data)
                {
                    $date = Carbon::createFromFormat('Y-m-d', $data->followupExpiry);


                    if($today==$date)
                    {
                        $push=array(
                            "CallSid"=>$data->CallSid,
                            "CallFrom"=>$data->CallFrom,
                            "followupNum"=>$data->followupNum+1,
                            "followupDate"=>createdDate,
                            "followupExpiry"=>expiryDate,
                            "attended"=>"no"
                        );

                        if (DB::table('list_for_dietitin2')->where('CallSid', '=', $oldDate->CallSid)->where('followupNum','=',$oldDate->followupNum+1)->exists()) {
                            continue;
                         }
                         else {
                            $data_insert=DB::table('list_for_dietitin2')->insert($dataArr);
                            $data_insert1=DB::table('list_for_dietitin2_copy')->insert($dataArr);
                         }
                    }

                }

                if($data_insert && $data_insert1)
                {
                    $ret=array('data'=>'updated successfully');
                    return $ret;
                }
                else{
                    return $data;
                }

            }

            public function followupCount(Request $request)
            {
                $CallSid=$request->input('CallSid');
                $data=DB::table('followup_count')->where('CallSid',$CallSid)->first();
                if($data)
                {
                    $ret=array('data'=>$data);
                    return $ret;
                }
                else{
                    $ret=array('data'=>'no data');
                    return $ret;
                }
            }

            public function updateFollowupCount(Request $request)
            {
                $CallSid=$request->input('CallSid');
                $CallFrom=$request->input('CallFrom');
                $doctorCount=$request->input('doctorCount');
                $dietitianCount=$request->input('dietitianCount');
                $data;
            $ex=DB::table('followup_count')->where('CallSid',$CallSid)->exists();
            if($ex)
            {
                if($doctorCount==null && $dietitianCount!=null)
                {

                    $arr = array(
                        'dietitian_followup_count' => $dietitianCount
                    );
                    $data=DB::table('followup_count')->where('CallSid',$CallSid)->update($arr);
                }
                else if($doctorCount!=null && $dietitianCount==null)
                {
                    $arr = array(
                        'doctor_followup_count' => $doctorCount
                    );
                    $data=DB::table('followup_count')->where('CallSid',$CallSid)->update($arr);
                }
                else if($doctorCount!=null && $dietitianCount!=null){
                    $arr = array(
                        'dietitian_followup_count' => $dietitianCount,
                        'doctor_followup_count' => $doctorCount
                    );
                    $data=DB::table('followup_count')->where('CallSid',$CallSid)->update($arr);
                }
                else
                {
                    $data=0;
                }
            }
            else{
                $arr = array(
                    'CallSid'=>$CallSid,
                    'CallFrom'=>$CallFrom,
                    'dietitian_followup_count' => $dietitianCount,
                    'doctor_followup_count' => $doctorCount
                );
                $data=DB::table('followup_count')->where('CallSid',$CallSid)->insert($arr);
            }

                 if($data)
                {
                    $ret=array('msg'=>'updated successfully');
                    return $ret;
                }
                else{
                    $ret=array('msg'=>'no data');
                    return $ret;
                }
            }

            public function createFollowupCount(Request $request)
            {
                $raw_data = $request->json()->all();
                $CallSid=$raw_data['CallSid'];
                $CallFrom=$raw_data['CallFrom'];
                $doctorCount=$raw_data['doctorCount'];
                $dietitianCount=$raw_data['dietitianCount'];
                $data;

                    $arr = array(
                        'CallSid'=>$CallSid,
                        'CallFrom'=>$CallFrom,
                        'dietitian_followup_count' => $dietitianCount,
                        'doctor_followup_count' => $doctorCount
                    );
                    $data=DB::table('followup_count')->insert($arr);

                 if($data)
                {
                    $ret=array('msg'=>'successfully inserted');
                    return $ret;
                }
                else{
                    $ret=array('msg'=>'insertion failed');
                    return $ret;
                }
            }

            public function prevFollowupRemarks(Request $request)
            {
                $CallSid=$request->input('CallSid');

                $data=DB::table('info_from_dietitian1')->where('CallSid',$CallSid)->get();
                $data1=DB::table('info_from_dietitian2')->where('CallSid',$CallSid)->get();
                $data2=DB::table('info_from_followup_doctor')->where('CallSid',$CallSid)->get();

                $arr=array();
                $arr1=array();
                $arr2=array();

                if(sizeof($data))
                {
                    foreach($data as $data)
                    {
                        $remark=$data->remarks;
                        array_push($arr,$remark);
                    }
                }

                if(sizeof($data1))
                {
                    foreach($data1 as $data1)
                    {
                        $remark=$data1->remarks;
                        array_push($arr1,$remark);
                    }
                }
                if(sizeof($data2))
                {
                    foreach($data2 as $data2)
                    {
                        $remark=$data2->remarks;
                        array_push($arr2,$remark);
                    }
                }

                return array(
                    "d1_remarks"=>$arr,
                    "d2_remarks"=>$arr1,
                    "doc_remarks"=>$arr2
                );
            }


            public function followupDelivery(Request $request)
            {
                $raw_data = $request->json()->all();
                $CallSid=$raw_data['CallSid'];
                $CallFrom=$raw_data['CallFrom'];
                $delivery_count=$raw_data['delivery_count'];

                $exist=DB::table('followup_delivery_list')->where('CallSid',$CallSid)->where('delivery_count',$delivery_count)->exists();
                if($exist)
                {
                    return array("msg"=>"already exists");
                }
                $data=DB::table('followup_delivery_list')->insert(["CallSid"=>$CallSid,"CallFrom"=>$CallFrom,"delivery_count"=>$delivery_count]);
                $data1=DB::table('followup_delivery_list_copy')->insert(["CallSid"=>$CallSid,"CallFrom"=>$CallFrom,"delivery_count"=>$delivery_count]);
                if($data && $data1)
                {
                    $arr=array("msg"=>'successfully submitted');
                    return $arr;
                }
            }

            public function followupDeliveryexistence(Request $request)
            {
                $raw_data = $request->json()->all();
                $CallSid=$raw_data['CallSid'];
                $CallFrom=$raw_data['CallFrom'];
                $delivery_count=$raw_data['delivery_count'];

                $exist=DB::table('followup_delivery_list')->where('CallSid',$CallSid)->where('delivery_count',$delivery_count)->exists();
                if($exist)
                {
                    return array("msg"=>"item exists");
                }
                else
                {
                    $arr=array("msg"=>'item doesn\'t exist');
                    return $arr;
                }
            }

            public function patientPPBSReport(Request $request){

                $raw_data = $request->json()->all();
                $patientId=$request['patientId'];
                $name=$request['name'];
                $mob_num=$request['mob_num'];
                $PPBS=$request['PPBS'];

                $data=array(
                    'patientId'=>$patientId,
                    'name'=>$name,
                    'mob_num'=>$mob_num,
                    'PPBS'=>$PPBS
                );

                $insertData=DB::table('patient_PPBS_report')->insert($data);

                if($insertData)
                {
                    $ret=array('msg'=>'successfully inserted');
                    return $ret;
                }
                else{
                    $ret=array('msg'=>'insertion failed');
                    return $ret;
                }


            }

            public function getPPBSReport(Request $request)
            {
                $mob_num=$request->input('mob_num');
                $data=DB::table('patient_PPBS_report')->where('mob_num',$mob_num)->get();
                return array('data'=>$data);
            }

            public function prevFollowupComplaints(Request $request)
            {
                $CallSid=$request->input('CallSid');


                $data1=DB::table('info_from_dietitian2')->where('CallSid',$CallSid)->get();
                $data2=DB::table('info_from_followup_doctor')->where('CallSid',$CallSid)->get();

                $arr1=array();
                $arr2=array();


                if(sizeof($data1))
                {
                    foreach($data1 as $data1)
                    {
                        $remark=$data1->complaint;
                        array_push($arr1,$remark);
                    }
                }
                if(sizeof($data2))
                {
                    foreach($data2 as $data2)
                    {
                        $remark=$data2->complaint;
                        array_push($arr2,$remark);
                    }
                }

                return array(
                    "d2_complaints"=>$arr1,
                    "doc_complaints"=>$arr2
                );
            }

            public function prevFollowupFoodRecc(Request $request)
            {
                $CallSid=$request->input('CallSid');

                $data1=DB::table('info_from_dietitian2')->where('CallSid',$CallSid)->get();

                $arr1=array();


                if(sizeof($data1))
                {
                    foreach($data1 as $data1)
                    {
                        $remark=$data1->follows_food_recc;
                        array_push($arr1,$remark);
                    }
                }

                return array(
                    "d2_food"=>$arr1,
                );
            }

            public function prevFollowupExcRecc(Request $request)
            {
                $CallSid=$request->input('CallSid');

                $data1=DB::table('info_from_dietitian2')->where('CallSid',$CallSid)->get();

                $arr1=array();


                if(sizeof($data1))
                {
                    foreach($data1 as $data1)
                    {
                        $remark=$data1->follows_excercise_recc;
                        array_push($arr1,$remark);
                    }
                }

                return array(
                    "d2_exercise"=>$arr1,
                );
            }

            public function treamentForFollowupDoc(Request $request)
            {
                $CallSid=$request->input('CallSid');
                $CallFrom=$request->input('CallFrom');

                $data1=DB::table('followup_treatment')->where('CallSid',$CallSid)->first();
                $data2=DB::table('followup_stage')->where('CallSid',$CallSid)->first();

                if($data1 && $data2)
                {
                    $arr=array(
                        "followup_treatment"=>$data1,
                        "followup_stage"=>$data2->stage
                    );
                    return array(
                        "data"=>$arr
                    );
                }
                else{
                    return array(
                        "data"=>"no data"
                    );
                }

            }



            public function dummy(){
                $table=DB::table('posts')->get();
                echo $table;
            }

        }

