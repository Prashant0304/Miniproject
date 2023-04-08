<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Feeds extends Model
{
    public function insertFeedModel($feedData){
        $insert = DB::table('required_calorie_reference')->insertgetID($feedData);
    }

    public function getBmiFactorGeneral($patientID){
        $getPatient = DB::table('patient_factor')->select('BMI')->where('patientID',$patientID)->first();
        $bmi =  $getPatient->BMI;
        $factor = DB::table('bmi_factor_reference')
                        ->select('*')
                        ->where('Min','<=',$bmi)
                        ->where('Max','>=',$bmi)
                        ->first();
        $factor = $factor->General;
        
        // if($bmi >= 0 && $bmi < 18.5)
        // {
        //         $factor = 1.1;
        // }
        // elseif($bmi >= 18.5 && $bmi < 22.5)
        // {
        //         $factor = 1;
        // }
        // elseif($bmi >= 22.5 && $bmi < 25)
        // {
        //         $factor = 0.95;
        // }
        // elseif($bmi >= 25 && $bmi < 30)
        // {
        //         $factor = 0.9;
        // }
        // elseif($bmi >= 30 )
        // {
        //         $factor = 0.87;
        // }
       return $factor;


    }
    public function getBmiFactorPregnent($patientID)
    {
        $getPatient = DB::table('patient_factor')->select('BMI')->where('patientID',$patientID)->first();
        $bmi = $getPatient->BMI;

        $factor = DB::table('bmi_factor_reference')
        ->select('*')
        ->where('Min','<=',$bmi)
        ->where('Max','>=',$bmi)
        ->first();
        $factor = $factor->Pregnant;
        return $factor;

//         if($bmi >= 0 && $bmi < 18.5)
//         {
//                 $factor = 1.04;
//         }
//         elseif($bmi >= 18.5 && $bmi < 22.5)
//         {
//                 $factor = 1;
//         }
//         elseif($bmi >= 22.5 && $bmi < 25)
//         {
//                 $factor = 0.95;
//         }
//         elseif($bmi >= 25 && $bmi < 30)
//         {
//                 $factor = 0.87;
//         }
//         elseif($bmi >= 30 )
//         {
//                 $factor = 0.87;
//         }
//        return $factor;
    }
        public function updateRequiredCalories($requiredCalories, $patientID){
                $insert = DB::table('patient_factor')->where('patientID',$patientID)->update($requiredCalories);
                    return $insert;
            }

        public function healthStatusList(){
                $health = DB::table('healthstatus')->select('*')->get();
                return $health;
        }

        public function insertFoods($meal,$foods){
                $getID = DB::table($meal.'_consumed')->insertgetID($foods);
        }

        public function graphParametersList($feed_id)
        {
            $param_data = DB::table('tbl_graph_load_reference_master')->select('*')->where('feed_id',$feed_id)->get();
            $data = array();

            if(!empty($param_data))
            {
                $i = 0;

                foreach($param_data as $param)
                {
                    $data[$i]['parameter'] = $param->parameter;
                    $data[$i]['unit'] = $param->unit;
                    $i++;
                }
            }

            return $data;
        }

        public function activityReferenceInsert($data){
                $insert = DB::table('activity_reference')->insertgetID($data);
                return $insert;
        }
        public function activityReferenceUpdate($activityID, $data){
                $insert = DB::table('activity_reference')->where('activityID',$activityID)->update($data);
                return $insert;
        }
        public function activityReferenceDelete($activityID){
                $insert = DB::table('activity_reference')->where('activityID',$activityID)->delete();
                return $insert;
        }
        public function activityReferenceList(){
                $list = DB::table('activity_reference')->get();
                return $list;
        }


        public function weightFactorInsert($data){
                $insert = DB::table('weight_factor')->insertgetID($data);
                return $insert;
        }
        public function weightFactorUpdate($weightFactorID, $data){
                $insert = DB::table('weight_factor')->where('weightFactorID',$weightFactorID)->update($data);
                return $insert;
        }
        public function weightFactorDelete($weightFactorID){
                $insert = DB::table('weight_factor')->where('weightFactorID',$weightFactorID)->delete();
                return $insert;
        }
        public function weightFactorList(){
                $list = DB::table('weight_factor')->get();
                return $list;
        }
        public function calorieBurntCalculation($data){ 
                $patientWeight = DB::table('patient_factor')->select('weight')->where('patientID',$data['patientID'])->first();
                $calorieReference = DB::table('activity_reference')->select('calorieBurnt')->where('activityID',$data['activityID'])->first();
                $calorieReferenceValue = DB::table('weight_factor')->select('factor')->where('weightMax','>=',$patientWeight->weight)->where('weightMin','<=',$patientWeight->weight)->first();
                $multiply = $data['time']/30;
                
                $caloriesBurnt = $calorieReference->calorieBurnt * $calorieReferenceValue->factor * $multiply ;           
                $data['caloriesBurnt'] = $caloriesBurnt;
                $insertData = DB::table('user_activity')->insertgetID($data);
               //print_r($data);
        }

                
        public function userActivityListByDate($data){
                $calorieBurntData = DB::table('user_activity')->where('date',$data['date'])->where('patientID',$data['patientID'])->get();
                // print_r($calorieBurntData);
                return $calorieBurntData;
        }
        
        public function userActivityListToAdmin($data){
                $calorieBurntData = DB::table('user_activity as ua')
                                        ->select('ua.caloriesBurnt','ua.userActivityID','ua.patientID',DB::raw("CONCAT(p.firstName,' ',p.lastName) as fullname"))
                                        ->leftJoin('patients as p','p.patientID', '=', 'ua.patientID')
                                        ->whereBetween('ua.date', [$data['fromDate'], $data['toDate']])
                                        ->get();
                return $calorieBurntData;    
        }
         
        //Anusha
        public function activityStatusInsert($data){
                $insert = DB::table('activity_status')->insert($data);
                return $insert;
        }
 
        public function activityStatusList(){
                $list = DB::table('activity_status')->get();
                return $list;
        }

        public function activityStatusUpdate($activityID, $data){
                $insert = DB::table('activity_status')->where('activityID',$activityID)->update($data);
                return $insert;
        }

        public function activityStatusDelete($activityID){
                $insert = DB::table('activity_status')->where('activityID',$activityID)->delete();
                return $insert;
        }

        public function mealInsert($data){
                $insert = DB::table('meal_reference')->insertgetID($data);
        }
        public function mealUpdate($data,$itemID){
                $insert = DB::table('meal_reference')->where('itemID',$itemID)->update($data);
        }
        public function mealSingle($itemID){
                $single = DB::table('meal_reference')->where('itemID',$itemID)->first();
                return $single;
        }
        public function mealDelete($itemID){
                $insert = DB::table('meal_reference')->where('itemID',$itemID)->delete();
        }
        public function mealList(){
                $list = DB::table('meal_reference')->get();
                return $list;
        }
        public function mealVersion(){
                $version = DB::table('meal_reference_json')->select('version')->whereRaw('meal_reference_json_id = (select max(`meal_reference_json_id`) from meal_reference_json)')->first();
                $version = $version->version;
                return $version;
        }

        public function mealVersionUpdate(){
                
                $jsonData = DB::table('meal_reference')->get();
                $version = DB::table('meal_reference_json')->select('version')->orderBy('meal_reference_json_id', 'DESC')->first();
                $version=  str_replace("v",' ',$version->version);
                // echo $version;
                $version = $version + 1;
                $version = 'v'.$version.'.0';
                // print_r($jsonData);exit;
                foreach ($jsonData as $key => $value) {

                        $array[0]['Version'] = $version;
                        $array[$key]['ItemId'] = $value->itemID;
                        $array[$key]['Item'] = $value->item;
                        $array[$key]['Quantity'] = $value->quantity;
                        $array[$key]['Units'] = $value->units;
                        $array[$key]['grams'] = $value->grams;
                        $array[$key]['Calories (kCal)'] = $value->calories;
                        $array[$key]['Protein(g)'] = $value->protein;
                        $array[$key]['Carbohydrates (g)'] = $value->carbohydrates;
                        $array[$key]['fats (g)'] = $value->fats;
                        $array[$key]['fibre'] = $value->fibre;
                        $array[$key]['glycemic index'] = $value->glycemic_index;
                        $array[$key]['POTASSIUM(mg)'] = $value->pottasium;
                        $array[$key]['SODIUM(mg)'] = $value->sodium;
                        $array[$key]['PHOSPHOROUS (mg)'] = $value->phosporous;
                        $array[$key]['CALCIUM'] = $value->calcium;
                        $array[$key]['IRON'] = $value->iron;
                        $array[$key]['Vitamin A MCG'] = $value->vitamin_a_mcg;
                        $array[$key]['Vitamin D'] = $value->vitaminD;
                        $array[$key]['Vitamin C'] = $value->vitaminC;
                        $array[$key]['Vitamin E'] = $value->vitaminE;
                        $array[$key]['Vitamin K'] = $value->vitaminK;
                        $array[$key]['THIAMINE Vitamin B1'] = $value->thimine_vitaminB1;
                        $array[$key]['RIBOFLAVIN Vitamin B2'] = $value->riboflvan_vitaminB2;
                        $array[$key]['NIACIN Vitamin B3'] = $value->niacin_vitaminB3;
                        $array[$key]['vitamin B12'] = $value->vitaminB12;
                        $array[$key]['FOLIC ACID VITAMIN B9'] = $value->folic_acid_vitaminB9;
                        $array[$key]['MAGNESIUM'] = $value->magnesium;
                        $array[$key]['ZINC'] = $value->zinc;
                        $array[$key]['SELENIUM'] = $value->selenium;
                }
                $insertJson = json_encode($array);
                $insertData = array(
                        'json_data' => $insertJson,
                        'version' => $version
                );
                // print_r($insertData);exit;
                 $updateVersion = DB::table('meal_reference_json')->insert($insertData);

                return 1;              
        }

        public function graphLoadReferenceMasterInsert($data){
                $insert = DB::table('tbl_graph_load_reference_master')->insert($data);
        }
        public function graphLoadReferenceMasterUpdate($data,$id){
                $insert = DB::table('tbl_graph_load_reference_master')->where('graph_load_reference_master_id',$id)->update($data);
        }
        public function graphLoadReferenceMasterList(){
                $list = DB::table('tbl_graph_load_reference_master')->get();
                return $list;
        }
        public function graphLoadReferenceMasterSingle($id){
                $list = DB::table('tbl_graph_load_reference_master')->where('graph_load_reference_master_id',$id)->first();
                return $list;
        }
        public function graphLoadReferenceMasterDelete($id){
                $list = DB::table('tbl_graph_load_reference_master')->where('graph_load_reference_master_id',$id)->delete();
                return $list;
        }

        public function mealConsumedByPatient($patientID,$fromDate, $toDate,$type){
                $data = DB::table('food_consume as fc')
                                ->select('fc.item_occurance','fc.food_consume_id','mr.item as itemName','fc.date')
                                ->leftJoin('meal_reference as mr','fc.item_id','=','mr.itemID')
                                ->where('fc.patient_id','=',$patientID)
                                ->whereBetween('fc.date',[$fromDate,$toDate])
                                ->where('fc.meal_type_id','>=',$type)
                                ->get();
                                // print_r($data);
                                // exit;
                return $data;


        }
        public function saveBalanceIndex($balance_array){
        
                $query = DB::table('balance_index')->insert($balance_array);

               return $query;
          }

        public function particularDateMeal($patientID,$fromDate, $toDate) {
        $arrayValue = array();
        $data = DB::table('food_consume')
                        ->select('food_consume_id','meal_type_id','date')
                        ->where('patient_id',$patientID)
                        ->where('date','>=',$fromDate)
                        ->where('date','<=',$toDate)
                        ->get();
        foreach ($data as $key => $value) {
              $value->food_consume_id."  ";
               $valueData = DB::table('reccommendation')
                                ->select('reccommendation_id','date','food_consume_id',"calorie_requirement","protein_requirement","carb_requirement","fat_requirement","consume_calorie","consume_protein","consume_carb","consume_fat")
                                ->where('food_consume_id',$value->food_consume_id)
                                ->get();
                                if ($value->meal_type_id == "B") {
                                        $meal = 'Breakfast';
                                }
                                if ($value->meal_type_id == "L") {
                                        $meal = 'Lunch';
                                }
                                if ($value->meal_type_id == "D") {
                                        $meal = 'Dinner';
                                }
                                $arrayValue[$value->date][$meal] = $valueData;
        }
        return $arrayValue;

        }
  }