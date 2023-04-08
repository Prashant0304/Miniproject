<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    public function insertOTP($data){
        // print_r($data);exit;
        // print_r($data);
        $getID = DB::table('unverified_patient')->insertgetID($data);
       
    }

    public function addPatients($insert_data){
            $insertGet = DB::table('patients')->insertgetID($insert_data);
          return $insertGet;
    }
    public function verifyCodeSignup($code, $phone){
        
            $there = DB::table('unverified_patient')->select('*')->where('phone',$phone)->where('code',$code)->first();
           if (!empty($there)) {
              
            return 1;
           }
           else {
              
               return 0;
           }
        }

        
    public function verifyCodeLogin($code, $phone ){ 
        
            $there = DB::table('patients as p')
                    ->select('p.patientID',"p.firstName","p.lastName","p.phone","p.language","p.locationID","p.patientID","p.gender","p.doctorID","p.physioID","p.dieticianID",'pf.feedID')
                    ->leftJoin('patient_factor as pf', 'p.patientID', '=', 'pf.patientID')
                    ->where('p.phone',$phone)
                    ->where('p.code',$code)
                    ->first();
            if (!empty($there)) {
                
                return $there;
            }
            else {
                $data = array();
                return $data;
            }
        }
        
        public function check_existance($phone){
            $exist = DB::table('patients')->select('*')->where('phone',$phone)->first();
            if (!empty($exist)) {
                $data = DB::table('patients')->select('*')->where('phone',$phone)->first();
                return 1;
            }
            else {
                
                return 0;
            }
        }

        public function updateLoginOTP($code, $phone ){
            $insert = DB::table('patients')->where('phone',$phone)->update(array('code'=>$code));
            
        }

        public function insertHealthStatusmodel($status){
           $insert =  DB::table('healthStatus')->insertgetID($status);
            if($insert  !== null) {
                return 1;
            }
            else 
            {
                return 0;
            }
        }
        public function getHealthStatusName($healthStatusID){
            $name = DB::table('healthstatus')->select('status')->where('id',$healthStatusID)->first();
            $status = $name->status; 
            return $status;
        }

        public function getFeedName($feedID){
            $name = DB::table('feeds')->select('name')->where('id',$feedID)->first();
            return $name->name;
        }
        public function insertPatientFactor($insert_patient_factor){
            $insert = DB::table('patient_factor')->insert($insert_patient_factor);
        }

        public function getPatientDetail($patientID){
            $details = DB::table('patient_factor')->select('*')->where('patientID', $patientID)->first();
            return $details;
        }

        public function updateFeedID($patientID, $feedID){
            $updateGetID = DB::table('patient_factor')->where('patientID',$patientID)->update(array('feedID'=>$feedID));
            return $updateGetID;
        }

        public function allPatients($id='')
        {
            if(empty($id))
            {
                $patients = DB::table('patients as p')
                        ->select('p.patientID',DB::raw("CONCAT(p.firstName,' ',p.lastName) as fullname"),'p.phone','p.gender','pf.age','pf.weight','pf.height','p.locationID','st.name as state','l.city','p.language','pf.feedID','f.name as feedName','pf.healthStatusID','pf.diabetes','pf.albuminFactor','pf.requiredCalories','pf.requiredProtein','pf.requiredCarbs','pf.requiredFats','pf.BMI','p.createdAt as onboardingTime','p.doctorID','d.name as doctorName','p.physioID','p.dieticianID','p.updatedAt as lastLogin')
                        ->leftJoin('patient_factor as pf','p.patientID','pf.patientID')
                        ->leftJoin('cities as l','l.id','p.locationID')
                        ->leftJoin('states as st','l.state_id','st.id')
                        ->leftJoin('doctor as d','p.doctorID','d.doctor_id')
                        ->leftJoin('feeds as f','f.id','pf.feedID')
                        // ->select("p.patientID","p.firstName","p.lastName","p.phone")
                        // ->leftJoin('patient_factor', 'p.patientID', '=', 'patient_factor.patientID')
                        // ->leftJoin('patient_factor', 'p.patientID', '=', 'patient_factor.patientID')
                        
                        ->get();
            }
            else
            {
                $patients = DB::table('patients as p')
                        ->join('patient_factor', 'p.patientID', '=', 'patient_factor.patientID')
                        ->where('p.patientID',$id)
                        ->get();
            }
            
            return $patients;
        }

        public  function singlePatient($patientID){
             $patient = DB::table('patients as p')                            
                            ->join('patient_factor', 'p.patientID', '=', 'patient_factor.patientID')
                            //->join('patient_factor','h.id','patient_factor.healthStatusID')
                            ->where('p.patientID',$patientID)
                            ->get();
                return $patient;
        }

        public function insertPegnantStatus($data){
            $insertID = DB::table('pregnant_patient')->insertgetID($data);
            return $insertID;
        }
        
        /* Rohan code started */

        public function manageCsv($reference_data,$version_no)
        {
            $is_exist = DB::table('meal_reference_json')
                            ->where('version',$version_no)
                            ->first();

            if(empty($is_exist->meal_reference_json_id))
            {
                $insertID = DB::table('meal_reference_json')->insertgetID($reference_data);
                return $insertID;
            }
            else
            {
                unset($reference_data['meal_reference_json_id']);
                DB::table('meal_reference_json')->where('meal_reference_id',$is_exist->meal_reference_json_id)->update($reference_data);
            }
        }

        public function manageMessageCsv($message_data)
        {
            $is_exist = DB::table('meal_reference_json')
                            ->where('version',$version_no)
                            ->first();

            if(empty($is_exist->meal_reference_json_id))
            {
                $insertID = DB::table('meal_reference_json')->insertgetID($reference_data);
                return $insertID;
            }
            else
            {
                unset($reference_data['meal_reference_json_id']);
                DB::table('meal_reference_json')->where('meal_reference_id',$is_exist->meal_reference_json_id)->update($reference_data);
            }
        }

        public function getMealReference($versionID){
            $details = DB::table('meal_reference_json')->select('*')->orderBy('meal_reference_json_id','desc')->first();
            $version=$details->version ;
            if(!empty($details->version) && $details->version!=$versionID)
            {
                $json_data = json_decode($details->json_data,true);
                $resp_data = array('is_updated'=>'Y','Version'=>$version,'data'=>$json_data);
            }
            else
            {
                $resp_data = array('is_updated'=>'N','data'=>null);
            }

            return $resp_data;
        }

        public function getFeedId($feed_name)
        {
            $is_exist = DB::table('feeds')
                            ->where('name',$feed_name)
                            ->first();

            if(!empty($is_exist->name))
            {
                return $is_exist->id;
            }
        }

        public function deleteDeficiencyReference()
        {
            DB::table('deficiency_reference')
                            ->truncate();
        }

        public function deleteMessage()
        {
            DB::table('message_master')
                            ->truncate();
        }

        public function insertDeficiencyReference($feed_id,$deficiency_data)
        {
            $insertID = DB::table('deficiency_reference')->insertgetID($deficiency_data);
                return $insertID;
        }

        public function insertMessageMaster($feed_id,$message_data)
        {
            // echo "<pre>";
            // print_r($message_data);
            // exit;
            $insertID = DB::table('message_master')->insertgetID($message_data);
            return $insertID;
        }

        public function getItemData($item_id,$item_occurance,$patient_id,$meal_type)
        {
            $itemCount = count($item_id);

            $high_glycemic = array();
            foreach ($item_id as $key => $value) 
            {
                $item_data = DB::table('meal_reference')->where('itemID', $value)->first();
                $high_glycemic[$item_data->item] = $item_data->glycemic_index ;
             
            }
            $maxval = 0;
            foreach ($high_glycemic as $key => $val)
            {    
              	if($val > $maxval)
                {
                	$maxval = $val;
                	$maxItem = $key;
                }
            }
            
            $item_data = DB::table('meal_reference_json')->orderBy('meal_reference_json_id','desc')->first();
            $patient_data = DB::table('patient_factor')->where('patientID',$patient_id)->first();
            $feed_data = DB::table('feeds')->where('id',$patient_data->feedID)->first();
            $lang = DB::table('patients')->select('language')->where('patientID',$patient_id)->first();
            $language = $lang->language;
            // echo $language;exit;
            switch($meal_type)
            {
                case 'B':
                        $meal_type_data = DB::table('tbl_meal_type')->where('meal_type_code',"B")->first();
                        $required_cal = (($patient_data->requiredCalories*$meal_type_data->requirement)/100);
                        $required_protein = (($patient_data->requiredProtein*$meal_type_data->requirement)/100);
                        $required_carb = (($patient_data->requiredCarbs*$meal_type_data->requirement)/100);
                        $ideal_glycemic_load = (((($patient_data->requiredCarbs/4)*$meal_type_data->requirement)/100)/2);
                        $required_fat = (($patient_data->requiredFats*$meal_type_data->requirement)/100);
                        $meal = 'Breakfast';
                        break;
                case 'L':
                        $meal_type_data = DB::table('tbl_meal_type')->where('meal_type_code',"L")->first();
                        $required_cal = (($patient_data->requiredCalories*$meal_type_data->requirement)/100);
                        $required_protein = (($patient_data->requiredProtein*$meal_type_data->requirement)/100);
                        $required_carb = (($patient_data->requiredCarbs*$meal_type_data->requirement)/100);
                        $ideal_glycemic_load = (((($patient_data->requiredCarbs/4)*$meal_type_data->requirement)/100)/2);
                        $required_fat = (($patient_data->requiredFats*$meal_type_data->requirement)/100);
                        $meal = 'Lunch';
                        break;
                case 'S':
                        $meal_type_data = DB::table('tbl_meal_type')->where('meal_type_code',"S")->first();
                        $required_cal = (($patient_data->requiredCalories*$meal_type_data->requirement)/100);
                        $required_protein = (($patient_data->requiredProtein*$meal_type_data->requirement)/100);
                        $required_carb = (($patient_data->requiredCarbs*$meal_type_data->requirement)/100);
                        $ideal_glycemic_load = (((($patient_data->requiredCarbs/4)*$meal_type_data->requirement)/100)/2);
                        $required_fat = (($patient_data->requiredFats*$meal_type_data->requirement)/100);
                        $meal = 'Snacks';
                        break;
                case 'D':
                        $meal_type_data = DB::table('tbl_meal_type')->where('meal_type_code',"D")->first();
                        $required_cal = (($patient_data->requiredCalories*$meal_type_data->requirement)/100);
                        $required_protein = (($patient_data->requiredProtein*$meal_type_data->requirement)/100);
                        $required_carb = (($patient_data->requiredCarbs*$meal_type_data->requirement)/100);
                        $ideal_glycemic_load = (((($patient_data->requiredCarbs/4)*$meal_type_data->requirement)/100)/2);
                        $required_fat = (($patient_data->requiredFats*$meal_type_data->requirement)/100);
                        $meal = 'Dinner';
                        break;
            }

            $item_arr = json_decode($item_data->json_data,true);
            $i = 0;
            $total_cal = 0;
            $total_protein = 0;
            $total_glycemic_load = 0;
            $item_resp = array();
            $item_array = array();
            $glycemic_load_arr = array();
            $item_resp['calorie_requirement'] = $required_cal;
            $item_resp['protein_requirement'] = $required_protein;
            $item_resp['carb_requirement'] = $required_carb;
            $item_resp['fat_requirement'] = $required_fat;
            $item_resp['total_cal'] = 0;
            $item_resp['total_protein'] = 0;
            $item_resp['total_carb'] = 0;
            $item_resp['total_fat'] = 0;
            $z = 0;

            foreach($item_arr as $item)
            {
                if(in_array($item['ItemId'], $item_id))
                {
                    $item_key = array_search($item['ItemId'], $item_id);
                    $item_resp['total_cal'] = $item_resp['total_cal']+($item['Calories (kCal)']*$item_occurance[$item_key]);
                    $item_resp['total_protein'] = $item_resp['total_protein']+($item['Protein(g)']*$item_occurance[$item_key]*4);
                    $item_resp['total_carb'] = $item_resp['total_carb']+($item['Carbohydrates (g)']*$item_occurance[$item_key]*4);
                    $item_resp['total_fat'] = $item_resp['total_fat']+($item['fats (g)']*$item_occurance[$item_key]*9);
                    
                    $cal = ($item['Calories (kCal)']*$item_occurance[$item_key]);
                    $protein = ($item['Protein(g)']*$item_occurance[$item_key]);

                    $item_array['item_name'][$z] = $item['Item'];
                    $item_array['item_cal'][$z] = (!empty($protein))?($cal/$protein):($cal/0.1);

                    $total_cal += ($item['Calories (kCal)']*$item_occurance[$item_key]);
                    $total_protein += ($item['Protein(g)']*$item_occurance[$item_key]*4);
                    $total_glycemic_load += (($item['Carbohydrates (g)']*$item_occurance[$item_key])*$item['glycemic index']);
                    $glycemic_load_arr[$z]['glycemic_load'] = (($item['Carbohydrates (g)']*$item_occurance[$item_key])*$item['glycemic index']);
                    $glycemic_load_arr[$z]['glycemic_item'] = $item['Item'];
                    $z++;
                }
            }
 
            if(!empty($total_glycemic_load))
            {
                $total_glycemic_load = ($total_glycemic_load/100);
            }

            if(!empty($glycemic_load_arr))
            {
            	foreach($glycemic_load_arr as $glycemic_key=>$glycemic_val)
            	{
            		if($glycemic_key < (count($glycemic_load_arr)-1))
            		{
            			if($glycemic_load_arr[$glycemic_key+1]['glycemic_load'] > $glycemic_val['glycemic_load'])
            			{
            				$temp_glycemic_item = $glycemic_load_arr[$glycemic_key+1]['glycemic_item'];
            				$temp_glycemic_load = $glycemic_load_arr[$glycemic_key+1]['glycemic_load'];
            				$glycemic_load_arr[$glycemic_key+1]['glycemic_item'] = $glycemic_val['glycemic_item'];
            				$glycemic_load_arr[$glycemic_key+1]['glycemic_load'] = $glycemic_val['glycemic_load'];
            				$glycemic_load_arr[$glycemic_key]['glycemic_item'] = $temp_glycemic_item;
            				$glycemic_load_arr[$glycemic_key]['glycemic_load'] = $temp_glycemic_load;
            			}
            		}
            	}
            }
            // echo $required_protein." total=".$total_protein;
            // exit;

            if($required_protein > $total_protein)
            {
                $deficiency_protein = ($required_protein - $total_protein);
                $deficiency_protein_percentage = round(($deficiency_protein/$required_protein)*100);

                $protein_level_data = DB::table('level_master')->where('min_value','<=',$deficiency_protein_percentage)->where('max_value','>=',$deficiency_protein_percentage)->where('type','D')->first();
                $protain_level_status = 'D';

                if(empty($protein_level_data))
                {
                   $protein_level_data = DB::table('level_master')->where('min_value','<=',$deficiency_protein_percentage)->where('max_value','>=',$deficiency_protein_percentage)->where('type','N')->first();
                   $protain_level_status = 'N'; 
                }

                if(!empty($protein_level_data->level_no))
                {
                    $protein_level = $protein_level_data->level_no;
                }
            }
            else if($required_protein < $total_protein)
            {
                $increase_protein = ($total_protein - $required_protein);
                $increase_protein_percentage = round(($increase_protein/$required_protein)*100);

                $protein_level_data = DB::table('level_master')->where('min_value','<=',$increase_protein_percentage)->where('max_value','>=',$increase_protein_percentage)->where('type','I')->first();
                $protain_level_status = 'I';

                if(empty($protein_level_data))
                {
                   $protein_level_data = DB::table('level_master')->where('min_value','<=',$increase_protein_percentage)->where('max_value','>=',$increase_protein_percentage)->where('type','N')->first(); 
                   $protain_level_status = 'N';
                }

                if(!empty($protein_level_data->level_no))
                {
                    $protein_level = $protein_level_data->level_no;
                }
            }
            else
            {
                $protein_level = '4';
            }

            if($required_cal > $total_cal)
            {
                $deficiency = ($required_cal - $total_cal);
                $deficiency_percentage = round(($deficiency/$required_cal)*100);
                
                $calories_level_data = DB::table('level_master')->where('min_value','<=',$deficiency_percentage)->where('max_value','>=',$deficiency_percentage)->where('type','D')->first();
                $calories_level_status = 'D';

                if(empty($calories_level_data))
                {
                   $calories_level_data = DB::table('level_master')->where('min_value','<=',$deficiency_percentage)->where('max_value','>=',$deficiency_percentage)->where('type','N')->first();
                   $calories_level_status = 'N'; 
                }

                if(!empty($calories_level_data->level_no))
                {
                    $calories_level = $calories_level_data->level_no;
                }

                $deficiency_data = DB::table('deficiency_reference')->where('deficiency_min_value','<=',$deficiency)->where('deficiency_max_value','>=',$deficiency)->where('feed_id',$patient_data->feedID)->first();
                $item_resp['feed'] = $feed_data->name;
                $item_resp['feed_id'] = $patient_data->feedID;
                $item_resp['water'] = (!empty($deficiency_data->water))?$deficiency_data->water:'';
                $item_resp['scoops'] = (!empty($deficiency_data->scoops))?$deficiency_data->scoops:'';
            }
            else if($required_cal < $total_cal)
            {
                $increase = ($total_cal - $required_cal);
                $increase_percentage = round(($increase/$required_cal)*100);
                $calories_level_data = DB::table('level_master')->where('min_value','<=',$increase_percentage)->where('max_value','>=',$increase_percentage)->where('type','I')->first();
                $calories_level_status = 'I';


                if(empty($calories_level_data))
                {
                   $calories_level_data = DB::table('level_master')->where('min_value','<=',$increase_percentage)->where('max_value','>=',$increase_percentage)->where('type','N')->first(); 
                   $calories_level_status = 'N';
                }

                if(!empty($calories_level_data->level_no))
                {
                    $calories_level = $calories_level_data->level_no;
                }
            }
            else
            {
                $calories_level = '4';
            }

            $case_id = $protein_level.$calories_level;

            if(!empty($_REQUEST['language']))
            {
                $message_data = DB::table('message_master')->whereRaw('case_id like "%'.$case_id.'%" and feed_id='.$patient_data->feedID.' and message_language="'.$_REQUEST['language'].'"')->get(); 
            }
            else
            {
               $message_data = DB::table('message_master')->whereRaw('case_id like "%'.$case_id.'%" and feed_id='.$patient_data->feedID.' and message_language="'.$language.' "')->get(); 
            }
            
            if(!empty($item_array))
            {
                if(count($item_array['item_cal']) > 1)
                {
                    
                    foreach($item_array['item_cal'] as $key=>$item)
                    {
                        $cal_ctr = 0;
                        $protein_ctr = 0;
                        
                        foreach($item_array['item_cal'] as $itm)
                        {
                            if($item > $itm)
                            {
                                $cal_ctr++;
                            }
                            else if($item < $itm)
                            {
                                $protein_ctr++;
                            }
                        }

                        if($cal_ctr > $protein_ctr)
                        {
                            if(empty($cal_str))
                            {   
                                $cal_str = $item_array['item_name'][$key];
                                
                            }
                            else
                            {
                                $cal_str .= $item_array['item_name'][$key];
                            }
                        }
                        else
                        {
                            if(empty($protein_str))
                            {
                                $protein_str = ','.$item_array['item_name'][$key];
                            }
                            else
                            {
                                $protein_str .= ','.$item_array['item_name'][$key];
                            }
                        }
                    }
                }
                else
                {
                	if($protein_level > $calories_level)
                    {
                        $protein_str = $item_array['item_name'][0];
                        $cal_str = ','.$item_array['item_name'][0];
                        
                    }
                    else
                    {
                        $cal_str = $item_array['item_name'][0];
                        $protein_str = ','.$item_array['item_name'][0];
                        
                    }
                }
                
                
            }
            if ($itemCount > 4) {
                $itemCut = array_slice(explode(',',$cal_str), 0, 3);
                $cal_str = implode(',',$itemCut);
             
            }
            if ($itemCount < 4) {
                $itemCut = array_slice(explode(',',$cal_str), 0, 2);
                $cal_str = implode(',',$itemCut);
               
            }
            if ($itemCount > 4) {
                $itemCutProtein = array_slice(explode(',',$protein_str), 0, 3);
                $protein_str = implode(',',$itemCutProtein);
              
            }
            if ($itemCount < 4) {
                $itemCutProtein = array_slice(explode(',',$protein_str), 0, 2);
                $protein_str = implode(',',$itemCutProtein);
                
            }
            // echo $protein_str;
            // echo $cal_str;
            if(!empty($message_data))
            {
                foreach($message_data as $msg)
                {
                    if($msg->message_type=='S')
                    {
                        $item_resp['standard_msg_title'] = str_replace('{{meal}}',$meal,str_replace('{calories_level_name}',$calories_level_data->level_name,str_replace('{protein_level_name}',$protein_level_data->level_name,$msg->standard_msg)));
                        $item_resp['standard_msg_description'] = $msg->summary_msg;
                    }
                    else if($msg->message_type=='I')
                    {
                        if($calories_level_status=='I' && $protain_level_status=='I')
                        {
                            $decrease_item = 'Calories & Protein';
                        }
                        else if($calories_level_status=='I')
                        {
                            $decrease_item = 'Calories';
                        }
                        else if($protain_level_status=='I')
                        {
                            $decrease_item = 'Protein';
                        }
                        else
                        {
                            $decrease_item = '';
                        }
                        // echo $decrease_item;exit;
                        if(strpos('61, 71, 52, 62, 72, 53, 63, 73,  64, 74,  54,  55,  65, 75, 66, 67, 76, 77', $case_id)!==false)
                        {
                            $suggested_item = $protein_str.'::';
                        }
                        else if(strpos('45, 46, 47, 56, 57', $case_id)!==false)
                        {
                            $suggested_item = $protein_str.'::';
                        }
                        else if(strpos('14, 24, 34, 15, 25, 35, 16, 26, 36, 17, 27, 37', $case_id)!==false)
                        {
                            $suggested_item = (!empty($cal_str))?$cal_str.'::':'';
                        }
                        else
                        {
                            $suggested_item = '';
                        }

                        // $item_resp['decrease_msg_title'] = $msg->standard_msg.' '.$decrease_item;
                        $item_resp['decrease_msg_title'] = $msg->standard_msg;
                        $item_resp['decrease_msg_description'] = '::'.str_replace("{{Item 1}}, {{item 2}}", $suggested_item, $msg->summary_msg);
                        
                    }
                    else if($msg->message_type=='D')
                    {
                        $item_resp['increase_msg_title'] = $msg->standard_msg;
                        $item_resp['increase_msg_description'] = $msg->summary_msg;
                    }
                }
            }

            $item_resp['ideal_glycemic_load'] = $ideal_glycemic_load;
            $item_resp['consumed_glycemic_load'] = $total_glycemic_load;

            

            if($required_cal <= $total_cal)
            {
                $glycemic_load_per = (($total_glycemic_load/$ideal_glycemic_load)*100);
            	$diabetes = ($patient_data->diabetes=='No')?'ND':'D';
            	
            	if($glycemic_load_per <= 104)
            	{
            		$glycemic_case_id = '104';
            		$is_replace = 'N';
            	}
            	else
            	{
            		$is_replace = 'Y';

            		if($diabetes=='D')
            		{
            			if($glycemic_load_per >= 105 && $glycemic_load_per <= 120)
            			{
            				$glycemic_case_id = '105,120';
            			}
            			else
            			{
            				$glycemic_case_id = '121';
            			}
            		}
            		else
            		{
            			if($glycemic_load_per >= 105 && $glycemic_load_per <= 115)
            			{
            				$glycemic_case_id = '105,115';
            			}
            			else
            			{
            				$glycemic_case_id = '116';
            			}
            		}
            	}

            	if(!empty($_REQUEST['language']))
	            {
	            	$glycemic_message_data = DB::table('message_master')->whereRaw('case_id="'.$glycemic_case_id.'" and feed_id="'.$diabetes.'" and message_language="'.$_REQUEST['language'].'"')->get(); 
	            }
	            else
	            {
	               	$glycemic_message_data = DB::table('message_master')->whereRaw('case_id="'.$glycemic_case_id.'" and feed_id="'.$diabetes.'" and message_language="EN"')->get(); 
	            }

	            if($is_replace=='N')
	            {
	            	$item_resp['glycemic_msg'] = $glycemic_message_data[0]->standard_msg;
	            }
	            else
	            {
	            	if(count($glycemic_load_arr) >= 2)
	            	{
	            		$replace_str = $maxItem;
	            	}
	            	else
	            	{
	            		$replace_str = $maxItem;
	            	}
	            	$item_resp['glycemic_msg'] = str_replace('{{item1}} {{item2}}', $replace_str, $glycemic_message_data[0]->standard_msg);
                   
	            
                }
	            
	            $item_resp['glycemic_case_id'] = $glycemic_case_id;
            }

            $item_resp['case_id'] = $case_id;
            // print_r($item_resp);exit;
            
            return $item_resp;
            
        }
        public function updateFoodConsume($feed_consume_id,$updateArray){
            // echo($feed_consume_id);
            // print_r($updateArray);exit;
            
            $itemsupdate = DB::table('food_consume')
             ->where('food_consume_id',$feed_consume_id)
             ->update($updateArray);
        }

        public function allState(){
            $states = DB::table('states')
                        ->get();
                 return $states;
        }

        public function allCity($state_id){
            $city = DB::table('cities')
                        ->whereRaw('state_id='.$state_id)
                        ->get();
                 return $city;
        }

        public function adminLogin($username,$password)
        {
            $data = array();
            $admin_data = DB::table('admin')
                        ->whereRaw('username="'.$username.'" and password="'.md5($username."|".$password).'"')
                        ->first();

            if(!empty($admin_data))
            {
                DB::table('admin')->where('admin_id',$admin_data->admin_id)->update(array('last_login'=>time()));
  
                $data['admin_id'] = $admin_data->admin_id;
                $data['role'] = ($admin_data->role=='A')?'Admin':'User';
                $data['email_id'] = $admin_data->email_id;
                $data['active_status'] = ($admin_data->active_status=='S')?'Active':'In-Active';
                $data['added_on'] = date('d-m-Y H:i:s a',$admin_data->added_on); 
                $data['updated_on'] = date('d-m-Y H:i:s a',$admin_data->updated_on);
                $data['last_login'] = date('d-m-Y H:i:s a',time());
            }
            return $data;
        }

        public function insertAdminMaster($data)
        {
            $admin_data = DB::table('admin')
                        ->whereRaw('username="'.$data['email_id'].'"')
                        ->first();

            if(!empty($admin_data))
            {
                $data = array('msg'=>'Email Id Already Exist','data'=>null);
            }
            else
            {
                $insertID = DB::table('admin')->insertgetID($data);

                $data = array('msg'=>'User Created Successfully','data'=>array('admin_id'=>$insertID));
            }
            
            return $data;
        }

        public function updateAdminMaster($data,$id)
        {
            $insert = DB::table('admin')->where('admin_id',$id)->update($data);

            $data = array('msg'=>'Admin User Updated Successfully','data'=>array('admin_id'=>$id));

            return $data;
        }

        public function getAllUser($id='')
        {
            if(empty($id))
            {
                $admin_data = DB::table('admin')
                        ->get();
            }
            else
            {
                $admin_data = DB::table('admin')
                        ->where('admin_id',$id)
                        ->get();
            }
            

            if(!empty($admin_data))
            {
                $i = 0;

                foreach($admin_data as $admin)
                {
                    $data[$i]['admin_id'] = $admin->admin_id;
                    $data[$i]['role'] = ($admin->role=='A')?'Admin':'User';
                    $data[$i]['name'] = $admin->name;
                    $data[$i]['email_id'] = $admin->email_id;
                    $data[$i]['active_status'] = ($admin->active_status=='S')?'Active':'In-Active';
                    $data[$i]['added_on'] = date('d-m-Y h:i:s a',$admin->added_on);
                    $data[$i]['updated_on'] = date('d-m-Y h:i:s a',$admin->updated_on);
                    $data[$i]['last_login'] = date('d-m-Y h:i:s a',$admin->last_login);
                    $i++;
                }

                $resp = array('msg'=>'Users Found','data'=>$data);
            }
            else
            {
                $resp = array('msg'=>'No Users Found','data'=>null);
            }

            return $resp;
        }

        public function insertDoctorMaster($data)
        {
            $doctor_data = DB::table('doctor')
                        ->whereRaw('name="'.$data['name'].'"')
                        ->first();

            if(!empty($doctor_data))
            {
                $data = array('msg'=>'Doctor Already Exist','data'=>null);
            }
            else
            {
                $insertID = DB::table('doctor')->insertgetID($data);

                $data = array('msg'=>'Doctor Created Successfully','data'=>array('doctor_id'=>$insertID));
            }
            
            return $data;
        }

        public function updateDoctorMaster($data,$id)
        {
            $insert = DB::table('doctor')->where('doctor_id',$id)->update($data);

            $data = array('msg'=>'Doctor Updated Successfully','data'=>array('doctor_id'=>$id));

            return $data;
        }

        public function allDoctors($id=''){

            if(empty($id))
            {
                $doctors = DB::table('doctor')
                        // ->where('active_status="S"')
                        ->get();
            }
            else
            {
                $doctors = DB::table('doctor')
                        ->where('doctor_id',$id)
                        ->get();
            }
            
            return $doctors;
        }

        public function deleteDoctor($id)
        {
           $doctors = DB::table('doctor')
                        ->where('doctor_id',$id)
                        ->delete(); 
        }

        public function listMealType($id=''){

            if(empty($id))
            {
                $meal_type = DB::table('tbl_meal_type')
                        // ->where('active_status="S"')
                        ->get();
            }
            else
            {
                $meal_type = DB::table('tbl_meal_type')
                        ->where('meal_type_id',$id)
                        ->get();
            }
            
            return $meal_type;
        }

        public function updateMealTypeMaster($data,$id)
        {
            $insert = DB::table('tbl_meal_type')->where('meal_type_id',$id)->update($data);

            $data = array('msg'=>'Meal Type Updated Successfully','data'=>array('meal_type_id'=>$id));

            return $data;
        }

        public function listFaqCategory($id=''){

            if(empty($id))
            {
                $faq_category = DB::table('tbl_faq_category')
                        // ->where('active_status="S"')
                        ->get();
            }
            else
            {
                $faq_category = DB::table('tbl_faq_category')
                        ->where('faq_category_id',$id)
                        ->get();
            }
            
            return $faq_category;
        }

        public function manageFaqCategory($data,$id='')
        {
            if(empty($id))
            {
                $faq_category_data = DB::table('tbl_faq_category')
                        ->whereRaw('faq_category="'.$data['faq_category'].'"')
                        ->first();  
            }
            else
            {
                $faq_category_data = DB::table('tbl_faq_category')
                        ->whereRaw('faq_category="'.$data['faq_category'].'" and faq_category_id!='.$id)
                        ->first();
            }
            
            if(!empty($faq_category_data))
            {
                $data = array('msg'=>'This faq category already exist','data'=>'');
            }
            else
            {
                if(empty($id))
                {   
                    $insertID = DB::table('tbl_faq_category')->insertgetID($data);
                    $id = $insertID;
                }
                else
                {
                    $insert = DB::table('tbl_faq_category')->where('faq_category_id',$id)->update($data);
                }

                $data = array('msg'=>'Faq Category Manage Successfully','data'=>array('faq_category_id'=>$id));
            }
            
            
            return $data;
        }

        public function deleteFaqCategory($id)
        {
           $faq_category = DB::table('tbl_faq_category')
                        ->where('faq_category_id',$id)
                        ->delete(); 
        }

        public function getMessage($feed_id,$case_id,$language)
        {
            $message_data = DB::table('message_master')->whereRaw('case_id like "%'.$case_id.'%" and feed_id='.$feed_id.' and message_language="'.$language.'"')->get(); 
            $data = array();

            if(!empty($message_data))
            {
                $i = 0;

                foreach($message_data as $msg)
                {
                    $data[$i]['message_master_id'] = $msg->message_master_id;
                    $data[$i]['feed_id'] = $msg->feed_id;
                    $data[$i]['case_id'] = $msg->case_id;
                    $data[$i]['standard_msg'] = $msg->standard_msg;
                    $data[$i]['summary_msg'] = $msg->summary_msg;

                    if($msg->message_type=='S')
                    {
                        $type = 'Standard Message';
                    }
                    else if($msg->message_type=='D')
                    {
                        $type = 'Decrease Message';
                    }
                    else if($msg->message_type=='I')
                    {
                        $type = 'Increase Message';
                    }
                    else if($msg->message_type=='G')
                    {
                        $type = 'Glycemic Message';
                    }

                    $data[$i]['message_type'] = $type;
                    $data[$i]['message_language'] = $language;
                    $i++;
                }
            }

            return $data;
        }

        public function getGraphData($patient_id,$parameter,$start_date,$end_date,$feed_id)
        {
            $data = DB::table('tbl_graph_load_reference as g')
                            ->select('g.testDate','g.patient_id','g.parameter','tbl_graph_load_reference_master.unit','g.date','g.input_value')
                            ->leftJoin('tbl_graph_load_reference_master', 'g.parameter', '=', 'tbl_graph_load_reference_master.parameter')
                            ->where('g.patient_id',$patient_id)
                            ->where('g.parameter',$parameter)
                            ->where('tbl_graph_load_reference_master.feed_id',$feed_id)
                            ->where('g.testDate','>=',$start_date)
                            ->where('g.testDate','<=',$end_date)
                            ->orderBy('g.date','desc')
                            ->take(8)
                            ->get();
            if (count($data)<= 0 ) {
                # code...
                $resp_data = array();
            }
            else {
                $resp_data['patient_id'] = $patient_id;
                $resp_data['test_name'] = $data[0]->parameter;
                $resp_data['test_unit'] = $data[0]->unit;
                //$resp_data['normal_value_min'] = $data[0]->normal_min;
                //$resp_data['normal_value_max'] = $data[0]->normal_max;
                $i = 0;

                foreach($data as $val)
                {
                    // echo $val->date;
                    // date('dd/mm/YY',strtotime($val->date));
                    $resp_data['Values'][$i]['date'] = $val->testDate;
                    $resp_data['Values'][$i]['value'] = $val->input_value;

                    $i++;
                }
            }
            
            return $resp_data;
        }

        public function getAllPatients(){
            $patientIDS = DB::table('patients as p')
                            ->leftJoin('patient_factor as pf','p.patientID','=','pf.patientID')
                            ->leftJoin('patient_factor as pf','p.patientID','=','pf.patientID')
                            ->select('p.patientID','pf.healthStatusID')
                            ->where('pf.healthStatusID','!=','1')
                            ->orderBy('patientID', 'ASC')
                            ->get();

            return $patientIDS;
        }

        public function singlePatientDetails($patientID){
            $data = DB::table('patients as p')
                        ->select('p.patientID',DB::raw("CONCAT(p.firstName,' ',p.lastName) as fullname"),'p.phone','p.language','p.gender','pf.age','pf.weight','pf.height','pf.healthStatusID','pf.diabetes','pf.feedID','f.name as feedName','pf.albuminFactor','pf.requiredCalories','pf.requiredProtein','pf.requiredCarbs','pf.requiredFats','pf.BMI','.p.locationID','l.city','p.createdAt as onboardingTime','p.doctorID','p.physioID','p.dieticianID')
                        ->leftJoin('patient_factor as pf','p.patientID','pf.patientID')
                        ->leftJoin('cities as l','l.id','p.locationID')
                        ->leftJoin('feeds as f','f.id','pf.feedID')
                        ->where('p.patientID',$patientID)
                        ->get();
            return $data;
        }

        public function insertToken($patientID, $token) {
            // echo $patientID . "patient ID"."<br>";
            // echo "Token".$token;

            $updateArray = array (
                "token" => $token,
            );

            // print_r($updateArray); exit;

            $update = DB::table('patients')->where('patientID',$patientID)->update($updateArray);
        }

       
        public function graphs($data){
            $dates = [
                $data['fromDate'],
                $data['toDate']
            ];

           $values = array();

            $query = DB::table("food_consume as fc")
                   ->select("fc.patient_id","fc.date","fc.meal_type_id","r.calorie_requirement","fc.consume_calorie","r.protein_requirement","fc.consume_protein",
                   "r.carb_requirement","fc.consume_carb","r.fat_requirement","fc.consume_fat","r.ideal_glycemic_load","r.consumed_glycemic_load","r.glycemic_msg")                 
                   ->where("fc.patient_id", $data['PatientID'])
                   ->whereBetween("fc.date",$dates)
                   ->leftJoin("reccommendation as r","fc.food_consume_id","=","r.food_consume_id")
                   ->get();

                   foreach($query as $value){
                    if($value->{'meal_type_id'} == "B"){
                        $values['breakfast'][] = $value;
    
                    }
                    else if($value->{'meal_type_id'} =="L"){
                        $values['lunch'][] = $value;
    
                    }
                    else {
                        $values['dinner'][] = $value;
    
                    }

                   }
            
                return $values;

            

            
        }

        /*NEW*/
        public function addDietitian($data)
        {
          $insertId = DB::table('dietitian')->insertGetId($data);
          return $insertId; 
        }

        public function listDietitian(){
        $values = DB::table('dietitian')->get();
        return $values;
    }
    //Update Profile Details Code
    public function UpdatePatients($insert_patient_data , $patientID){
        $insertGet = DB::table('patients')->where('patientID', $patientID)->update($insert_patient_data);
      return $insertGet;
    }

     public function UpdatePatientFactor($insert_patient_factor,$patientID){
         $insert = DB::table('patient_factor')->where('patientID', $patientID)->update($insert_patient_factor);
     }


     public function getPatientId($mob_num)
     {
        $getId=DB::table('patients')->select('patientID','firstName','phone')->where('phone',$mob_num)->first();

            return $getId;
     }

     public function getRoles($mobile_no)
     {
        $there = DB::table('tbl_dietitiansupport') 
                ->select('d_id',DB::raw("CONCAT(FirstName,' ',LastName) as Name"),'role','mobile_no','registration_number')
                ->where('mobile_no',$mobile_no)
                ->get();
        if (!empty($there)) {
            
            return $there;
        }
        else {
            $data = array();
            return $data;
        }
     }
     
}
