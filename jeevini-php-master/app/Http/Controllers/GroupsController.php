<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Groups;
use Illuminate\Support\Facades\URL;


class GroupsController extends Controller
{
    public function __construct(){
        $this->_groups = new Groups;
}

public function CreateGroup(Request $request){
    
    $data = array();
    $response = array();

    $data["groupName"] = $request->groupName;
    $data["groupIcon"] = $request->groupIcon;
    $data["groupAdmin"] = $request->header('patientId');
    //generating url with 13 digits . 
    $id = rand(pow(10, 13-1), pow(10, 13)-1);
    $groupURL =url("groups/invite?id={$id}");
    $data['groupURL'] = $groupURL;
        //   base_url();
   
   
    $inserted = $this->_groups->savegroups($data);
    if($inserted >0 ){
        $response['groupId'] = $inserted;
        $response['message ']= "Group created by successfully ";

    }
    else{
        $response['groupId'] = false;
        $response['message ']= "Group created by un-successfully ";

    }
    return response(json_encode($response),200);


}
 public function getGroups(){
            $values = $this->_groups->getGroups();
            return json_encode($values);
    }


 public function singleGroups(Request $request){
    $groupId = $request->header('groupId');
    $groups = $this->_groups->singleGroups($groupId);
  // return $resp = array('groupName'=>$groupName,'groupDescription'=>$groupDescription,'groupIcon'=>$groupIcon,'data'=>$groups); 
    return json_encode($groups);
    
}

 public function updateGroups(Request $request){
    $groupId = $request->header('groupID');
    $updateData = $request->json()->all();
    // print_r($updateData);exit;
    $query = $this->_groups->updateGroups($groupId, $updateData);
    return json_encode($query);
 }


 public function deleteGroups(Request $request){
    $groupId = $request->header('groupID');
    $deleteData = $request->json()->all();
    $query1 = $this->_groups->deleteGroups($groupId, $deleteData);

    
    return json_encode($query1);
 }


 public function CreateMember(Request $request){
    $data = array();
    $response = array();

    $data["groupId"] = $request->header('groupId');
    $data["patientId"] = $request->header('patientId');
    $data["addedBy"] = $request->addedBy;
    $data["status"] = $request->status;

   
    $created = $this->_groups->savemember($data);
    if($created >0 ){
        $response['groupMemberId'] = $created;
        $response['message ']= "Group member created by successfully ";

    }
    else{
        $response['groupMemberId'] = false;
        $response['message ']= "Group member created by un-successfully ";

    }
    return response(json_encode($response),200);
  
}


 public function getmember(Request $request){
    $patientID = $request->header('patientID');
    $val = $this->_groups->getmember($patientID);
    return json_encode($val);
}


 public function deleteMember(Request $request){
    $groupMemberId = $request->header('groupMemberID');
    $deleteMember = $request->json()->all();
    $res = $this->_groups->deleteMember($groupMemberId, $deleteMember);

    
    return json_encode($res);
 }

 public function groupRank(Request $request){
    $groupID = $request->header('groupID');
    $patientID = $request->header('patientID');
    $fromDate = $request->header('fromDate');
    $val = $this->_groups->groupRank($patientID,$groupID,$fromDate);
    return json_encode($val);
  
   }

   public function highDiseaseRisk(Request $request){
    $groupID = $request->header('groupID');
    $patientID = $request->header('patientID');
    $fromDate = $request->header('fromDate');
    $val = $this->_groups->highDiseaseRisk($patientID,$groupID,$fromDate);
    return json_encode($val);
  
   }

   public function highDiabeticRisk(Request $request){
    $groupID = $request->header('groupID');
    $patientID = $request->header('patientID');
    $Date = $request->header('Date');
    $val = $this->_groups->highDiabeticRisk($patientID,$groupID,$Date);
    return json_encode($val);
  


    }
   public function groupInvite(Request $request){
    $phone = $request->header('phone');
     $groupID = $request->header('groupID');
     $data = $this->_groups->groupInvite($phone,$groupID);  
     return json_encode($data);
 }
     
 public function calories(Request $request){
    $data = array();
    $data["PatientID"] = $request->patientID;
    $data["fromDate"] = $request->fromDate;
    $data["toDate"] = $request->toDate;
    $res = $this->_groups->calories($data);
   return $res;
    
}
public function protein(Request $request){
    $data = array();
    $data["PatientID"] = $request->patientID;
    $data["fromDate"] = $request->fromDate;
    $data["toDate"] = $request->toDate;
    $res = $this->_groups->protein($data);
   return $res;
    
}
public function carb(Request $request){
    $data = array();
    $data["PatientID"] = $request->patientID;
    $data["fromDate"] = $request->fromDate;
    $data["toDate"] = $request->toDate;
    $res = $this->_groups->carb($data);
   return $res;
    
}
public function fat(Request $request){
    $data = array();
    $data["PatientID"] = $request->patientID;
    $data["fromDate"] = $request->fromDate;
    $data["toDate"] = $request->toDate;
    $res = $this->_groups->fat($data);
   return $res;
    
}
 
}