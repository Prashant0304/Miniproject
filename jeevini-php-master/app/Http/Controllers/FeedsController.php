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

class FeedsController extends Controller
{   
    public function __construct(){
        $this->_feed = new Feeds;
        $this->_calculation = new Calculation;
    }
    
    public function insertFeed(Request $request){
        $feedData = $request->json()->all();
        $insert = $this->_feed->insertFeedModel($feedData);

        print_r($feedData);exit;

    }

    public function HealthStatusList(){
            $healthStatus = $this->_feed->healthStatusList();
            return json_encode($healthStatus);
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

        // public function bmi(){
            
        //     $check = $this->_feed->getBmiFactorGeneral(94);

        //     echo $check;
        // }

}
