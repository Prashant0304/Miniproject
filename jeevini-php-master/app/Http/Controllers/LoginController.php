<?php

namespace App\Http\Controllers;
ob_start();
use Illuminate\Http\Request;
use  App\Models\Login;
use  App\Models\Feeds;
use App\Helpers\TokenHelper;
use App\Helpers\Calculation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use DB;
use Illuminate\Support\Facades\Schema;
use Twilio\TwiML\MessagingResponse;
class LoginController extends Controller
{
    public function __construct(){
        $this->_login = new Login;
        $this->_calculation = new Calculation; 
        $this->_feed = new Feeds;
    }

    public function sendSMS($toNumber, $code){
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
        $client->messages->create(
            "+".$toNumber ,
            array(
                'from' => $from_phone_number,
                "body" => "Jivini App Authentication Code is "." ".$code,
            )
        );
    }
    public function getOTP(Request $request){
        $phone = $request->phone;
         $code = rand(100000, 999999);
        $validator = Validator::make($request->all(),[
            'phone' => 'required|unique:patients',
        ]);
        if ($validator->fails()) {
            $response = [
                'message' => "User Exist",
                'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
        else 
        {
            $data = array(
                'phone' => $request->phone,
                'code' => $code,
            );
            $toNumber = $request->phone;        
             $this->sendSMS($toNumber,$code);
            $response = [
                'message' => "Verification Code Sent Successfully",
                'key' => 1,
            ];
             $insert = $this->_login->insertOTP($data);
             return $response;
        }
    }

    public  function OTPverificationsignup(Request $request){
        $code =  $request->code;
        $phone = $request->phone;
        $flag = $request->flag;
        $verified = $this->_login->verifyCodeSignup($code, $phone);
        if($verified == 1) {
            $response = [
                'message' => 'Verification Done Succesfully!',
                'key' => 1,
            ];
            return json_encode($response);
        }
        elseif($verified == 0) {
            $response = [
                'message' => 'Please Check OTP Again',
                'key' => 0,
            ];
            return json_encode($response);
        }
    }
   
/*public function RegisterPatients(Request $request){
    $raw_data = $request->json()->all();
    $weight = $raw_data['weight'];
    $height = $raw_data['height'];
    $age = $raw_data['age'];
    $healthStatusID = $raw_data['healthStatusID'];
    $gender = $raw_data['gender'];
    $diabetes = $raw_data['diabetes'];
    $doctorID = $raw_data['doctorID'];
    
    $insert_patient_data = array(
            "patientID" => null,
            "firstName" => $raw_data['firstName'],
            "lastName" => $raw_data['lastName'],
            "phone" => $raw_data['phone'],
            "language" => $raw_data['language'],
            "locationID" => $raw_data['locationID'],
            "gender" => $raw_data['gender'],
            "doctorID" => $doctorID,

    );
    $patientID = $this->_login->addPatients($insert_patient_data);  
    $BMI = ($weight/$height)/$height;      
    $insert_patient_factor = array(
                'patientID' => $patientID,
                'age' => $age,
                'weight' => $weight,
                'height' => $height,
                'gender' => $gender,
                'healthStatusID' => $healthStatusID,
                'diabetes' => $diabetes,
                'feedID' => "0",
                'albuminFactor' => "1",
                'performanceStatusID' => "",
                'requiredCalories' => "",
                'requiredProtein' => "",
                'requiredCarbs' => "",
                'requiredFats' => "",
                'BMI' => $BMI,
    );
    //To insert data in Patient factor Table
     $insertPatientFactor = $this->_login->insertPatientFactor($insert_patient_factor);
    //To Assign Feed To Patient 
    // $patientID = '42';
    $assignFeedID = $this->_calculation->assignFeedID($patientID);
    if ($healthStatusID > 1) {
        $requiredCalories = $this->_calculation->requiredCaloriesCalculation($patientID);
    }
    $patientDetails = $this->_login->getPatientDetail($patientID);
    $payload = array(
        'user_info' => array(
                'userId' =>  $patientID,
            )
        );
        $token = TokenHelper::generateToken($payload);
        //$insertToken = $this->_login->insertToken($patientID, $token);
        // exit;
        $response = [
            'key' => 1,
            'message' => 'Verification Done Successfully!',
            'accessToken' => $token,
            'patientDetails' =>$patientDetails,
        ];
    return json_encode($response);
}*/

public function RegisterPatients(Request $request){
    // echo "hii";
    $raw_data = $request->json()->all();
    $weight = $raw_data['weight'];
    $height = $raw_data['height'];
    $age = $raw_data['age'];
    $healthStatusID = $raw_data['healthStatusID'];
    $gender = $raw_data['gender'];
    $diabetes = $raw_data['diabetes'];
    $doctorID = $raw_data['doctorID'];
    
    $insert_patient_data = array(
            "patientID" => null,
            "firstName" => $raw_data['firstName'],
            "lastName" => $raw_data['lastName'],
            "phone" => $raw_data['phone'],
            "language" => $raw_data['language'],
            "locationID" => $raw_data['locationID'],
            "gender" => $raw_data['gender'],
            "doctorID" => $doctorID,

    );
    $patientID = $this->_login->addPatients($insert_patient_data);  
    $BMI = ($weight/$height)/$height;      
    $insert_patient_factor = array(
                'patientID' => $patientID,
                'age' => $age,
                'weight' => $weight,
                'height' => $height,
                'gender' => $gender,
                'healthStatusID' => $healthStatusID,
                'diabetes' => $diabetes,
                'feedID' => "0",
                'albuminFactor' => "1",
                'performanceStatusID' => "",
                'requiredCalories' => "",
                'requiredProtein' => "",
                'requiredCarbs' => "",
                'requiredFats' => "",
                'BMI' => $BMI,
    );
    //To insert data in Patient factor Table
     $insertPatientFactor = $this->_login->insertPatientFactor($insert_patient_factor);
    //To Assign Feed To Patient 
    // $patientID = '42';
    $assignFeedID = $this->_calculation->assignFeedID($patientID);
    if ($healthStatusID > 1) {
        $requiredCalories = $this->_calculation->requiredCaloriesCalculation($patientID);
    }
    $patientDetails = $this->_login->getPatientDetail($patientID);
    $payload = array(
        'user_info' => array(
                'userId' =>  $patientID,
            )
        );
        $token = TokenHelper::generateToken($payload);
        //$insertToken = $this->_login->insertToken($patientID, $token);
        // exit;
        $response = [
            'key' => 1,
            'message' => 'Verification Done Successfully!',
            'accessToken' => $token,
            'patientDetails' =>$patientDetails,
        ];
    return json_encode($response);
}


    public function GetPatients(){
        echo "success";
    }

    public function LoginOtp(Request $request){
        $phone = $request->phone;
        $code = rand(100000, 999999);
        $exist = $this->_login->check_existance($phone);

        if($exist == 1){
            // echo "exsit";
            if($phone==919632675745)
            {
                $response = [
                    'message' => "Verification Code Successfully",
                    'key' => 1,
                ];
                return json_encode($response);
            }
            else{
             $verified = $this->_login->updateLoginOTP($code, $phone );
             $this->sendSMS($phone,$code);
            $response = [
                'message' => "Verification Code Sent Successfully",
                'key' => 1,
            ];
            return json_encode($response);
        }
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
        $phone = $request->phone;
        $data = $this->_login->verifyCodeLogin($code, $phone);
        if (!empty($data)) {
            $payload = array(
                'user_info' => array(
                        'userId' =>  $data->patientID,
                    )
                );
                $token = TokenHelper::generateToken($payload);
                $response = [
                    'message' => 'Verification Done Successfully!',
                    'key' => 1,
                    'accessToken' => $token,
                    'patient_data' => $data,
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

    public function insertHealthStatus(Request $request){
        $status = array(
            
            'id' => null,
            'status' => $request->status, 
        );
        $insert = $this->_login->insertHealthStatusmodel($status);
        if ($insert) {
            $response = [
                'message' => 'Health Status Inserted',
                'key' => 1,
            ];
        return json_encode($response);
        }
        else {
            $response = [
                'message' => 'Not Inserted',
                'key' => 0,
            ];
        return json_encode($response);
        }
    }

    public function getAllPatients(){
        $patients = $this->_login->allPatients();
        return json_encode($patients);
    }
    public function SinglePatient(Request $request){
        $patientID = $request->patientID;
        //echo controller
        $patient = $this->_login->singlePatient($patientID);
        return json_encode($patient);
    }

    public function pregnantPatient(Request $request){
        $data = $request->json()->all();
        $insertID = $this->_login->insertPegnantStatus($data);
        $patientID = $data['patientID'];
        $requiredCalories = $this->_calculation->requiredCaloriesCalculation($patientID);
        $patientDetails = $this->_login->getPatientDetail($patientID);
        $response = [
            'patientDetails' => $patientDetails,
        ];
        return json_encode($response);
    }

    public function ListState()
    {
        $data = $this->_login->allState();

        if(!empty($data))
        {
            $i = 0;

            foreach($data as $val)
            {
               $resp[$i]['state_id'] = $val->id; 
               $resp[$i]['state'] = $val->name; 
               $i++;
            }
            
        }

        if(!empty($data))
        {
            return json_encode(array('data'=>$resp,'msg'=>'State Found'));
        }
        else
        {
            return json_encode(array('data'=>null,'msg'=>'No State Found'));
        }
    }

    public function ListCity(Request $request)
    {
        $data = $this->_login->allCity($request->state_id);

        if(!empty($data))
        {
            $i = 0;

            foreach($data as $val)
            {
               $resp[$i]['city_id'] = $val->id; 
               $resp[$i]['city'] = $val->city; 
               $i++;
            }
            
        }

        if(!empty($data))
        {
            return json_encode(array('data'=>$resp,'msg'=>'City Found'));
        }
        else
        {
            return json_encode(array('data'=>null,'msg'=>'No City Found'));
        }
    }

    public function AdminLogin(Request $request)
    {
        $admin_data = $this->_login->adminLogin($request->username,$request->password);

        if(empty($admin_data))
        {
            return json_encode(array('data'=>null,'msg'=>'Username Or Password Is Wrong'));
        }
        else
        {
            return json_encode(array('data'=>$admin_data,'msg'=>'Admin Found'));
        }
    }

    public function CreateAdminUser(Request $request)
    {
        if(empty($request->name) || empty($request->role) || empty($request->email_id) || empty($request->password))
        {
            $data = array('data'=>null,'msg'=>'Required Parameter Missing');
        }
        else
        {
            $admin_data = array();
            $admin_data['name'] = $request->name;
            $admin_data['role'] = $request->role;
            $admin_data['email_id'] = $request->email_id;
            $admin_data['username'] = $request->email_id;

            if(empty($request->admin_id))
            {
                $admin_data['password'] = md5($request->email_id."|".$request->password);
            }

            $admin_data['added_on'] = time();
            $admin_data['updated_on'] = time();

            if(empty($request->admin_id))
            {
                $data = $this->_login->insertAdminMaster($admin_data);
            }
            else
            {
                $data = $this->_login->updateAdminMaster($admin_data,$request->admin_id);
            }
            
        }

        return json_encode($data);
    }

    public function ListUser(Request $request)
    {
        if(empty($request->admin_id))
        {
            $admin_data = $this->_login->getAllUser();
        }
        else
        {
           $admin_data = $this->_login->getAllUser($request->admin_id); 
        }
        

        return json_encode($admin_data);
    }

    public function DeleteUser(request $request)
    {
        $id = $request->admin_id;

        $user = DB::table('admin')
                    ->where('admin_id',$id)
                    ->delete(); 

        return json_encode(array('msg'=>'User Deleted Successfully'));
    }

    public function ListPatient(Request $request)
    {
        if(empty($request->patient_id))
        {
            $patient_data = $this->_login->allPatients();
        }
        else
        {
           $patient_data = $this->_login->allPatients($request->patient_id); 
        }
        
        if(empty($patient_data))
        {
            $resp = array('msg'=>'Patient Not Found','data'=>null);
        }
        else
        {
            $i = 0;

            foreach($patient_data as $patient)
            {
                $data[$i]['patient_id'] = $patient->patientID;
                $data[$i]['name'] = $patient->firstName.' '.$patient->lastName;
                $data[$i]['feed'] = $patient->feedID;
                $data[$i]['location'] = $patient->locationID;
                $data[$i]['age'] = $patient->age;
                $data[$i]['gender'] = $patient->gender;
                $i++;
            }

            $resp = array('msg'=>'Patient Found','data'=>$data);
        }

        return json_encode($resp);
    }

    public function patientDetail(Request $request)
    {
        $patient_data = $this->_login->getPatientDetail($request->patient_id);

        if(!empty($patient_data))
        {
            $resp = array('msg'=>'Patient Details Found','data'=>$patient_data);
        }
        else
        {
            $resp = array('msg'=>'No Patient Details Found','data'=>null);
        }

        return json_encode($resp);
    }

    public function DeletePatient(request $request)
    {
        $id = $request->patient_id;

        $user = DB::table('patients')
                    ->where('patientID',$id)
                    ->delete();
        $user = DB::table('patient_factor')
                    ->where('patientID',$id)
                    ->delete(); 

        return json_encode(array('msg'=>'Patients Deleted Successfully'));
    }

    public function createDoctor(Request $request)
    {
        if(empty($request->name) || empty($request->location) || empty($request->age) || empty($request->speciality) || empty($request->doctor_no))
        {
            $data = array('data'=>null,'msg'=>'Required Parameter Missing');
        }
        else
        {
            $admin_data = array();
            $admin_data['name'] = $request->name;
            $admin_data['speciality'] = $request->speciality;
            $admin_data['age'] = $request->age;
            $admin_data['location'] = $request->location;
            $admin_data['doctor_no'] = $request->doctor_no;

            if(empty($request->doctor_id))
            {
                $admin_data['added_on'] = time();
                $admin_data['updated_on'] = time();
                $data = $this->_login->insertDoctorMaster($admin_data);
            }
            else
            {
                $admin_data['updated_on'] = time();
                $data = $this->_login->updateDoctorMaster($admin_data,$request->doctor_id);
            }
        }

        return json_encode($data);
    }

    public function ListDoctors(Request $request)
    {
        if(!empty($request->doctor_id))
        {
            $id = $request->doctor_id;
        }
        else
        {
            $id = '';
        }

        $doctor_data = $this->_login->allDoctors($id);

        if(empty($doctor_data))
        {
            $resp = array('msg'=>'Doctor Not Found','data'=>null);
        }
        else
        {
            $i = 0;

            foreach($doctor_data as $doctor)
            {
                $data[$i]['doctor_id'] = $doctor->doctor_id;
                $data[$i]['name'] = $doctor->name;
                $data[$i]['speciality'] = $doctor->speciality;
                $data[$i]['location'] = $doctor->location;
                $data[$i]['age'] = $doctor->age;
                $data[$i]['doctor_no'] = $doctor->doctor_no;
                $i++;
            }

            $resp = array('msg'=>'Doctor Found','data'=>$data);
        }

        return json_encode($resp);
    }

    public function DeleteDoctors(Request $request)
    {
        $this->_login->deleteDoctor($request->doctor_id);
        return json_encode(array('msg'=>'Doctor Deleted Successfully'));
    }

    public function ListMealType(Request $request)
    {
        if(!empty($request->meal_type_id))
        {
            $id = $request->meal_type_id;
        }
        else
        {
            $id = '';
        }

        $meal_type_data = $this->_login->listMealType($id);

        if(empty($meal_type_data))
        {
            $resp = array('msg'=>'Meal Type Not Found','data'=>null);
        }
        else
        {
            $i = 0;

            foreach($meal_type_data as $meal_type)
            {
                $data[$i]['meal_type_id'] = $meal_type->meal_type_id;
                $data[$i]['meal_type'] = $meal_type->meal_type;
                $data[$i]['meal_type_code'] = $meal_type->meal_type_code;
                $data[$i]['requirement'] = $meal_type->requirement;
                $i++;
            }

            $resp = array('msg'=>'Meal Type Found','data'=>$data);
        }

        return json_encode($resp);
    }

    public function ManageMealType(Request $request)
    {
        if(empty($request->meal_type_id) || empty($request->requirement))
        {
            $data = array('data'=>null,'msg'=>'Required Parameter Missing');
        }
        else
        {
            $admin_data = array();
            $admin_data['requirement'] = $request->requirement;
            $admin_data['updated_on'] = time();
            $data = $this->_login->updateMealTypeMaster($admin_data,$request->meal_type_id);
        }

        return json_encode($data);
    }

    public function ListFaqCategory(Request $request)
    {
        if(!empty($request->faq_category_id))
        {
            $id = $request->faq_category_id;
        }
        else
        {
            $id = '';
        }

        $faq_category_data = $this->_login->listFaqCategory($id);

        if(!empty($faq_category_data))
        {
        	$i = 0;

        	foreach($faq_category_data as $cat_data)
        	{
        		$data[$i]['id'] = $cat_data->faq_category_id;
        		$data[$i]['name'] = $cat_data->faq_category;
        		$i++;
        	}
        	
        }

        if(empty($data))
        {
        	$resp = array('msg'=>'No Data Found','data'=>null);
        }
        else
        {
        	$resp = array('msg'=>'Data Found','data'=>$data);
        }

        return json_encode($resp);
    }

    public function ManageFaqCategory(Request $request)
    {
    	if(!empty($request->faq_category_id))
    	{
    		$id = $request->faq_category_id;
    	}
    	else
    	{
    		$id = '';
    	}

    	$data = array('faq_category'=>$request->faq_category);

    	$resp = $this->_login->manageFaqCategory($data,$id);

    	return json_encode($resp);
    }

    public function DeleteFaqCategory(Request $request)
    {
        $this->_login->deleteFaqCategory($request->faq_category_id);
        return json_encode(array('msg'=>'Faq Category Deleted Successfully'));
    }

    public function PatientFactor(Request $request)
    {
        if(empty($request->patient_id))
        {
            $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
        }
        else
        {
            $patient = DB::table('patient_factor as p')
                            ->join('patients', 'patients.patientID', '=', 'p.patientID')
                            ->join('feeds', 'p.feedID', '=', 'feeds.id')
                            ->join('healthstatus', 'p.healthStatusID', '=', 'healthstatus.id')
                            ->where('p.patientID',$request->patient_id)
                            ->get();
            
            if(!empty($patient))
            {
                $data['patient_id'] = $patient[0]->patientID;
                $data['first_name'] = $patient[0]->firstName;
                $data['last_name'] = $patient[0]->lastName;
                $data['phone'] = $patient[0]->phone;
                $data['age'] = $patient[0]->age;
                $data['weight'] = $patient[0]->weight;
                $data['height'] = $patient[0]->height;
                $data['gender'] = $patient[0]->gender;
                $data['healthStatusID'] = $patient[0]->healthStatusID;
                $data['status'] = $patient[0]->status;
                $data['feedID'] = $patient[0]->feedID;
                $data['name'] = $patient[0]->name;
                $data['diabetes'] = $patient[0]->diabetes;
                $data['albuminFactor'] = $patient[0]->albuminFactor;
                $data['requiredCalories'] = $patient[0]->requiredCalories;
                $data['requiredProtein'] = $patient[0]->requiredProtein;
                $data['requiredCarbs'] = $patient[0]->requiredCarbs;
                $data['requiredFats'] = $patient[0]->requiredFats;
                $data['BMI'] = $patient[0]->BMI;

                $resp = array('msg'=>'Patient Factor Found','data'=>$data);
            }
            else
            {
                $resp = array('msg'=>'No Data Found','data'=>null);
            }
        }

        echo json_encode($resp);
        exit;
    }

    public function CheckAppVersion()
    {
    	$app_version = DB::table('tbl_setting')
                    ->where('setting_id',1)
                    ->get();

        if(!empty($app_version[0]->app_version))
        {
        	$resp = array('msg'=>'App Version Found','data'=>$app_version[0]->app_version);
        }
        else
        {
        	$resp = array('msg'=>'No App Version Found','data'=>null);
        }
        
        echo json_encode($resp);
    }

    public function ChangeAppVersion(Request $request)
    {
    	DB::table('tbl_setting')->where('setting_id',1)->update(array('app_version'=>$request->app_version));
    	
    	if(empty($request->app_version))
        {
        	$resp = array('msg'=>'Mandatory Data Found','data'=>null);
        }
        else
        {
        	$resp = array('msg'=>'App Version Updated','data'=>null);
        }
        
        echo json_encode($resp);
    } 

    public function OfferMessage(Request $request)
    {
    	if(empty($request->mobile_no))
    	{
    		$resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
    	}
    	else
    	{
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
	        $msg = "Thank you for sharing jeevini i-NOS to people you care. 

Your coupon code to get Rs 100/- off on your formula-feed is 3124";
	        $client = new Client($sid, $token);
	        $client->messages->create(
	            "+".$request->mobile_no ,
	            array(
	                'from' => $from_phone_number,
	                "body" => $msg,
	            )
	        );

	        $resp = array('msg'=>'Message Send Successfully','data'=>null);
    	}

    	echo json_encode($resp);
    }

    public function ListBmiFactorReference()
    {
        $bmi_factor_data = DB::table('bmi_factor_reference')->get();

        if(empty($bmi_factor_data))
        {
            $resp = array('msg'=>'No BMI Factor Reference Found','data'=>null);
        }
        else
        {
            $i = 0;

            foreach($bmi_factor_data as $bmi)
            {
                $data[$i]['id'] = $bmi->id;
                $data[$i]['min'] = $bmi->Min;
                $data[$i]['max'] = $bmi->Max;
                $data[$i]['general'] = $bmi->General;
                $data[$i]['pregnant'] = $bmi->Pregnant;
                $i++;
            }

            $resp = array('msg'=>'BMI Factor Reference Found','data'=>$data);
        }

        echo json_encode($resp);
        exit;
    }

    public function UpdateBmiFactorReference(Request $request)
    {
        if(empty($request->id) || empty($request->min) || empty($request->max) || empty($request->general) || empty($request->pregnant))
        {
            $resp = array('msg'=>'Mandatory Parameters Missing','data'=>null);
        }
        else
        {
           DB::table('bmi_factor_reference')->where('id',$request->id)->update(array('Min'=>$request->min,'Max'=>$request->max,'General'=>$request->general,'Pregnant'=>$request->pregnant));
           
           $resp = array('msg'=>'BMI Factor Reference Updated Successfully','data'=>null); 
        }
        
        echo json_encode($resp);
        exit;
    }

    public function ListRequiredCalorieReference()
    {
        $calorie_factor_data = DB::table('required_calorie_reference')->get();

        if(empty($calorie_factor_data))
        {
            $resp = array('msg'=>'No Calorie Factor Reference Found','data'=>null);
        }
        else
        {
            $i = 0;

            foreach($calorie_factor_data as $calorie)
            {
                $data[$i]['id'] = $calorie->id;
                $data[$i]['feed_id'] = $calorie->feedID;
                $data[$i]['age_dependant'] = $calorie->AgeDependant;
                $data[$i]['gender'] = $calorie->gender;
                $data[$i]['PregnancyDependant'] = $calorie->PregnancyDependant;
                $data[$i]['MinAge'] = $calorie->MinAge;
                $data[$i]['MaxAge'] = $calorie->MaxAge;
                $data[$i]['ValueMultiplication'] = $calorie->ValueMultiplication;
                $data[$i]['ValueAddition'] = $calorie->ValueAddition;
                $i++;
            }

            $resp = array('msg'=>'Clorie Factor Reference Found','data'=>$data);
        }

        echo json_encode($resp);
        exit;
    }

    public function UpdateRequiredCalorieReference(Request $request)
    {
        if(empty($request->id) || empty($request->feedID) || empty($request->AgeDependant) || empty($request->gender) || empty($request->PregnancyDependant) || empty($request->MinAge) || empty($request->MaxAge) || empty($request->ValueMultiplication))
        {
            $resp = array('msg'=>'Mandatory Parameters Missing','data'=>null);
        }
        else
        {
           DB::table('required_calorie_reference')->where('id',$request->id)->update(array('feedID'=>$request->feedID,'AgeDependant'=>$request->AgeDependant,'gender'=>$request->gender,'PregnancyDependant'=>$request->PregnancyDependant,'MinAge'=>$request->MinAge,'MaxAge'=>$request->MaxAge,'ValueMultiplication'=>$request->ValueMultiplication,'ValueAddition'=>$request->ValueAddition));
           
           $resp = array('msg'=>'Calorie Factor Reference Updated Successfully','data'=>null); 
        }
        
        echo json_encode($resp);
        exit;
    }

    public function singlePatientDetails(Request $request){
        $patientID = $request->header('patientID');
        $data =  $this->_login->singlePatientDetails($patientID);
        return $resp = array('status'=>'1','data'=>$data); 
    }

    public function getPatientId(Request $request)
    {
        $mob_num=$request->input('mob_num');
        $status=$this->_login->getPatientId($mob_num);
        if($status)
        {
            return json_encode(array("status"=>$status));
        }
        else{
            return ["status"=>"failed to fetch patient details"];
        }
    }

    public function getTabletName(Request $request)
    {
        $tabletName=DB::Table('tablet_name')->first();
        return ["tabletName"=>$tabletName];
    }

    public function getRoles(Request $request)
    {
        $mobile_no = $request->mobile_no;
    $data = $this->_login->getRoles($mobile_no);
    if (!empty($data)) {
            $response = [
                'data' => $data,
            ];
        return json_encode($response);
    }
    else {
        $response = [
                'message' => 'no data',
                'key' => 0,
            ];
        return json_encode($response);
    }
    }
}
