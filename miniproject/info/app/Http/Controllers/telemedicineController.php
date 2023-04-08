<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class telemedicineController extends Controller
{
    public function info_from_customers(Request $request)
    {
        $CallSid=$request->input('CallSid');
        $PatientName=$request->input('PatientName');
        $mobile_no=$request->input('mobile_no');
        $tablets=$request->input('tablets');
        $isTablets=$request->input('isTablets');
        $noOfTabs=$request->input('noOfTabs');
        $isInsulin=$request->input('isInsulin');
        $Mg=$request->input('Mg');
        $times=$request->input('times');
        $age=$request->input('age');
        $gender=$request->input('gender');
        $city=$request->input('city');
        $Diabeties_duration=$request->input('Diabeties_duration');
        $FBS=$request->input('FBS');//A fasting blood sugar level
        $PPBS=$request->input('PPBS');//Blood Sugar Post Prandial (BSPP) test
        $HBA1C=$request->input('HBA1C');//A hemoglobin A1C (HbA1C) test is a blood test that shows what your average blood sugar level
        $RBS=$request->input('RBS');//Random blood sugar (RBS) measures blood glucose regardless of when you last ate.
        $date=$request->input('date');
        $stage=$request->null;

        $BT1=false;
        $BT2=false;
        $BT3=false;
        $BT4=false;
        $BT5=false;

        if(($FBS<=100)&&($HBA1C<=6.5)&&($PPBS<=200)||($RBS<=200))
        {
            $BT1=true;
        }
        else if(($FBS<=150)&&($HBA1C<=7.5)&&($PPBS<=300)||($RBS<=300))
        {
            $BT2=true;
        }
        else if(($FBS<=200)&&($HBA1C<=8.5)&&($PPBS<=400)||($RBS<=400))
        {
            $BT3=true;
        }
        else if(($FBS<=300)&&(HBA1C<=9)&&($PPBS<=500)||($RBS<=500))
        {
            $BT4=true;
        }
        else if(($FBS>300)&&($HBA1C>9)&&($PPBS>500)||($RBS>500))
        {
            $BT5=true;
        }
        else{
            if($PPBS!='empty')
            {
                if($PPBS<=200)
                {
                    $BT1=true;
                }
                else if($PPBS<=300)
                {
                    $BT2=true;
                }
                else if($PPBS<=400)
                {
                    $BT3=true;
                }
                else if($PPBS<=500)
                {
                    $BT4=true;
                }
                else
                {
                    $BT5=true;
                }
            }
            if($PPBS=='empty' && $RBS!='empty')
            {
                if($RBS<=200)
                {
                    $BT1=true;
                }
                else if($RBS<=300)
                {
                    $BT2=true;
                }
                else if($RBS<=400)
                {
                    $BT4=true;
                }
                else if($RBS<=500)
                {
                    $BT5=true;
                }
                else
                {
                    $BT5=true;
                }
            }
            else if($PPBS=='empty' && $RBS=='empty' && $HBA1C!='empty')
            {
                if($HBA1C<=6.5)
                {
                    $BT1=true;
                }
                else if($HBA1C<=7.5)
                {
                    $BT2=true;
                }
                else if($HBA1C<=8.5)
                {
                    $BT3=true;
                }
                else if($HBA1C<=9)
                {
                    $BT4=true;
                }
                else{
                    $BT5=true;
                }
            }
            else if(($PPBS=='empty') && ($RBS=='empty') && ($FBS!='empty'))
            {
                if($FBS<100)
                {
                    $BT1=true;
                }
                else if($FBS<=150)
                {
                    $BT2=true;
                }
                else if($FBS<=200)
                {
                    $BT3=true;
                }
                else if($FBS<=300)
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
        else
        {
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
            else if($noOfTabs==1)
            {
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
                        else if($BT4=='true'||$BT5=='true')
                        {
                            $stage=4;
                        }
                    }
                    else
                    {
                        if($BT1='true'||$BT2=='true')
                        {
                            $stage=3;
                        }
                        else if($BT4=='true'||$BT5=='true'||$BT3=='true')
                        {
                            $stage=4;
                        }
                    }
                }
             }
            }
             else
             {
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
                else {
                    $stage=null;
                }
             }
        }

        $status=\DB::table('info_from_customers')->insert(
            array(
                'CallSid'=>$CallSid,
                'PatientName'=>$PatientName,
                'mobile_no'=>"91".$mobile_no,
                'tablets'=>json_encode($tablets),
                'Mg'=>json_encode($Mg),
                'times'=>json_encode($times),
                'age'=>$age,
                'gender'=>$gender,
                'city'=>$city,
                'Diabeties_duration'=>$Diabeties_duration,
                'FBS'=>$FBS,
                'PPBS'=>$PPBS,
                'HBA1C'=>$HBA1C,
                'RBS'=>$RBS,
                'date'=>$date,
                'isTablets'=>$isTablets,
                'noOfTabs'=>$noOfTabs,
                'isInsulin'=>$isInsulin,
                'stage'=>$stage
            )
            );

            if($status)
            {
                return[
                    "status"=>"success",
                ];
            }
            else
            {
                return[
                    "status"=>"failed to insert",
                ];
            }
    }


    public function doctor_form(Request $request)
    {

        $raw_data = $request->json()->all();
        $CallSid=$raw_data['CallSid'];
        // $token_id = $raw_data['token_id'];
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
                   "Diabeties_duration"=>$raw_data["Diabeties_duration"],
                //    "token_id" => $raw_data['token_id'],
                   "mobile_no" => $raw_data['mobile_no'],
                   "PatientName" => $raw_data['name'],
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

        // $chair_squats=$raw_data['chair_squats'];
        // $chair_leg_raise=$raw_data['chair_leg_raise'];
        // $chair_shoulder_raise=$raw_data['chair_shoulder_raise'];
        // $half_squats=$raw_data['half_squats'];
        // $stair_climb =$raw_data['stair_climb'];


        // $insert_exercise_data = array
        // (
        //     "CallSid"=>$CallSid,
        //     // "token_id"=>$token_id,
        //     "mobile_no"=>$mobile_no,
        //     "chair_squats"=>$chair_squats,
        //     "chair_leg_raise"=>$chair_leg_raise,
        //     "chair_shoulder_raise"=>$chair_shoulder_raise,
        //     "half_squats"=>$half_squats,
        //     "stair_climb"=>$stair_climb
        // );

        $submitted = array(

            "CallSid"=>$CallSid,
            "CallFrom" => $raw_data['mobile_no'],

        );

        // $ID = $this->_dietitiansupport->exercise_insert($insert_exercise_data);
        $ID1 = \DB::table('doctor_form')->insert($insert_doctor_form);
        // $ID2= $this->_dietitiansupport->submitted_details($submitted);
        return array('msg'=>"inserted successfully");

      }
    public function patientDetails(Request $request)
    {
        // $id=$request->input('id');
        $PatientName=$request->input('PatientName');
        // $getpatient=\DB::table('info_from_customers')->where('id',$id)->first();
        $getpatient=\DB::table('info_from_customers')->where('PatientName',$PatientName);
        return $getpatient;
    }
}
