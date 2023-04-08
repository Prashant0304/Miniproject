<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Groups extends Model
{
    public function savegroups($data){
        $insertId = DB::table('groups')->insertGetId($data);
        return $insertId; 
    }
    
public function getGroups(){
    $values = DB::table('groups')->where('groupStatus', '!=',0)->get();
    return $values;
}

public  function singleGroups($groupID){
    $group = DB::table('groups')
    ->where('groupID', $groupID)
    ->first();
    $members = DB::table('group_members as gm')
    ->select ('gm.patientID','gm.groupId','gr.rank','r.consumed_glycemic_load as highDiabeticRisk','gr.high_disease_risk as highDieaseRisk',DB::raw("Concat(p.firstName,' ' ,p.lastName) as fullname"))
    ->leftJoin('patients as p','gm.patientID','p.patientID')
    ->leftJoin('group_rank as gr','gr.patient_id','p.patientID')
    ->leftJoin('reccommendation as r','r.patient_id','p.patientID')
    ->leftJoin('balance_index as b','b.patientID','p.patientID')
    ->where('gm.groupID',$groupID)
    ->groupBy('gr.rank')
    ->get();
     $group->groupMembers = $members;
    

    return $group;
   
     }


public function updateGroups($groupID, $updateData){
    $query = DB::table('groups')
            ->where('groupID',$groupID)
            ->update($updateData);
            return $query;
            

}
public function deleteGroups($groupID,$deleteData){
    $query1 =    DB::table('groups')
                 ->where('groupID',$groupID)
                 ->update($deleteData);
                 return $query1;
}
public function savemember($data){
    $insertMemberId = DB::table('group_members')->insertGetId($data);
    return $insertMemberId; 
}

public function getmember($patientID){
    $val = DB::table('group_members')
    ->where('patientID',$patientID)
    ->get();
    return $val;
}

public function deleteMember($groupMemberID,$deleteMember){
    $res =    DB::table('group_members')
                 ->where('groupMemberID',$groupMemberID)
                 ->update($deleteMember);
                 return $res;
}

public  function groupRank($patientID,$groupid,$fromDate){
        // $query1 = $query2 = array();
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
    
      
      return $rank =['status'=> true, 'rank'=> $rank];
    }
}
public  function highDiseaseRisk($patientID,$groupid,$fromDate){
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

public  function highDiabeticRisk($patientID,$groupid,$Date){
    $query = DB::table('reccommendation')
    ->select('ideal_glycemic_load','consumed_glycemic_load')
    ->where('patient_id',$patientID)
    ->where('date',$Date)
    ->get();

      //var_dump($query);exit; 
      if(count($query) == 3){    
     $data = (($query[0]->{'consumed_glycemic_load'}/$query[0]->{'ideal_glycemic_load'})*100) +
             (($query[1]->{'consumed_glycemic_load'}/$query[1]->{'ideal_glycemic_load'})*100) +
             (($query[2]->{'consumed_glycemic_load'}/$query[2]->{'ideal_glycemic_load'})*100);
    //$data = $data/3;
   
    }
}



public  function groupInvite($phone,$groupID){
    $isPatient = DB::table('patients as p')
    ->select('patientID')
    ->where('phone',$phone)
    ->get();

 
    if(count($isPatient)>0){
        $isGroupMember = DB::table('group_members as gm')
        ->select('groupMemberID','patientID')
        ->where('groupID',$groupID)
        ->get();
        
      if(count($isGroupMember)< 1){
          $getURL = DB::table('groups')
          ->select('groupURL')
          ->where('groupID',$groupID)
          ->get();

          return $getURL;
      }else{
        return false;

      }

    }
       else { 
           $url = "https://play.google.com/store/apps/details?id=com.jeevini.patient";
       return $url; 
       }
 
 
}

public function calories($data){
    $dates = [
        $data['fromDate'],
        $data['toDate']
    ];

   $values = array();

    $query = DB::table("food_consume as fc")
           ->select("fc.patient_id","fc.date","fc.meal_type_id","r.calorie_requirement","r.consume_calorie","r.glycemic_msg","r.standard_msg_title",
           "r.standard_msg_description","r.increase_msg_title","r.increase_msg_description","r.decrease_msg_title","r.decrease_msg_description")                 
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
            else if($value->{'meal_type_id'} =="S"){
                $values['snacks'][] = $value;

            }
            else {
                $values['dinner'][] = $value;

            }

           }
    
        return $values;

    
        }

        public function protein($data){
            $dates = [
                $data['fromDate'],
                $data['toDate']
            ];
        
           $values = array();
        
            $query = DB::table("food_consume as fc")
                   ->select("fc.patient_id","fc.date","fc.meal_type_id","r.protein_requirement","r.consume_protein","r.glycemic_msg","r.standard_msg_title",
                   "r.standard_msg_description","r.increase_msg_title","r.increase_msg_description","r.decrease_msg_title","r.decrease_msg_description")                 
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
                    else if($value->{'meal_type_id'} =="S"){
                        $values['snacks'][] = $value;
        
                    }
                    else {
                        $values['dinner'][] = $value;
        
                    }
        
                   }
            
                return $values;
        
            
                }
                
    public function carb($data){
             $dates = [
                 $data['fromDate'],
                 $data['toDate']
                    ];
                
               $values = array();
                
               $query = DB::table("food_consume as fc")
                         ->select("fc.patient_id","fc.date","fc.meal_type_id","r.carb_requirement","r.consume_carb","r.glycemic_msg","r.standard_msg_title",
                         "r.standard_msg_description","r.increase_msg_title","r.increase_msg_description","r.decrease_msg_title","r.decrease_msg_description")                 
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
                            else if($value->{'meal_type_id'} =="S"){
                                $values['snacks'][] = $value;
                
                            }
                            else {
                                $values['dinner'][] = $value;
                
                            }
                
                           }
                    
                        return $values;
                
                    
                        }
public function fat($data){
       $dates = [
       $data['fromDate'],
       $data['toDate']
                                   ];
                               
          $values = array();
                               
          $query = DB::table("food_consume as fc")
              ->select("fc.patient_id","fc.date","fc.meal_type_id","r.fat_requirement","r.consume_fat","r.glycemic_msg","r.standard_msg_title",
              "r.standard_msg_description","r.increase_msg_title","r.increase_msg_description","r.decrease_msg_title","r.decrease_msg_description")                 
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
                        else if($value->{'meal_type_id'} =="S"){
                            $values['snacks'][] = $value;
            
                        }
                    else {
                        $values['dinner'][] = $value;
                               
                        }
                               
                    }
                                   
                        return $values;
                               
                    }             
                         
}