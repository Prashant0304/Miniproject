<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use  App\Models\Login;
use  App\Models\Feeds;
use App\Helpers\Calculation;
use Illuminate\Support\Facades\Storage;

class telemedicineController extends Controller

{
  public function __construct(){
    $this->_login = new Login;
    $this->_calculation = new Calculation; 
    $this->_feed = new Feeds;
}
  public function info_for_customerSupport(Request $request) 
  {
    //taking the input data from the users
      $Tokenid= uniqid();
      $patient_id=$request->input('patientID');
      $name=$request->input('name');
      $age=$request->input('age');
      $number=$request->input('number');
      $healthStatusID=$request->input('healthStatusID');
      $diabetes=$request->input('diabetes');
      $diseses=$request->input('diseses');
      $date=$request->input('date');
      $time=$request->input('time');
      

 
     //Query to insert the details into the info_for_customerSupport table
      DB::table('info_for_customerSupport')->insert(
          array('Tokenid' => $Tokenid,
                'patientID'=>$patient_id,
                'name' => $name,
                'age'=> $age,   
                'number'=> $number,
                'healthStatusID' => $healthStatusID,
                'diabetes' => $diabetes,
                'diseses'=> $diseses,
                'date' => $date,
                'time' => $time,
                'status'=>'pending'
                )
      );
     return "success";
      
  }

  public function  customerSupport()//this function is used to fetch the data for customer support
  {
    $details = DB::table('info_for_customerSupport')->select(

      'Tokenid','name','age','number','healthStatusID', 'diabetes', 'diseses','date', 'time'
    )
    ->where('status','pending')
    ->get();
    
    return response()->json($details);
  }

  
public function info_from_customer(Request $request)
{
  $CallSid=$request->input('CallSid'); //Priya
  $Tokenid=uniqid();

  $PatientName=$request->input('PatientName');
  $mobile_no =$request->input('mobile_no');

  $tablets=$request->input('tablets');
  $Mg=$request->input('Mg');
  $times=$request->input('times');

  $age=$request->input('age');

  $isInsulin=$request->input('isInsulin');
  $isTablets=$request->input('isTablets');
  $noOfTabs=$request->input('noOfTabs');
  $noOfInsulin=$request->input('noOfInsulin');

  $gender=$request->input('gender');	
  $city=$request->input('city'); 
  $height=$request->input('height'); 
  $weight=$request->input('weight'); 
  $profession=$request->input('profession'); 
  $Diabetes_duration=$request->input('Diabetes_duration');
  $FBS=$request->input('FBS'); 
  $PPBS=$request->input('PPBS'); 
  $HBA1C=$request->input('HBA1C'); 
  $RBS=$request->input('RBS'); 

  $insulin_injection=$request->input('insulin_injection');
  $morning_iu=$request->input('morning_iu');
  $afternoon_iu=$request->input('afternoon_iu');
  $evening_iu=$request->input('evening_iu');

  $thyroid=$request->input('thyroid');
  $bp=$request->input('bp');
  $burning_foot=$request->input('burning_foot');
  $patientComplent=$request->input('patientComplent');
  
  if($request->hasFile('Prescription_image')){
    $file = $request->file('Prescription_image');
      $prescriptionImage=  time() . $file->getClientOriginalName();
      $filePath = 'PrescriptionImages/' . $prescriptionImage;
      Storage::disk('s3')->put($filePath, file_get_contents($file));
  }
       if($request->hasFile('Blood_test_image')){
        $file = $request->file('Blood_test_image');
         $BloodTestImage=  time() . $file->getClientOriginalName();
         $filePath = 'BloodTestImages/' . $BloodTestImage;
         Storage::disk('s3')->put($filePath, file_get_contents($file)); } 
         $date=$request->input('date');
        //  $insString='';
        //  foreach($insulin_injection as $insulin)
        //  {
        //    $insString=$insString.$insulin.',';
        //  }
        //   echo $insString;

         $BT1=false;
         $BT2=false;
         $BT3=false;
         $BT4=false;
         $BT5=false;

         if(($FBS>0&&$FBS<100)&&($HBA1C>0&&$HBA1C<6.5)&&(($PPBS>0&&$PPBS<200)||($RBS>0&&$RBS<200)))
         {
             $BT1=true;
            
         }
         else if(($FBS>=101&&$FBS<=150)&&($HBA1C>=6.5&&$HBA1C<=7.5)&&(($PPBS>=201&&$PPBS<=300)||($RBS>=201&&$RBS<=300)))
         {
             $BT2=true; 
         }
         else if(($FBS>=151&&$FBS<=200)&&($HBA1C>=7.6&&$HBA1C<=8.5)&&(($PPBS>=301&&$PPBS<=400)||($RBS>=301&&$RBS<=400)))
         {
             $BT3=true; 
         }

         else if(($FBS>=201&&$FBS<=300)&&($HBA1C>=8.6&&$HBA1C<=9)&&(($PPBS>=401&&$PPBS<=500)||($RBS>=401&&$RBS<=500)))
         {
             $BT4=true; 
         }
         else if(($FBS>300)&&($HBA1C>9)&&(($PPBS>500)||($RBS>500)))
         {
             $BT5=true;
         }
         else{
          if($PPBS!='empty')
          {
            if($PPBS<200)
            {
              $BT1=true;
            }
            else if($PPBS>=201&&$PPBS<=300)
            {
              $BT2=true;
            }
            else if($PPBS>=301&&$PPBS<=400)
            {
              $BT3=true;
            }
            else if($PPBS>=401&&$PPBS<=500)
            {
              $BT4=true;
            }
            else{
              $BT5=true;
            }
          }
          if($PPBS=='empty'&&$RBS!='empty')
          {
            if($RBS<200)
            {
              $BT1=true;
            }
            else if($RBS>=201&&$RBS<=300)
            {
              $BT2=true;
            }
            else if($RBS>=301&&$RBS<=400)
            {
              $BT3=true;
            }
            else if($RBS>=401&&$RBS<=500)
            {
              $BT4=true;
            }
            else{
              $BT5=true;
            }
          }
          else if($PPBS=='empty'&&$RBS=='empty'&&$HBA1C!='empty')
          {
            if($HBA1C<6.5)
            {
              $BT1=true;
            }
            else if($HBA1C>=6.5&&$HBA1C<=7.5)
            {
              $BT2=true;
            }
            else if($HBA1C>=7.6&&$HBA1C<=8.5)
            {
              $BT3=true;
            }
            else if($HBA1C>=8.6&&$HBA1C<=9)
            {
              $BT4=true;
            }
            else{
              $BT5=true;
            }
          }
          else if($PPBS=='empty'&&$RBS=='empty'&&$HBA1C=='empty'&&$FBS!='empty')
          {
            if($FBS<100)
            {
              $BT1=true;
            }
            else if($FBS>=101&&$FBS<=150)
            {
              $BT2=true;
            }
            else if($FBS>=151&&$FBS<=200)
            {
              $BT3=true;
            }
            else if($FBS>=201&&$FBS<=300)
            {
              $BT4=true;
            }
            else{
              $BT5=true;
            }
          }
         }      

         if($isInsulin=='true')
         {
          $stage=4;
         }
         else{
          if($isTablets=='true')
          {
              if($noOfTabs>=3)
              {
                $stage=4;
              }
              else if($noOfTabs==2)
              {
                if($BT4==true||$BT5==true)
                {
                    $stage=4;
                }
                else{
                  $stage=3;
                }
              }
             else if($noOfTabs==1){
                foreach($times as $times)
                {
                  $time=$times;
                  if($time==1)
                  {
                    if($BT1=='true'||$BT2=='true')
                    {
                      $stage=2;
                    }
                    else if($BT3=='true')
                    {
                      $stage=3;
                    }
                    else if($BT4=='true'||($BT5=='true')){
                      $stage=4;
                    }
                  }
                  else{
                    if($BT1=='true'||$BT2=='true')
                    {
                      $stage=3;
                    }
                    else if($BT4=='true'||($BT5=='true')||($BT3=='true')){
                      $stage=4;
                    }
                  }
                }
              }
          }
          else{
              if($BT1=='true')
              {
                $stage=0;
              }
              else if($BT2=='true')
              {
                $stage=1;
              }
              else if($BT3=='true')
              {
                $stage=2;
              }
              else if($BT4=='true')
              {
                $stage=3;
              }
              else if($BT5=='true')
              {
                $stage=4;
              }
              else{
                $stage=null;
              }
          }
         }
  
$status=DB::table('info_from_customers')->insert(
      array(
          'CallSid'=>$CallSid,//Priya
          'token_id' => $Tokenid,
          'PatientName' => $PatientName,
            'mobile_no'=> "91".$mobile_no,   
            'tablets'=> json_encode($tablets),
            'Mg'=>json_encode($Mg),
            'times'=>json_encode($times),
            'age' => $age,
            'gender' => $gender,
            'city'=> $city,
            'height'=>$height,
            'weight'=>$weight,
            'profession'=>$profession,
            'Diabetes_duration'=> $Diabetes_duration,
            'FBS'=>$FBS,
            'PPBS'=>$PPBS,
            'HBA1C'=>$HBA1C,
            'RBS'=>$RBS,
            'insulin_injection'=>json_encode($insulin_injection),
            'morning_iu'=>json_encode($morning_iu),
            'afternoon_iu'=>json_encode($afternoon_iu),
            'evening_iu'=>json_encode($evening_iu),
            'thyroid'=>$thyroid,
            'bp'=>$bp,
            'burning_foot'=>$burning_foot,
            'patientComplent'=>$patientComplent,
            //'Prescription_image'=> $prescriptionImage,
            //'Blood_test_image' => $BloodTestImage,
            'date' => $date,
            'isTablets'=>$isTablets,
            'isInsulin'=>$isInsulin,
            'noOfTabs'=>$noOfTabs,
            'noOfInsulin'=>$noOfInsulin,
            'stage'=>$stage
         //   'createdAt'=>$createdAt,
           // 'updatedAt'=>$updatedAt
            )
  );
 
  $status1=DB::table('info_from_customers1')->insert(
    array(
      'CallSid'=>$CallSid,//Priya
      'token_id' => $Tokenid,
      'PatientName' => $PatientName,
        'mobile_no'=> "91".$mobile_no,   
        'tablets'=> json_encode($tablets),
        'Mg'=>json_encode($Mg),
        'times'=>json_encode($times),
        'age' => $age,
        'gender' => $gender,
        'city'=> $city,
        'height'=>$height,
        'weight'=>$weight,
        'profession'=>$profession,
        'Diabetes_duration'=> $Diabetes_duration,
        'FBS'=>$FBS,
        'PPBS'=>$PPBS,
        'HBA1C'=>$HBA1C,
        'RBS'=>$RBS,
        'insulin_injection'=>json_encode($insulin_injection),
        'morning_iu'=>json_encode($morning_iu),
        'afternoon_iu'=>json_encode($afternoon_iu),
        'evening_iu'=>json_encode($evening_iu),
        'thyroid' => $thyroid,
        'bp'=>$bp,
        'burning_foot'=>$burning_foot,
        'patientComplent'=>$patientComplent,
        //'Prescription_image'=> $prescriptionImage,
        //'Blood_test_image' => $BloodTestImage,
        'date' => $date,
        'isTablets'=>$isTablets,
        'isInsulin'=>$isInsulin,
        'noOfTabs'=>$noOfTabs,
        'noOfInsulin'=>$noOfInsulin,
        'stage'=>$stage
      //  'createdAt'=>$createdAt,
       // 'updatedAt'=>$updatedAt
        
        )
);
if($status && $status1)
{
  return [
    "status"=>"success",
  ];
}
else{
  return [
    "status"=>"failed to insert",
  ];
}
 
}


public function UpdatePatientDetails(Request $request)
{
  $patientID=$request->input('patientID');
  $weight = $request->input('weight');
  $height = $request->input('height');
  $age = $request->input('age');
  $healthStatusID = $request->input('healthStatusID');
  $gender = $request->input('gender');
  $diabetes = $request->input('diabetes');
  $doctorID = $request->input('doctorID');
  $firstName = $request->input('firstName');
  $lastName = $request->input('lastName');
  $phone= $request->input('phone');
  $language = $request->input('language');
  $locationID = $request->input('locationID');
  
  $insert_patient_data = array(
          
          "firstName" => $firstName,
          "lastName" => $lastName,
          "phone" =>  $phone,
          "language" => $language,
          "locationID" => $locationID,
          "gender" => $gender,
          "doctorID" => $doctorID,

  );
  $patient= $this->_login->UpdatePatients($insert_patient_data,$patientID);  
  $BMI = ($weight/$height)/$height;      
  $insert_patient_factor = array(
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
   $insertPatientFactor = $this->_login->UpdatePatientFactor($insert_patient_factor,$patientID);
  //To Assign Feed To Patient 
  // $patientID = '42';
  $assignFeedID = $this->_calculation->assignFeedID($patientID);
  if ($healthStatusID > 1) {
      $requiredCalories = $this->_calculation->requiredCaloriesCalculation($patientID);
  }
 
  $patientDetails = $this->_login->getPatientDetail($patientID);
  
  return json_encode("Updated Successfull");
}

//update profile picture
public function updateprofilepic(Request $request)
{
  $this->validate($request, [
    'patientID' =>'required',
    'profile' => 'required'
   
]);
  $patient_id=$request->input('patientID');
  if($request->hasFile('profile')){
    $file = $request->file('profile' );
    $profile = time() . $file->getClientOriginalName();
    $filePath = 'PatientProfile/' . $profile;
    Storage::disk('s3')->put($filePath, file_get_contents($file));

}
DB::table('patients')->where('patientID',$patient_id)->update(
  array(
     
        'profile'=> $profile,
       
        
        )
);
return "success";
}

public function viewProfile(Request $request)
{
  $patient_id=$request->input('patientID');
  
  $imageData=DB::table('patients')->select('profile')
  ->where('patientID',$patient_id)
  ->first();
  $profile= Storage::disk('s3')->url('PatientProfile/' . $imageData->profile);
  return response($profile);
  //return view('welcome',compact('imageData'));
}
public function viewformDetails(Request $request)
{
 $patient_id=$request->input('ID');
  
  $imageData=DB::table('info_from_customers')->select('ID','patientName','mobile_no','medicines','age','gender','city','Diabetes_duration','knee_join_pain','patientComplent','Prescription_image','Blood_test_image','date')
  ->where('ID',$patient_id)
  ->first();
  
  return response()->json($imageData);
}

public function  infofromcustomerlist(Request $request)//this function is used to fetch the data for customer support
  {
    $patient_id=$request->input('ID');
    if(empty($patient_id))
    {
      $details = DB::table('info_from_customers')->get();
    }
    else{
      $details=DB::table('info_from_customers')->select('ID','token_id','patientName','mobile_no','medicines','age','gender','city','height','weight','profession','Diabetes_duration',
      'FBS','PPBS','HBA1C','RBS','insulin_injection','morning_iu','afternoon_iu','evening_iu','knee_join_pain','patientComplent','Prescription_image','Blood_test_image','token_status','isAttended','date')
  ->where('ID',$patient_id)
  ->first();
    }
    
     return response()->json($details);
  }

public function contacts(Request $request){

  //recheck
  
  $arr1=$request->input('phone');

  $i=0; 

 $arr2=DB::table('patients')->get()->pluck('phone');
 

for($i=0;$i<count($arr1);$i++){
  for($j=0;$j<count($arr2);$j++){
  if($arr1[$i]==$arr2[$j]){
    $final[$i]=$arr1[$i];
    break;
  }
  else{
    $notin[$i]=$arr1[$i];
    
  }
}

}
if(isset($final)){
return response()->json($final);
}
else{
  return 0;
}





}


}
