<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Login;
use  App\Models\Feeds;
use App\Helpers\TokenHelper;
use App\Helpers\Calculation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Schema;

class MealController extends Controller
{   
    public function __construct(){
        $this->_login = new Login;
        $this->_feed = new Feeds;
        $this->_calculation = new Calculation;
        $this->_balance_index = new Feeds;
    }
    
    public function ManageCsv()
    {
        $file_name = $_REQUEST['file_name'];
        $file_name_arr = explode('_',str_replace('.csv', '', $file_name));
        $version = $file_name_arr[count($file_name_arr)-1];
        $file_path = base_path()."/upload/csv/".$file_name;
        $column = array();
        $arr_data = array();

        if(!empty($_REQUEST['mod']) && $_REQUEST['mod']=='D')
        {
        	$this->_login->deleteDeficiencyReference();

        	if(($handle = fopen($file_path , "r")) !== FALSE) 
	        {
	            $i = 0;
	            $j = 0;
	            
	            while (($data = fgetcsv($handle)) !== FALSE) 
	            {
	            	if($i!=0)
	                {
	                	$range = explode("-",$data[0]);

	                	$feed_data = $this->_login->getFeedId($data[1]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[2],'scoops'=>$data[3],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[4]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[5],'scoops'=>$data[6],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[7]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[8],'scoops'=>$data[9],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[10]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[11],'scoops'=>$data[12],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[13]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[14],'scoops'=>$data[15],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[16]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[17],'scoops'=>$data[18],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[19]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[20],'scoops'=>$data[21],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[22]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[23],'scoops'=>$data[24],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[25]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[26],'scoops'=>$data[27],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);

	                	$feed_data = $this->_login->getFeedId($data[28]);
	                	$deficiency_data = array('deficiency_min_value'=>$range[0],'deficiency_max_value'=>$range[1],'feed_id'=>$feed_data,'water'=>$data[29],'scoops'=>$data[30],'added_on'=>time(),'updated_on'=>time());
	                	
	                	$deficiencyReferenceID = $this->_login->insertDeficiencyReference($feed_data,$deficiency_data);
	                }

	                $i++;
	            }
	        }

	        if(!empty($deficiencyReferenceID))
	        {
	            echo "Deficiency Reference Updated Successfully";
	        }
        }
        else
        {
        	if(($handle = fopen($file_path , "r")) !== FALSE) 
	        {
	            $i = 0;
	            $j = 0;
	            
	            while (($data = fgetcsv($handle)) !== FALSE) 
	            {
	                if($i==0)
	                {
	                    foreach($data as $key=>$val)
	                    {
	                        if($key==0)
	                        {
	                            $column[] = 'Item';
	                        }
	                        else
	                        {
	                            $column[] = $val;
	                        }
	                    }
	                }
	                else
	                {
	                    if($j==0)
	                    {
	                        $arr_data[$j]['Version'] = $version;
	                    }

	                    $arr_data[$j]['ItemId'] = $i;

	                    foreach($data as $key=>$val)
	                    {
	                        $arr_data[$j][$column[$key]] = $val;
	                    }

	                    $j++;
	                }

	                $i++;
	            }
	        }

	        $json_data = json_encode($arr_data);
	        
	        $reference_data = array('meal_reference_json_id'=>null,'version'=>$version,'json_data'=>$json_data);
	        $referenceID = $this->_login->manageCsv($reference_data,$version);

	        if(!empty($referenceID))
	        {
	            echo "Meal Reference Updated Successfully";
	        }
        }
    }

    public function ManageMessageCsv()
    {
        $file_name = $_REQUEST['file_name'];
        $file_path = base_path()."/upload/csv/".$file_name;

        $column = array();
        $arr_data = array();

        // $this->_login->deleteMessage();

    	if(($handle = fopen($file_path , "r")) !== FALSE) 
        {
            $i = 0;
            $j = 0;
            
            while (($data = fgetcsv($handle)) !== FALSE) 
            {
            	if($i!=0)
                {
                	$feed_data = $this->_login->getFeedId($data[0]);
                	$message_data = array('feed_id'=>$feed_data,'case_id'=>$data[1],'standard_msg'=>$data[2],'summary_msg'=>$data[3],'message_type'=>$data[4],'message_language'=>$data[5],'added_on'=>time(),'updated_on'=>time());

                	// echo "<pre>";
                	// print_r($message_data);
                	// exit;
                	
                	$messageMasterID = $this->_login->insertMessageMaster($feed_data,$message_data);
                }

                $i++;
            }
        }

        if(!empty($messageMasterID))
        {
            echo "Message Master Updated Successfully";
        }
    }

    public function GetMealReference(Request $request)
    {
        if(!empty($request->version_no))
        {
            $version_no = $request->version_no;
        }
        else
        {
            $version_no = '';
        }

        $data = $this->_login->getMealReference($version_no);
        
        echo json_encode($data);
    }

    public function PostFoodConsume(Request $request)
    {
    	if(!empty($request->item_id))
    	{
    		$item_data = $this->_login->getItemData($request->item_id,$request->item_occurance,$request->patient_id,$request->meal_type_id);
    	}

        // echo json_encode($item_data); die;
    	$data = array();

    	$data['patient_id'] = $request->patient_id;
    	$data['meal_type_id'] = $request->meal_type_id;
    	$data['item_id'] = implode(",",$request->item_id);
    	$data['item_occurance'] = implode(",",$request->item_occurance);
    	$data['date'] = date('Y-m-d');
    	$data['added_on'] = time();
    	$data['updated_on'] = time();
        $feed_consume_id = DB::table('food_consume')->insertgetID($data);
    
        
    	$reccommendation_data['patient_id'] = $request->patient_id;
    	$data['patient_id'] = $request->patient_id;
    	$reccommendation_data['food_consume_id'] = $feed_consume_id;
    	$data['food_consume_id'] = $feed_consume_id;
    	$reccommendation_data['calorie_requirement'] = $item_data['calorie_requirement'];
    	$data['calorie_requirement'] = $item_data['calorie_requirement'];
    	$reccommendation_data['protein_requirement'] = $item_data['protein_requirement'];
    	$data['protein_requirement'] = $item_data['protein_requirement'];
    	$reccommendation_data['carb_requirement'] = $item_data['carb_requirement'];
    	$data['carb_requirement'] = $item_data['carb_requirement'];
    	$reccommendation_data['fat_requirement'] = $item_data['fat_requirement'];
    	$data['fat_requirement'] = $item_data['fat_requirement'];
    	$reccommendation_data['consume_calorie'] = $item_data['total_cal'];
    	$data['consume_calorie'] = $item_data['total_cal'];
    	$reccommendation_data['consume_protein'] = $item_data['total_protein'];
    	$data['consume_protein'] = $item_data['total_protein'];
    	$reccommendation_data['consume_carb'] = $item_data['total_carb'];
    	$data['consume_carb'] = $item_data['total_carb'];
    	$reccommendation_data['consume_fat'] = $item_data['total_fat'];
    	$data['consume_fat'] = $item_data['total_fat'];

    	if(!empty($item_data['feed_id']))
    	{
    		$reccommendation_data['feed_id'] = $item_data['feed_id'];
    		$data['feed_id'] = $item_data['feed_id'];
    	}
    	
    	if(!empty($item_data['scoops']))
    	{
    		$reccommendation_data['suggested_scoops'] = $item_data['scoops'];
    		$data['suggested_scoops'] = $item_data['scoops'];
    	}

        /*$reccommendation_data['suggested_scoops'] = (!empty($item_data['scoops']))?$item_data['scoops']:'';
        $data['suggested_scoops'] = (!empty($item_data['scoops']))?$item_data['scoops']:'';
*/
    	
    	if(!empty($item_data['water']))
    	{
    		$reccommendation_data['suggested_water'] = $item_data['water'];
    		$data['suggested_water'] = $item_data['water'];
    	}

       /* $reccommendation_data['suggested_water'] = (!empty($item_data['water']))?$item_data['water']:'';
        $data['suggested_water'] = (!empty($item_data['water']))?$item_data['water']:'';
*/
        $reccommendation_data['suggested_feed'] = (!empty($item_data['feed']))?$item_data['feed']:'';
        $data['suggested_feed'] = (!empty($item_data['feed']))?$item_data['feed']:'';

        if(!empty($item_data['standard_msg_title']))
        {
            $reccommendation_data['standard_msg_title'] = $item_data['standard_msg_title'];
            $reccommendation_data['standard_msg_description'] = $item_data['standard_msg_description'];
            $data['standard_msg_title'] = $item_data['standard_msg_title'];
            $data['standard_msg_description'] = $item_data['standard_msg_description'];
        }

       /* $reccommendation_data['standard_msg_title'] = (!empty($item_data['standard_msg_title']))?$item_data['standard_msg_title']:'';
        $data['standard_msg_title'] = (!empty($item_data['standard_msg_title']))?$item_data['standard_msg_title']:'';

        $reccommendation_data['standard_msg_description'] = (!empty($item_data['standard_msg_description']))?$item_data['standard_msg_description']:'';
        $data['standard_msg_description'] = (!empty($item_data['standard_msg_description']))?$item_data['standard_msg_description']:'';
*/
        if(!empty($item_data['increase_msg_title']))
        {
            $reccommendation_data['increase_msg_title'] = $item_data['increase_msg_title'];
            $reccommendation_data['increase_msg_description'] = $item_data['increase_msg_description'];
            $data['increase_msg_title'] = $item_data['increase_msg_title'];
            $data['increase_msg_description'] = $item_data['increase_msg_description'];
        }

       /* $reccommendation_data['increase_msg_title'] = (!empty($item_data['increase_msg_title']))?$item_data['increase_msg_title']:'';
        $data['increase_msg_title'] = (!empty($item_data['increase_msg_title']))?$item_data['increase_msg_title']:'';

        $reccommendation_data['increase_msg_description'] = (!empty($item_data['increase_msg_description']))?$item_data['increase_msg_description']:'';
        $data['increase_msg_description'] = (!empty($item_data['increase_msg_description']))?$item_data['increase_msg_description']:'';
       */
       if(!empty($item_data['decrease_msg_title']))
        {
            $reccommendation_data['decrease_msg_title'] = $item_data['decrease_msg_title'];
            $reccommendation_data['decrease_msg_description'] = $item_data['decrease_msg_description'];
            $data['decrease_msg_title'] = $item_data['decrease_msg_title'];
            $data['decrease_msg_description'] = $item_data['decrease_msg_description'];
        }

        /*$reccommendation_data['decrease_msg_title'] = (!empty($item_data['decrease_msg_title']))?$item_data['decrease_msg_title']:'';
        $data['decrease_msg_title'] = (!empty($item_data['decrease_msg_title']))?$item_data['decrease_msg_title']:'';

        $reccommendation_data['decrease_msg_description'] = (!empty($item_data['decrease_msg_description']))?$item_data['decrease_msg_description']:'';
        $data['decrease_msg_description'] = (!empty($item_data['decrease_msg_description']))?$item_data['decrease_msg_description']:'';
*/
        $data['case_id'] = $item_data['case_id'];

        if(!empty($item_data['glycemic_msg']))
        {
        	$data['glycemic_msg'] = $item_data['glycemic_msg'];
            // print_r($item_data['glycemic_msg']); exit;
        	$data['glycemic_case_id'] = $item_data['glycemic_case_id'];
        	$reccommendation_data['glycemic_msg'] = $item_data['glycemic_msg'];
        	$reccommendation_data['glycemic_case_id'] = $item_data['glycemic_case_id'];
            $reccommendation_data['ideal_glycemic_load'] = $item_data['ideal_glycemic_load'];
            $reccommendation_data['consumed_glycemic_load'] = $item_data['consumed_glycemic_load'];
        	$data['ideal_glycemic_load'] = $item_data['ideal_glycemic_load'];
        	$data['consumed_glycemic_load'] = $item_data['consumed_glycemic_load'];
        }

        $reccommendation_data['case_id'] = $item_data['case_id'];

    	$reccommendation_data['date'] = date('Y-m-d');
    	$reccommendation_data['added_on'] = time();
    	$reccommendation_data['updated_on'] = time();
    	//  echo "<pre>";
    	//  print_r($reccommendation_data);
    	//  exit;
    	$reccommendation_id = DB::table('reccommendation')->insertgetID($reccommendation_data);

    	// echo "<pre>";
    	// print_r($data);
    	// exit;
        $data['consume_carb'] = round($data['consume_carb'],2);
    	$data['reccommendation_id'] = $reccommendation_id;

    //echo json_encode($data);
 //exit;
     
        // Anusha
            /*calories*/
             $cx = $data['consume_calorie'];
             $cy = $data['calorie_requirement'];
            
             if ($cx == $cy)
                 {
                     $x = 1;
                 }
             
               
               if (($cx < 2*($cy)) && ($cx >$cy))
               {
                 $x = 1-($cx-$cy)/$cy;
               }
     
               if ($cx > 2*($cy)) 
               {
                  $x = 0;
               }
     
               if ($cx < $cy) 
               {
                  $x = 1-($cy-$cx)/$cy;
               }
               
                 
             //print_r($x);exit;
               
                   /*proteins*/
     
               $px = $data['consume_protein'];
              $py = $data['protein_requirement'];
                     
             if($px == $py)
                  {
                    $y = 1;
                  }
                   
                  
            if(($px < 2*($py)) && ($px >$py))
                 {
                 
                     $y = 1-($px-$py)/$py;
                }
     
            if ($px > 2*($py)) 
                 {
                    $y = 0;
                 }
     
           
            if ($px < $py) 
                 {
                   $y = 1-($py-$px)/$py;
                   $y =0;
                 }
                
                 
               //print_r($y);exit;
                
                       /*carb*/
     
                 $cax = $data['consume_carb'];
                 $cay = $data['carb_requirement'];
                       
                         
                 if($cax == $cay)
                  {
                      $z = 1;
                  }
                       
                   if(($cax < 2*($cay)) && ($cax >$cay))
                   {
                     $z = 1-($cax-$cay)/$cay;
                   }
     
                   if ($cax > 2*($cay)) 
                   {
                       $z = 0;
                   }
               
                  if ($cax < $cay) 
                   {
                      $z = 1-($cay-$cax)/$cay;
                   }
                   //print_r($z);exit;
                   
                   /*fats*/
                 //print_r($data);exit;
                 $fx = $data['consume_fat'];
                 $fy = $data['fat_requirement'];
               
                 if($fx == $fy)
                   {
                       $f = 1;
                    }
               
     
                  if(($fx < 2*($fy)) && ($fx >$fy))
                 {
                     $f = 1 -($fx-$cy)/$fy;
                 }
     
                           
                  if ($fx > 2*($fy)) 
                 {
                   $f = 0;
                 }
                 
                  if ($fx < $fy) 
                   {
                     $f = 1-($fy-$fx)/$fy;
                   }
                  //print_r($f);exit;                          
              $sum = ($x + $y + $z + $f)*100/4;
              $data['balance_index'] = $sum;
             // print_r($sum);exit;
            
     
             $balance_array = [
             'balanceIndex_value' => $sum,
             //'groupID' => $request->group_id,
             'patientID' =>  $request->patient_id,
             'meal_type' =>  $request->meal_type_id,
             'date_consume' => $data['date']
             ];
             
             $query = $this->_balance_index->saveBalanceIndex($balance_array);
             
             $updateArray = array(
                'consume_calorie' => $data['consume_calorie'],
                
                'consume_protein' => $data['consume_protein'],
                
                'consume_carb' => $data['consume_carb'],
                'consume_fat' => $data['consume_fat']
            );
           // print_r($updateArray);exit;
             $updateFoodConsume = $this->_login->updateFoodConsume($feed_consume_id,$updateArray);
             
 
    	       echo json_encode($data);

    	exit;
        
    }


    public function PostFeedConsume(Request $request)
    {
    	$status = DB::table('reccommendation')->where('reccommendation_id',$request->reccommendation_id)->update(array('consume_feed'=>$request->consume_feed));

    	echo json_encode(array('status'=>$status));
    	exit;
    }

    public function GetPatientMeal(Request $request)
    {
        $meal_data = DB::table('food_consume')
        ->where('patient_id',$request->patient_id)
        ->where('date',$request->date)
        ->get();

        if(!empty($meal_data))
        {
            $i = 0;

            foreach($meal_data as $meal)
            {
                $suggest_data = DB::table('reccommendation')
                ->where('food_consume_id',$meal->food_consume_id)->first();

                $data[$i]['food_consume_id'] = $meal->food_consume_id;
                $data[$i]['patient_id'] = $meal->patient_id;
                $data[$i]['meal_type_id'] = $meal->meal_type_id;
                $data[$i]['item_id'] = $meal->item_id;
                $data[$i]['item_occurance'] = $meal->item_occurance;
                $data[$i]['date'] = $meal->date;
                $data[$i]['feed_id'] = $suggest_data->feed_id;
                $data[$i]['reccommendation_id'] = $suggest_data->reccommendation_id;
                $data[$i]['calorie_requirement'] = $suggest_data->calorie_requirement;
                $data[$i]['protein_requirement'] = $suggest_data->protein_requirement;
                $data[$i]['carb_requirement'] = $suggest_data->carb_requirement;
                $data[$i]['fat_requirement'] = $suggest_data->fat_requirement;
                $data[$i]['consume_calorie'] = $suggest_data->consume_calorie;
                $data[$i]['consume_protein'] = $suggest_data->consume_protein;
                $data[$i]['consume_carb'] = $suggest_data->consume_carb;
                $data[$i]['consume_fat'] = $suggest_data->consume_fat;
                $data[$i]['consume_feed'] = $suggest_data->consume_feed;
                $data[$i]['suggested_scoops'] = $suggest_data->suggested_scoops;
                $data[$i]['suggested_water'] = $suggest_data->suggested_water;
                $data[$i]['suggested_feed'] = $suggest_data->suggested_feed;
                $data[$i]['standard_msg_title'] = $suggest_data->standard_msg_title;
                $data[$i]['standard_msg_description'] = $suggest_data->standard_msg_description;
                $data[$i]['increase_msg_title'] = $suggest_data->increase_msg_title;
                $data[$i]['increase_msg_description'] = $suggest_data->increase_msg_description;
                $data[$i]['decrease_msg_title'] = $suggest_data->decrease_msg_title;
                $data[$i]['decrease_msg_description'] = $suggest_data->decrease_msg_description;
                $data[$i]['case_id'] = $suggest_data->case_id;
                $data[$i]['glycemic_msg'] = $suggest_data->glycemic_msg;
                $data[$i]['glycemic_case_id'] = $suggest_data->glycemic_case_id;
                $data[$i]['ideal_glycemic_load'] = $suggest_data->ideal_glycemic_load;
                $data[$i]['consumed_glycemic_load'] = $suggest_data->consumed_glycemic_load;

                if(empty($suggest_data->consume_feed))
                {
                    $data[$i]['feed_consumed'] = false;
                }
                else
                {
                    $data[$i]['feed_consumed'] = true; 
                }
                
                $i++;
            }

            if(!empty($data))
            {
                echo json_encode(array('data'=>$data));
                exit;
            }
            else
            {
                echo json_encode(array('data'=>'No Data Found'));
                exit;
            }
            
        }
    }

    public function HealthStatusList(){
            $healthStatus = $this->_feed->healthStatusList();
            return json_encode($healthStatus);
    }

    public function PatientGraphParametersList(Request $request){
            $param_list = $this->_feed->graphParametersList($request->feed_id);
            return json_encode($param_list);
    }

    public function PatientGraphParametersInsert(Request $request)
    {   
        $data = array();

        foreach($request->parameter as $key=>$val)
        {
            $date = $request->testDate;
            $split = explode(' ', $date);
            $testDate = $split[0];
            $testTime = $split[1].' '.$split[2];
        	$data['patient_id'] = $request->patient_id;
        	$data['testDate'] = $testDate;
        	$data['testTime'] = $testTime;
	    	$data['parameter'] = $val;
	    	$data['input_value'] = $request->input_value[$key];
	    	// $data['date'] = date('Y-m-d');
	    	$data['added_on'] = time();
	    	$data['updated_on'] = time();
	    	DB::table('tbl_graph_load_reference')->insertgetID($data);
        }

        echo json_encode(array('data'=>'Graph parameters added successfully'));
        exit;
    }

    public function TestMsg(Request $request)
    {
        if(empty($request->feed_id) || empty($request->case_id))
        {
            $resp = array('msg'=>'Mandatory parameters missing','data'=>null);
        }
        else
        {
            $language = (!empty($request->language))?$request->language:'EN';
            $resp_data = $this->_login->getMessage($request->feed_id,$request->case_id,$language);
            
            if(empty($resp_data))
            {
                $resp = array('msg'=>'No Data Found','data'=>null);
            }
            else
            {
                $resp = array('msg'=>'Data Found','data'=>$resp_data);
            }
        }

        echo json_encode($resp);
        exit;
    }

    public function getGraphReport(Request $request)
    { 
        if(empty($request->patient_id) || empty($request->parameter) || empty($request->start_date) || empty($request->end_date) || empty($request->feed_id))
        {
            $resp = array('msg'=>'Mandatory parameters missing','data'=>null);
        }
        else
        {
            $graph_data = $this->_login->getGraphData($request->patient_id,$request->parameter,$request->start_date,$request->end_date,$request->feed_id);
            
            if(empty($graph_data))
            {
                $resp = array('msg'=>'No Data Found','data'=>null);
            }
            else
            {
                $resp = array('msg'=>'Data Found Successfully','data'=>$graph_data);
            }
        }

        echo json_encode($resp);
        exit;
    }

    // public  function GetFeedList(){
    //     $this->_feed->;
    // }

        public function foodEaten(Request $request){
            $data = $request->json()->all();
            $patientID = $request->header('patientID');
            $meal = $request->header('meal');
            $mealID = rand('0000','9999');
            // echo $mealID;
            // foreach ($data as $key => $value) {
            //     $foods = array(
            //         'itemID' => $value['itemID'],
            //         'mealID' => $mealID,
            //         'patientID' => $patientID,
            //         'itemOccurence' => $value['quantity'],
            //     );
            //     $insert = $this->_feed->insertFoods($meal,$foods);
            // }
            $mealID = '1894';            
           $this->_calculation->deficit($patientID,$mealID,$meal);
        }

        public function activityReferenceInsert(Request $request){
            $data['calorieBurnt']= $request->calorieBurnt;
            $data['activityName']= $request->activityName;
            $activity = $this->_feed->activityReferenceInsert($data);
            return     $resp = array('msg'=>'Data Inserted Successfully');
            
        }
        public function activityReferenceDelete(Request $request){
            $activityID = $request->header('activityID');
            $activity = $this->_feed->activityReferenceDelete($activityID);
            $activity = $this->_feed->activityReferenceList();
            return  $resp = array('msg'=>'Data Deleted Successfully','data'=>$activity);
            
        }
        
        public function activityReferenceUpdate(Request $request){
            $data = $request->json()->all();
            $activityID = $request->header('activityID');
            $activity = $this->_feed->activityReferenceUpdate($activityID,$data);
            $activity = $this->_feed->activityReferenceList();
            return  $resp = array('msg'=>'Data Updated Successfully','data'=>$activity);
            
        }
        public function activityReferenceList(){
            $activity = $this->_feed->activityReferenceList();
            return  $resp = array('status'=>'1','data'=>$activity);
            
        }
        public function weightFactorInsert(Request $request){
            $data['weightMin']= $request->weightMin;
            $data['weightMax']= $request->weightMax ;
            $data['factor']= $request->factor;
            $activity = $this->_feed->weightFactorInsert($data);
            return     $resp = array('msg'=>'Data Inserted Successfully');
            
        }
        public function weightFactorDelete(Request $request){
            $wieghtFactorID = $request->header('wieghtFactorID');
            $activity = $this->_feed->weightFactorDelete($wieghtFactorID);
            $activity = $this->_feed->weightFactorList();
            return  $resp = array('msg'=>'Data Deleted Successfully','data'=>$activity);
            
        }
        
        public function weightFactorUpdate(Request $request){
            $data = $request->json()->all();
            $weightFactorID = $request->header('weightFactorID');
            $weightFactor = $this->_feed->weightFactorUpdate($weightFactorID,$data);
            $weightFactor = $this->_feed->weightFactorList();
            return  $resp = array('msg'=>'Data Updated Successfully','data'=>$weightFactor);
            
        }
        public function weightFactorList(){
            $activity = $this->_feed->weightFactorList();
            return  $resp = array('status'=>'1','data'=>$activity);
            
        }
        public function userActivityInsert(Request $request){
            $data['patientID']= $request->patientID;
            $data['date']= $request->date;
            $data['time']= $request->time;
            $data['activityID']= $request->activityID;
            $activity = $this->_feed->calorieBurntCalculation($data); 
            return  $resp = array('status'=>'1','msg'=>'Burnt calorie Inserted Successfully');
            
        }
        public function userActivityListByDate(Request $request){
            $data['patientID']= $request->patientID;
            $data['date']= $request->date;
            $activityList = $this->_feed->userActivityListByDate($data);
            return  $resp = array('status'=>'1','data'=>$activityList);
        }
        public function userActivityListToAdmin(Request $request){
            $data['fromDate']= $request->fromDate;
            $data['toDate']= $request->toDate;
            $activityList = $this->_feed->userActivityListToAdmin($data);
            return  $resp = array('status'=>'1','data'=>$activityList);
        }

        public function activityStatusInsert(Request $request){
            $data['activityID']= $request->activityID;
            $data['sedentary']= $request->sedentary;
            $data['active']= $request->active;
            $data['very_active']= $request->very_active;
            $activity = $this->_feed->activityStatusInsert($data);
            return   $resp = array('msg'=>'Data Inserted Successfully');
            
        }
           
        public function activityStatusList(){
            $activity = $this->_feed->activityStatusList();
            return  $resp = array('status'=>'1','data'=>$activity);
            
        }

        public function activityStatusUpdate(Request $request){
            $data = $request->json()->all();
            $activityID = $request->header('activityID');
            $activity = $this->_feed->activityStatusUpdate($activityID,$data);
            return  $resp = array('msg'=>'Data Updated Successfully','data'=>$activity);
        }

        public function activityStatusDelete(Request $request){
            $activityID = $request->header('activityID');
            $activity = $this->_feed->activityStatusDelete($activityID);
            return  $resp = array('msg'=>'Data Deleted Successfully','data'=>$activity);
            
        }

        
        public function mealInsert(Request $request){
            $data = $request->json()->all();
            $inertdata = $this->_feed->mealInsert($data);
            return  $resp = array('status'=>'1','msg'=>'Meal inserted Successfully');
        }
        public function mealUpdate(Request $request){
            $itemID = $request->header('itemID');
            $data = $request->json()->all();
            $update = $this->_feed->mealUpdate($data,$itemID);
            return  $resp = array('status'=>'1','msg'=>'Meal Updated Successfully');
        }
        public function mealDelete(Request $request){
            $itemID = $request->header('itemID');
            $update = $this->_feed->mealDelete($itemID);
            return  $resp = array('status'=>'1','msg'=>'Meal Deleted Successfully');
        }
        public function mealSingle(Request $request){
            $itemID = $request->header('itemID');
            $mealList = $this->_feed->mealSingle($itemID);
            return  $resp = array('status'=>'1','meals'=>$mealList);
        }
        public function mealList(Request $request){
            $mealList = $this->_feed->mealList();
            $mealVersion = $this->_feed->mealVersion();
            return  $resp = array('status'=>'1','version'=>$mealVersion,'meals'=>$mealList);
        }
        public function mealVersionUpdate(){
            $update = $this->_feed->mealVersionUpdate();
            return $resp = array('status'=>1,'msg'=>'Version Updated Successfully');
        }
        
        public function reCalculateSingle(Request $request){
            $patientID = $request->header('patientID');
            $requiredCalories = $this->_calculation->requiredCaloriesCalculation($patientID);
            return  $resp = array('status'=>'1');
        }

        public function reCalculateAll(Request $request){
            $patientIDS = $this->_login->getAllPatients();
            foreach ($patientIDS as $key => $value) {
               //echo  $value->patientID."-";
              $requiredCalories = $this->_calculation->requiredCaloriesCalculation($value->patientID);
            }
            return  $resp = array('status'=>1);
        }


        public function graphLoadReferenceMasterInsert(Request $request){
            $data = $request->json()->all();
            $insert = $this->_feed->graphLoadReferenceMasterInsert($data);
            return  $resp = array('status'=>'1', 'msg'=>'Inserted successfully');
        }
        public function graphLoadReferenceMasterUpdate(Request $request){
            $id = $request->header('id');
            $data = $request->json()->all();
            $insert = $this->_feed->graphLoadReferenceMasterUpdate($data,$id);
            return  $resp = array('status'=>'1', 'msg'=>'Updated successfully');
        }
        public function graphLoadReferenceMasterList(Request $request){
            $data = $this->_feed->graphLoadReferenceMasterList();
            return  $resp = array('status'=>'1', 'data'=>$data);
        }
        public function graphLoadReferenceMasterSingle(Request $request){
            $id = $request->header('id');
            $data = $this->_feed->graphLoadReferenceMasterSingle($id);
            return  $resp = array('status'=>'1', 'data'=>$data);
        }
        public function graphLoadReferenceMasterDelete(Request $request){
            $id = $request->header('id');
            $data = $this->_feed->graphLoadReferenceMasterDelete($id);
            return  $resp = array('status'=>'1', 'msg'=>'Record deleted successfully');
        }

        
        public function mealConsumedByPatient(Request $request){
            $patientID = $request->header('patientID');
            $fromDate = $request->header('fromDate');
            $toDate = $request->header('toDate');
            $type = $request->header('type');
            $data = $this->_feed->mealConsumedByPatient($patientID,$fromDate, $toDate,$type);
            return  $resp = array('status'=>'1', 'data'=>$data);
        }
        
        public function graphs(Request $request){
            $data = array();
            $data["PatientID"] = $request->patientID;
            $data["fromDate"] = $request->fromDate;
            $data["toDate"] = $request->toDate;
            $res = $this->_login->graphs($data);
           return $res;
            
        }
        
        public function particularDateMeal(Request $request){
            $patientID = $request->header('patientID');
            $fromDate = $request->header('fromDate');
            $toDate = $request->header('toDate');
            $data = $this->_feed->particularDateMeal($patientID,$fromDate, $toDate);
            return json_encode($data);
        }
        
}
           