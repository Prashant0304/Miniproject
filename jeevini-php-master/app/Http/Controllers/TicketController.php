<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Ticket;
use  App\Models\Feeds;
use App\Helpers\TokenHelper;
use App\Helpers\Calculation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TicketController extends Controller
{   
    public function __construct(){
        $this->_ticket = new Ticket;
        $this->_feed = new Feeds;
        $this->_calculation = new Calculation;
    }

    public function ManageTicket(Request $request)
    {
    	$data = array();
    	$data['name'] = (!empty($request->name))?$request->name:'';
    	$data['description'] = (!empty($request->description))?$request->description:'';
    	$data['assignee'] = (!empty($request->assignee))?$request->assignee:0;
    	$data['ticket_status'] = (!empty($request->ticket_status))?$request->ticket_status:'Q';
    	$data['patient_id'] = (!empty($request->patient_id))?$request->patient_id:'';
    	$data['priority'] = (!empty($request->priority))?$request->priority:'';
       // $data['created_at'] = Carbon::now();
    	$data['added_on'] = Carbon::now('Asia/Kolkata');
    	$data['updated_on'] = Carbon::now('Asia/Kolkata');

    	if(empty($request->ticket_id))
    	{
    		$resp_data = $this->_ticket->insertTicket($data);
    	}
    	else
    	{
    		$resp_data = $this->_ticket->insertTicket($data,$request->ticket_id);
    	}
    	
    	$resp = array('code'=>200,'data'=>$resp_data);

    	echo json_encode($resp);
    }

    public function ListTicket(Request $request)
    {
        if(empty($request->ticket_id))
        {
            $ticket_data = $this->_ticket->allTicket();
        }
        else
        {       
           $ticket_data = $this->_ticket->allTicket($request->ticket_id); 
        }
    	
        if(!empty($ticket_data))
    	{
    		$resp = array('code'=>'200','data'=>$ticket_data);
    	}
    	else
    	{
    		$resp = array('code'=>'400','data'=>null);
    	}

    	echo json_encode($resp);
    }

    public function DeleteTicket(Request $request)
    {
    	$this->_ticket->deleteTicket($request->ticket_id);

    	$resp = array('code'=>'200','data'=>null);
    	echo json_encode($resp);
    }

    public function AddComment(Request $request)
    {
        if(empty($request->ticket_id) || empty($request->comment)|| empty($request->assignee)|| empty($request->ticket_status))
        {
            $resp = array('msg'=>'Mandatory Parameter Missing','data'=>null);
        }
        else
        {
            $detail_data = array();
            $detail_data['ticket_id'] = $request->ticket_id;
            $detail_data['assignee'] = $request->assignee;
            $detail_data['comment'] = $request->comment;
            $detail_data['priority'] = $request->priority;
            $detail_data['ticket_status'] = $request->ticket_status;
            $detail_data['type'] = $request->type;
            $detail_data['added_on'] = Carbon::now('Asia/Kolkata');
            $detail_data['updated_on'] = Carbon::now('Asia/Kolkata');
            $detailID = DB::table('tbl_ticket_history')->insertgetID($detail_data);

            $resp = array('msg'=>'Comment Added Successfully','data'=>$detailID);
        }

        echo json_encode($resp);
        exit;
    }

    public function singlePatientTicket(Request $request){
        $patientID = $request->header('patientID');
        $ticket = $this->_ticket->singlePatientTicket($patientID);
        return json_encode($ticket);
    }
      
    public function ActivityStream(Request $request){
        $agentID = $request->header('agentID');
        $value = $this->_ticket->ActivityStream($agentID);
        return json_encode($agentID);   

    }
     
    public function assignTicket(Request $request){
                                 
        $data = array();
        $response = array();
    
        $data["assigneeName"] = $request->assigneeName;
        $data["assigneePhone"] = $request->assigneePhone;

        $inserted = $this->_ticket->assignTicket($data);

        if($inserted >0 ){
        $response['assigneeID'] = $inserted;
        $response['message ']= "Name stored successfully ";

    }
    else{
        $response['assigneeID'] = false;
        $response['message ']= "Name stored un-successfully ";

    }
    return response(json_encode($response),200);

}

public function listAssignee(){
    $values = $this->_ticket->listAssignee();
    return json_encode($values);
}

public function agentDetail(Request $request){
    $assigneeID = $request->assigneeID;
    $value = $this->_ticket->agentDetail($assigneeID);
    return json_encode($value);
}

     /* To fetch weekly data */
        public function weeklyData(Request $request){
            $date = $request->date;
            $data = $request->docId;
            $value = $this->_ticket->weeklyData($date,$data);
            return json_encode($value);

        }
     
        public function ticketing(){
            $data = $this->_ticket->ticketing();
            return json_encode($data);
  
          }

    }