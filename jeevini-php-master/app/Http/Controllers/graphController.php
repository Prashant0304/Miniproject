<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use DB;

class graphController extends Controller
{
    public function calories(Request $request)
  {
    $patient_id=$request->input('patient_id');
    $days=$request->input('days');

   $Breakfast = DB::table('food_consume')
    ->select('patient_id','consume_calorie','requiredcalories','meal_type_id','date')
   ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
   ->where('patient_id', $patient_id)
   ->where('meal_type_id', 'B')
   ->where('date','>=',Carbon::now()->subdays($days))
    ->get(['consume_calorie','requiredcalories','meal_type_id','date']);

    
    
   
   $Lunch = DB::table('food_consume')
   ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
   ->where('patient_id', $patient_id)
   ->where('meal_type_id', 'L')
   ->where('date','>=',Carbon::now()->subdays($days))
    ->select('patient_id','consume_calorie','requiredcalories','meal_type_id','date')
   ->get();


   $Snacks = DB::table('food_consume')
   ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
   ->where('patient_id', $patient_id)
   ->where('meal_type_id', 'S')
   ->where('date','>=',Carbon::now()->subdays($days))
    ->select('patient_id','consume_calorie','requiredcalories','meal_type_id','date')
   ->get();

  

   $Dinner = DB::table('food_consume')
   ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
   ->where('patient_id', $patient_id)
   ->where('meal_type_id', 'D')
   ->where('date','>=',Carbon::now()->subdays($days))
   ->select('patient_id','consume_calorie','requiredcalories','meal_type_id','date')
   ->get();
  return response()->json(array($Breakfast,$Lunch,$Snacks,$Dinner));
  }
 



 //protiens 

public function protiens(Request $request)
{
  $patient_id=$request->input('patient_id');
  $days=$request->input('days');

 $Breakfast = DB::table('food_consume')
  ->select('patient_id','consume_protein','requiredProtein','meal_type_id','date')
 ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
 ->where('patient_id', $patient_id)
 ->where('meal_type_id', 'B')
 ->where('date','>=',Carbon::now()->subdays($days))
  ->get(['patient_id','consume_protein','requiredProtein','meal_type_id','date']);

  
 
 
 $Lunch = DB::table('food_consume')
 ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
 ->where('patient_id', $patient_id)
 ->where('meal_type_id', 'L')
 ->where('date','>=',Carbon::now()->subdays($days))
  ->select('patient_id','consume_protein','requiredProtein','meal_type_id','date')
 ->get();

 $Snacks = DB::table('food_consume')
 ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
 ->where('patient_id', $patient_id)
 ->where('meal_type_id', 'S')
 ->where('date','>=',Carbon::now()->subdays($days))
  ->select('patient_id','consume_protein','requiredProtein','meal_type_id','date')
 ->get();



 $Dinner = DB::table('food_consume')
 ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
 ->where('patient_id', $patient_id)
 ->where('meal_type_id', 'D')
 ->where('date','>=',Carbon::now()->subdays($days))
 ->select('patient_id','consume_protein','requiredProtein','meal_type_id','date')
 ->get();
return response()->json(array($Breakfast,$Lunch,$Snacks,$Dinner));
}


//gireesh code starts here

public function Fats(Request $request)
  {
     $patient_id=$request->input('patient_id');
     $days=$request->input('days');

    $Breakfast = DB::table('food_consume')
     ->select('patient_id','consume_fat','requiredFats','meal_type_id','date')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'B')
    ->where('date','>=',Carbon::now()->subdays($days))
     ->get();

     
      
    
    $Lunch = DB::table('food_consume')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'L')
    ->where('date','>=',Carbon::now()->subdays($days))
     ->select('patient_id','consume_fat','requiredFats','meal_type_id','date')
    ->get();


    $Snacks = DB::table('food_consume')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'S')
    ->where('date','>=',Carbon::now()->subdays($days))
     ->select('patient_id','consume_fat','requiredFats','meal_type_id','date')
    ->get();

   

    $Dinner = DB::table('food_consume')
    ->select('patient_id','consume_fat','requiredFats','meal_type_id','date')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'D')
    ->where('date','>=',Carbon::now()->subdays($days))
    ->get();

   return response()->json(array($Breakfast,$Lunch,$Snacks,$Dinner));
   
  }


  public function Carbs(Request $request)
  {
     $patient_id=$request->input('patient_id');
     $days=$request->input('days');

    $Breakfast = DB::table('food_consume')
    ->select('patient_id','consume_carb','requiredCarbs','meal_type_id','date')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'B')
    ->where('date','>=',Carbon::now()->subdays($days))
    ->get();

     
      
     $Lunch = DB::table('food_consume')
    ->select('patient_id','consume_carb','requiredCarbs','meal_type_id','date')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'L')
    ->where('date','>=',Carbon::now()->subdays($days))
    ->get();

    $Snacks = DB::table('food_consume')
    ->select('patient_id','consume_carb','requiredCarbs','meal_type_id','date')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'S')
    ->where('date','>=',Carbon::now()->subdays($days))
    ->get();

   

    $Dinner = DB::table('food_consume')
    ->select('patient_id','consume_carb','requiredCarbs','meal_type_id','date')
    ->join('patient_factor', 'food_consume.patient_id', '=', 'patient_factor.patientID')
    ->where('patient_id', $patient_id)
    ->where('meal_type_id', 'D')
    ->where('date','>=',Carbon::now()->subdays($days))
    ->get();

   return response()->json(array($Breakfast,$Lunch,$Snacks,$Dinner));

  }
}
