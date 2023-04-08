<?php

namespace App\Traits;

use DB;
use Carbon\Carbon;


trait GroupRank{
    private function groupRank(){
      $query = DB::table('patients as p')
            ->join('group_members as gm','gm.patientID','p.patientID')
            ->select('gm.patientID','gm.groupID')
            ->get();
      

       $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        foreach($query as $value){
           $rank[] = $this->rank($value->{"patientID"},$value->{"groupID"},$today);
           
        }
       
       

    }

    private function rank($patientID,$groupid,$fromDate){
        // var_dump($fromDate); die;
         $rank = 0; 

        $isActivity = DB::table('user_activity as u')
                ->join('activity_status as a','a.activityID','=','u.activityID')
                ->where('patientID',$patientID)
                ->where('u.date',$fromDate)->get();
           

        if(count($isActivity)>0){
            $query = DB::table('patients as p')
            ->select('p.patientID','b.balanceIndex_value','b.meal_type','u.time','a.sedentary','u.activityID','a.active','a.very_active')
            ->join('food_consume as f','f.patient_id','=','p.patientID')
            ->join('balance_index as b','b.patientID','=','f.patient_id')
            ->join('user_activity as u','u.patientID','=','p.patientID')
            ->join('activity_status as a','a.activityID','=','u.activityID')
            ->where('p.patientID','=',$patientID)
            ->where('f.date','=',$fromDate)
            ->where('b.date_consume','=',$fromDate)
            ->where('u.date','=',$fromDate)
            ->groupBy('b.meal_type','b.balanceIndex_value')
            ->get();

        }
        else{
            $query = DB::table('patients as p')
            ->select('p.patientID','b.balanceIndex_value','b.meal_type')
            ->join('food_consume as f','f.patient_id','=','p.patientID')
            ->join('balance_index as b','b.patientID','=','f.patient_id')
            
            ->where('p.patientID','=',$patientID)
            ->where('f.date','=',$fromDate)
            ->where('b.date_consume','=',$fromDate)
            
            ->groupBy('b.meal_type','b.balanceIndex_value')
            ->get();

        }
        
      
       
     
       if( (count($query)>0) ){

       if(count($query) == 3){
            $rank = ($query[0]->{'balanceIndex_value'} +$query[1]->{'balanceIndex_value'}+$query[2]->{'balanceIndex_value'}) /3;
            $rank += 15; 
            // $rank[] = $query; 
            
       }
       else if(count($query) ==2){
            $rank = ($query[0]->{'balanceIndex_value'}+$query[1]->{'balanceIndex_value'}) /2;
            $rank += 10; 
            // $rank[] = $query; 
       }
       else {
        $rank = ($query[0]->{'balanceIndex_value'});
        $rank += 5; 
        // $rank[] = $query; 
       }
      
       if(array_key_exists("sedentary",$query[0]) ){
        if($query[0]->time != 0){
       
            if($query[0]->time <= $query[0]->sedentary){
                    $rank += 0;
            }
            else if($query[0]->time <= $query[0]->active){
                $rank += 20; 
            }
            else{
                $rank += 25;
            }
            
        }
        $rank = ($rank * 100 ) / 140 ; 
    }
    
    $is_ranked = DB::table('group_rank')
                ->where('group_id',$groupid)
                ->where('patient_id',$patientID)
                ->get();

    if(count($is_ranked)> 0){
        DB::table("group_rank")
        ->where("patient_id",$patientID)
        ->where("group_id",$groupid)
        ->update(["rank" => $rank]);

    }else{
        DB::table('group_rank')
        ->insert(['group_id'=>$groupid,
                'patient_id' => $patientID,
                'rank' => $rank,
            ]);

    }
    
      
      return $rank =['status'=> true, 'rank'=> $rank, 'p'=>$patientID,'g'=>$groupid];
    }

    }
}



?>