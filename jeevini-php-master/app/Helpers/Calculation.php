<?php

namespace App\Helpers;

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

class Calculation {

    public function __construct(){
        $this->_login = new Login;
        $this->_feeds = new Feeds;
    }

        public function ElderFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition','MaxAge')
                                ->where('feedID',$feedID)
                                ->where('MinAge', '<=' , $age)
                                ->where('MaxAge','>',$age)
                                ->first();
            // print_r($caloryFactorValues);exit;
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $activityFactor * $bmi_factor * $albuminFactor ) + $calorificFactorAddition ;
            // Protien Requirement Reference 1.1
            $totalProteinRequired = ($weight * 1.1 * 4);
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);

        }

        public function PregnaFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $pregnancyDetails = DB::table('pregnant_patient')
                                    ->select('*')
                                    ->where('patientID',$patientID)->first(); 
            $twins = $pregnancyDetails->twins;
            $pregnantStatus = $pregnancyDetails->pregnantStatus;
            $months = $pregnancyDetails->months;
            $age = $patientDetails->age;
            if ($months <= 6) {
                $stage = "1st";
            }
            elseif($months >= 7){
                $stage = "2nd";
            }
            $Actualweight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorPregnent($patientID);
            $activityFactor = 1;
            if ($pregnantStatus == "Delivered") {
                $caloryFactorValues  = DB::table('required_calorie_reference')
                                        ->select('ValueMultiplication','ValueAddition')
                                        ->where('feedID',$feedID)
                                        ->where('PregnancyDependant', $stage)
                                        ->first();
                $weight = $Actualweight - 0;
            }
            elseif($pregnantStatus == "Pregnant") {
                $caloryFactorValues  = DB::table('required_calorie_reference')
                ->select('ValueMultiplication','ValueAddition')
                ->where('feedID',$feedID)
                ->where('PregnancyDependant', 'Pregnant')
                ->first();
                $weight = $Actualweight - 0;
            
            if ($twins == "Yes") {
                    $bodyRef = DB::table('body_weight_pregnafeed_ref')
                                        ->select('twins')
                                        ->where('month',$months)->first();
                    $substraction = $bodyRef->twins;
                    if ($months <= 3) {
                        $weight = $Actualweight - $substraction;
                    }
                    elseif($months == 4) {
                        $weight = $Actualweight - $substraction;
                    }
                    elseif($months == 5) {
                        $weight = $Actualweight - $substraction;
                    }
                    elseif($months == 6) {
                        $weight = $Actualweight - $substraction;
                    }
                    elseif($months == 7) {
                        $weight = $Actualweight - $substraction;
                    }
                    elseif($months == 8) {
                        $weight = $Actualweight - $substraction;
                    }
                    elseif($months == 9) {
                        $weight = $Actualweight - $substraction;
                    }
            }
            elseif ($twins == "No") {
                $bodyRef = DB::table('body_weight_pregnafeed_ref')
                                    ->select('single')
                                    ->where('month',$months)->first();
                $substraction = $bodyRef->single;        
                if ($months <= 3) {
                    $weight = $Actualweight - $substraction;
                }
                elseif($months == 4) {
                    $weight = $Actualweight - $substraction;
                }
                elseif($months == 5) {
                    $weight = $Actualweight - $substraction;
                }
                elseif($months == 6) {
                    $weight = $Actualweight - $substraction;
                }
                elseif($months == 7) {
                    $weight = $Actualweight - $substraction;
                }
                elseif($months == 8) {
                    $weight = $Actualweight - $substraction;
                }
                elseif($months == 9) {
                    $weight = $Actualweight - $substraction;
                }
            }
        }

            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $bmi_factor * $albuminFactor ) + $calorificFactorAddition ;
            // Protien Requirement Reference 1.2
            // Protien Requirement Reference 23
            // echo $weight."/".$calorificFactorMultiplication."/". $calorificFactorAddition. "/". $bmi_factor ."/" . $albuminFactor ;
            $totalProteinRequired = ($weight * 1 * 4) + 23;
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }


        public function RenelFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition')
                                ->where('feedID',$feedID)
                                ->where('MinAge', '<' , $age)
                                ->where('MaxAge','>',$age)
                                ->first();
           // print_r($caloryFactorValues);exit;
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $activityFactor * $albuminFactor ) + $calorificFactorAddition ;
            // Protein Requirement Reference 1.2
            $totalProteinRequired = ($weight * 1.2 * 4);
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }

        public function OncoFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition')
                                ->where('feedID',$feedID)
                                ->first();
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $activityFactor * $bmi_factor) + $calorificFactorAddition ;
             // Protien Requirement Reference 1.2
            $totalProteinRequired = ($weight * 1 * 4);
            //60 % of requiredCalories
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }

        public function ProFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition')
                                ->where('feedID',$feedID)
                                ->where('gender',$gender)
                                ->where('MinAge','<=',$age)
                                ->where('MaxAge','>',$age)
                                ->first();
                                // print_r($caloryFactorValues);exit;
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $activityFactor * $bmi_factor) + $calorificFactorAddition ;
             // Protien Requirement Reference 1.2
             if ($age >= 15 && $age < 30) {
                 $proteinRequirement = 1.2;
             }
             elseif($age >= 30 && $age < 55) {
                $proteinRequirement = 1.1 ;
            }
            $totalProteinRequired = ($weight * $proteinRequirement * 4);
            //60 % of requiredCalories
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }

        public function PediaFeedCalculation($patientDetails){
             $patientID = $patientDetails->patientID;
             $age = $patientDetails->age;
             $weight = $patientDetails->weight;
             $height = $patientDetails->height; 
             $gender = $patientDetails->gender;
             $healthStatusID = $patientDetails->healthStatusID;
             $diabetes = $patientDetails->diabetes;
             $feedID = $patientDetails->feedID;
             $albuminFactor = $patientDetails->albuminFactor;
             $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
             if ($age < 10) {
                 $caloryFactorValues = DB::table('required_calorie_reference')
                 ->select('ValueMultiplication','ValueAddition')
                 ->where('feedID',$feedID)
                 ->where('MinAge','<=',$age)
                 ->where('MaxAge','>',$age)
                 ->first();
             }
             else{
                 $caloryFactorValues = DB::table('required_calorie_reference')
                 ->select('ValueMultiplication','ValueAddition')
                 ->where('feedID',$feedID)
                 ->where('gender',$gender)
                 ->where('MinAge','<=',$age)
                 ->where('MaxAge','>',$age)
                 ->first();
             }
             // print_r($caloryFactorValues);exit;
             $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
             $calorificFactorAddition = $caloryFactorValues->ValueAddition;
             $activityFactor = 1;
             $requiredCalories = ($weight * $calorificFactorMultiplication  * $bmi_factor) + $calorificFactorAddition ;
             // Protien Requirement Reference 1.2
             $totalProteinRequired = ($weight * 1.1 * 4);
             //60 % of requiredCalories
             $totalcarbs = ($requiredCalories * 60) / 100;
             $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
             $calculatedvalues = array(
                 'requiredCalories' => $requiredCalories,
                 'requiredProtein' => $totalProteinRequired,
                 'requiredCarbs' => $totalcarbs,
                 'requiredFats' => $totalfats,

             );
             $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }

        public function SurgiFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition')
                                ->where('feedID',$feedID)->first();
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $activityFactor * $albuminFactor* $bmi_factor) + $calorificFactorAddition ;
             // Protien Requirement Reference 1.2
            $totalProteinRequired = ($weight * 1.1 * 4);
            //60 % of requiredCalories
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }

        public function OroFeedCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition')
                                ->where('feedID',$feedID)
                                ->first();
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $albuminFactor * $activityFactor * $bmi_factor) + $calorificFactorAddition ;
             // Protien Requirement Reference 1.2
            $totalProteinRequired = ($weight * 1 * 4);
            //60 % of requiredCalories
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
        }

        public function OroFeedDCalculation($patientDetails){
            $patientID = $patientDetails->patientID;
            $age = $patientDetails->age;
            $weight = $patientDetails->weight;
            $height = $patientDetails->height;
            $gender = $patientDetails->gender;
            $healthStatusID = $patientDetails->healthStatusID;
            $diabetes = $patientDetails->diabetes;
            $feedID = $patientDetails->feedID;
            $albuminFactor = $patientDetails->albuminFactor;
            $bmi_factor = $this->_feeds->getBmiFactorGeneral($patientID);
            $caloryFactorValues = DB::table('required_calorie_reference')
                                ->select('ValueMultiplication','ValueAddition')
                                ->where('feedID',$feedID)
                                ->where('gender',$gender)->first();
            $calorificFactorMultiplication = $caloryFactorValues->ValueMultiplication;
            $calorificFactorAddition = $caloryFactorValues->ValueAddition;
            $activityFactor = 1;
            $requiredCalories = ($weight * $calorificFactorMultiplication * $activityFactor * $bmi_factor) + $calorificFactorAddition ;
             // Protien Requirement Reference 1.2
            $totalProteinRequired = ($weight * 1.2 * 4);
            //60 % of requiredCalories
            $totalcarbs = ($requiredCalories * 60) / 100;
            $totalfats = $requiredCalories - ( $totalProteinRequired + $totalcarbs);
            $calculatedvalues = array(
                'requiredCalories' => $requiredCalories,
                'requiredProtein' => $totalProteinRequired,
                'requiredCarbs' => $totalcarbs,
                'requiredFats' => $totalfats,

            );
            $updateRequiredCalories = $this->_feeds->updateRequiredCalories($calculatedvalues, $patientID);
           
        }

        public function HepaFeedCalculation($patientDetails){
        
        }

    public function  assignFeedID($patientID){
        $patientDetails = $this->_login->getPatientDetail($patientID);
        $age = $patientDetails->age;
        //statusID change
        $healthStatusID = $patientDetails->healthStatusID;
        $diabetes = $patientDetails->diabetes;
        // print_r($patientDetails);exit;
        //statusID changed
        $healthStatus = $this->_login->getHealthStatusName($healthStatusID);
        $feedID = "0";
        switch ($healthStatus) {
            case 'Pregnancy':
                $feedID  = '1';
                break;
            case 'Dialysis':
                $feedID  = '2';
                break;
            case 'Cancer':
                $feedID  = '3';
                break;
            case 'Pediatric':
                $feedID  = '6';
                break;
            case 'Surgery':
                $feedID  = '7';
                break;
            case 'Normal':
                if ($age < 55 AND $diabetes == "Yes" ) {
                    $feedID  = '9';
                }
                elseif ($age < 55 ) {
                    $feedID  = '4';
                }
                else {
                    $feedID  = '5';
                }
                break;
            case 'Stroke/Paralysis':
                if ($age >= 55  ) {
                    $feedID  = '5';
                }
                elseif ($age < 55 AND $diabetes == "No" ) {
                    $feedID  = '8';
                }
                elseif ($age < 55 AND $diabetes == "Yes" ) {
                    $feedID  = '9';
                }
                
                break;
            case 'Heart Disease':
                if ($age > 55  ) {
                    $feedID  = '5';
                }
                elseif ($age < 55 && $diabetes == "No" ) {
                    $feedID  = '8';
                }
                elseif ($age < 55 && $diabetes == "Yes" ) {
                    $feedID  = '9';
                }
                
                break;
            case 'Infection/TB/COPD':
                if ($age >= 55  ) {
                    $feedID  = '5';
                }
                elseif ($age < 55 && $diabetes == "No" ) {
                    $feedID  = '8';
                }
                elseif ($age < 55 && $diabetes == "Yes" ) {
                    $feedID  = '9';
                }
                
                break;
            case 'Liver Disease':
                $feedID  = '10';
                break;
            case 'Infection/TB/COPD':
                $feedID  = '3';
                break;
            default:
                $feedID = '123465';
                break;
        }
        $upadteGetID = $this->_login->updateFeedID($patientID,$feedID);
        }

        public  function requiredCaloriesCalculation($patientID){
            $patientDetails = $this->_login->getPatientDetail($patientID);
            $feedID = $patientDetails->feedID;
            //To Get Feed Name
            $FeedName = $this->_login->getFeedName($feedID);
            // print_r($FeedName);exit;
            switch ($FeedName) {
                case 'PregnaFeed':
                   $requiredCalories = $this->PregnaFeedCalculation($patientDetails);
                    break;
                case 'ElderFeed':
                   $requiredCalories = $this->ElderFeedCalculation($patientDetails);
                    break;
                case 'RenalFeed':
                   $requiredCalories = $this->RenelFeedCalculation($patientDetails);
                    break;
                case 'OncoFeed':
                   $requiredCalories = $this->OncoFeedCalculation($patientDetails);
                    break;
                case 'ProFeed':
                   $requiredCalories = $this->ProFeedCalculation($patientDetails);
                    break;
                case 'PediaFeed':
                   $requiredCalories = $this->PediaFeedCalculation($patientDetails);
                    break;
                case 'SurgiFeed':
                   $requiredCalories = $this->SurgiFeedCalculation($patientDetails);
                    break;
                case 'OroFeed':
                   $requiredCalories = $this->OroFeedCalculation($patientDetails);
                    
                   break;
                case 'OroFeed-d':
                   
                   $requiredCalories = $this->OroFeedDCalculation($patientDetails);
                   return $requiredCalories;
                   break;
                case 'HepaFeed':
                   $requiredCalories = $this->HepaFeedCalculation($patientDetails);
                    break;
                
                default:
                    # code...
                    break;
                }

        }

        public function deficit($patientID,$mealID,$meal){
            $patientDetails  = $this->_login->singlePatient($patientID);
            $requiredCalories = $patientDetails[0]->requiredCalories;
            $requiredProtein = $patientDetails[0]->requiredProtein;
            $requiredCarbs = $patientDetails[0]->requiredCarbs;
            $requiredFats = $patientDetails[0]->requiredFats;
            // echo $calories. " ". $requiredCarbs. " ".$requiredFats ;
             $calCalories =  0.3 *   $requiredCalories ;
             $calProtein  =  0.3 * $requiredProtein  ;
             $calCarbs  = 0.3 *  $requiredCarbs  ;
             $calFats  = 0.3 *  $requiredFats  ;
             
             
            // $mealDetails = DB::table($meal.'_consumed')->select('');
            
        }

}
