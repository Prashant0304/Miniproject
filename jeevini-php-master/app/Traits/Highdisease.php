<?php

namespace App\Traits;

use DB;
use Carbon\Carbon;

trait HighDisease{
    private function highDisease(){
        $query = DB::table('patients as p')
            ->join('group_members as gm','gm.patientID','p.patientID')
            ->select('gm.patientID','gm.groupID')
            ->get();
      

       $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        foreach($query as $value){
           $rank[] = $this->highDiseaseRisk($value->{"patientID"},$value->{"groupID"},$today);
           //var_dump($rank);die;
        }
       

    }
    private  function highDiseaseRisk($patientID,$groupid,$fromDate){
        $disease = 0;
        $query = DB::table('patients as p')
                ->select('p.patientID','b.balanceIndex_value','b.meal_type')
                ->join('food_consume as f','f.patient_id','=','p.patientID')
                ->join('balance_index as b','b.patientID','=','f.patient_id')
                
                ->where('p.patientID','=',$patientID)
                ->where('f.date','=',$fromDate)
                ->where('b.date_consume','=',$fromDate)
                
                ->groupBy('b.meal_type','b.balanceIndex_value')
                ->get();  
    
                //var_dump($query);exit;
    
           if( (count($query)>0) ){
    
           if(count($query) == 3){
                $disease = ($query[0]->{'balanceIndex_value'} +$query[1]->{'balanceIndex_value'}+$query[2]->{'balanceIndex_value'}) /3;     
           }
           else if(count($query) ==2){
                $disease = ($query[0]->{'balanceIndex_value'}+$query[1]->{'balanceIndex_value'}) /2;
           }
           else {
            $disease = ($query[0]->{'balanceIndex_value'});
        }
    
        $isRiskDisease = DB::table('group_rank')
        ->where('patient_id',$patientID)
        ->where('group_id',$groupid)
        ->update(['high_disease_risk'=> $disease]);
        return $disease;
    }
           
    return $disease;
    }
       
   

}
