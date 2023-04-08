<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function insertTicket($data,$id='')
    {
        if(empty($id))
        {
            $getID = DB::table('tbl_ticket')->insertgetID($data);
            $id = $getID;
        }
        else
        {
            $getID = DB::table('tbl_ticket')->where('ticket_id',$id)->update($data);
        }
        
        $detail_data = array();
        $detail_data['ticket_id'] = $id;
        $detail_data['assignee'] = $data['assignee'];
        $detail_data['comment'] = $data['description'];
        $detail_data['priority'] = $data['priority'];
        $detail_data['ticket_status'] = $data['ticket_status'];
        $detail_data['added_on'] = $data['added_on'];
        $detail_data['updated_on'] = $data['updated_on'];
        $detailID = DB::table('tbl_ticket_history')->insertgetID($detail_data);

        return array('ticket_id'=>$id,'ticket_detail_id'=>$detailID);
    }

    public function allTicket($ticket_id='')
    {
        if(empty($ticket_id))
        {
             
          $ticket_data = DB::table('tbl_ticket_history as t')
            ->select("t.*","tbl_ticket.*","c.city as city","s.name as state","p.language as language","h.status","f.name as feeds","pf.diabetes as diabetics","pf.age as age",DB::raw("CONCAT(p.firstName,' ',p.lastName) as patientName"),"p.phone")
            ->join('tbl_ticket', 't.ticket_id', '=', 'tbl_ticket.ticket_id')
            ->join('patients as p', 'p.patientID','=','tbl_ticket.patient_id')
            ->join('cities as c','c.id','=','p.locationID')
            ->join('states as s','s.id','=','c.state_id')
            ->join('patient_factor as pf','p.patientID','=','pf.patientID')
            ->join('healthstatus as h','h.id','=','pf.healthStatusID')
            ->join('feeds as f','f.id','=','pf.feedID')
            ->get(); 
           
           
                }
        
        else
        {
           $ticket_data = DB::table('tbl_ticket_history as t')
                ->select("t.*","tb.name","tb.description","t.ticket_status","tb.patient_id","tb.priority","tb.active_status","tb.added_on","tb.updated_on","tb.status","c.city as city","s.name as state","p.language as language",
                "h.status","f.name as feeds","pf.diabetes as diabetics","pf.age as age",DB::raw("CONCAT(p.firstName,' ',p.lastName) as patientName"),"p.phone")
                ->join('tbl_ticket as tb', 't.ticket_id', '=', 'tb.ticket_id')
                ->join('patients as p', 'p.patientID','=','tb.patient_id')
                ->join('cities as c','c.id','=','p.locationID')
                ->join('states as s','s.id','=','c.state_id')
                ->join('patient_factor as pf','p.patientID','=','pf.patientID')
                ->join('healthstatus as h','h.id','=','pf.healthStatusID')
                ->join('feeds as f','f.id','=','pf.feedID')
                ->where('t.ticket_id',$ticket_id)
                ->get();

        }
        //return $ticket_data;
        $resp = array();

        if(!empty($ticket_data))
        {
           
            $i = 0;

            foreach($ticket_data as $ticket)
            {
                if(!empty($ticket->ticket_status))
                {
                    switch($ticket->ticket_status)
                    {
                        case 'Q':
                                $ticket_status = 'Queue';
                                break;
                        case 'P':
                                $ticket_status = 'In-Process';
                                break;
                        case 'C':
                                $ticket_status = 'Complete';
                                break;
                        case 'R':
                                $ticket_status = 'Rejected';
                                break;
                    }
                }
                    
                $resp[$i]['ticket_id'] = $ticket->ticket_id;
                $resp[$i]['name'] = $ticket->name;
                $resp[$i]['description'] = $ticket->description;
                $resp[$i]['ticket_status'] = $ticket->ticket_status;
                $resp[$i]['patient_id'] = $ticket->patient_id;
                $resp[$i]['patientName'] = $ticket->patientName;
                $resp[$i]['age'] = $ticket->age;
                $resp[$i]['phone'] = $ticket->phone;
                $resp[$i]['priority'] = $ticket->priority;
                $resp[$i]['city'] = $ticket->city;
                $resp[$i]['state'] = $ticket->state;
                $resp[$i]['language'] = $ticket->language;
                $resp[$i]['healthStatus'] = $ticket->status;
                $resp[$i]['diabetics'] = $ticket->diabetics;
                $resp[$i]['feeds'] = $ticket->feeds;
                $resp[$i]['created_at'] = $ticket->added_on;
                $resp[$i]['updated_at'] = $ticket->updated_on;

                if(empty($prev_ticket_id) || (!empty($prev_ticket_id) && $prev_ticket_id!=$ticket->ticket_id))
                {
                    $j = 0;
                    $k = 0;
                    $prev_ticket_id = $ticket->ticket_id;
                    if(!empty($prev_ticket_id) && $prev_ticket_id!=$ticket->ticket_id)
                    {
                        $i++;
                    }
                }

                $resp[$i]['ticket_history'][$j]['comment'] = $ticket->comment;
                $resp[$i]['ticket_history'][$k]['assignee'] = $ticket->assignee;
                 $j++;
                 $k++;
                 
                 //$i++;
            }


        }
        
        return $resp;
    }

    public function deleteTicket($ticket_id)
    {
        DB::table('tbl_ticket')->where('ticket_id', $ticket_id)->delete();
        DB::table('tbl_ticket_history')->where('ticket_id', $ticket_id)->delete();
    }
     
    public function SinglePatientTicket($patientID)
    {
            $tbl_data = DB::table('tbl_ticket_history as t')
            ->select("t.*","tbl_ticket.*","p.phone","p.language","pf.age","c.city as city","s.name as state","h.status","f.name as feeds","pf.diabetes as diabetics",DB::raw("CONCAT(p.firstName,' ',p.lastName) as patientName"))
            ->join('tbl_ticket', 't.ticket_id', '=', 'tbl_ticket.ticket_id')
            ->join('patients as p', 'p.patientID','=','tbl_ticket.patient_id')
            ->where('tbl_ticket.patient_id',$patientID)
            ->join('cities as c','c.id','=','p.locationID')
            ->join('states as s','s.id','=','c.state_id')
            ->join('patient_factor as pf','p.patientID','=','pf.patientID')
            ->join('healthstatus as h','h.id','=','pf.healthStatusID')
            ->join('feeds as f','f.id','=','pf.feedID')
            ->get();

            return  $tbl_data;
       
    }
   
    public function assignTicket($data){
        $insertId = DB::table('ticket_agent')->insertGetId($data);
        return $insertId; 
    }

    public function listAssignee(){
        $values = DB::table('ticket_agent')
        ->get();
        return $values;
    }
     
    public function agentDetail($assigneeID){
        {
            $ticket = DB::table('ticket_agent as ta')
            ->where ('assigneeID',$assigneeID)
            ->first();
            $ticket_data = DB::table('tbl_ticket_history as t')
            ->select("t.*","ta.assigneeName","ta.assigneePhone","tb.name","tb.description","tb.ticket_status","tb.patient_id","tb.priority","tb.added_on","tb.updated_on","tb.status",
                "p.phone","p.language","c.city as city","s.name as state","pf.age","pf.diabetes as diabetics","h.status","f.name as feeds",DB::raw("CONCAT(p.firstName,' ',p.lastName) as patientName"))
             ->join('ticket_agent as ta','ta.assigneeID',"=",'t.assignee')
             ->where('t.assignee',$assigneeID)
             ->join('tbl_ticket as tb', 't.ticket_id', '=', 'tb.ticket_id')
             ->join('patients as p', 'p.patientID','=','tb.patient_id')
             ->join('cities as c','c.id','=','p.locationID')
             ->join('states as s','s.id','=','c.state_id')
             ->join('patient_factor as pf','p.patientID','=','pf.patientID')
             ->join('healthstatus as h','h.id','=','pf.healthStatusID')
             ->join('feeds as f','f.id','=','pf.feedID')
             ->get();
               $ticket->ticket_details =  $ticket_data;
               return $ticket;
        
        
    
    
    /* to fetch the data weekly*/
    }
}
    public function weeklyData($Input,$docId){
        
        $date = date("Y-m-d H:i:s", strtotime($Input));
        $ticket  = DB::table('patients')   
        ->whereRaw ("month(createdAt) = month('$date') AND dayname(createdAt) = dayname('$date')  AND YEAR(createdAt)= year('$date')")
        ->where ("patients.doctorID",$docId)
        ->get();
        return $ticket;
    }

    public function ticketing()
    {
            $ticket_data = DB::table('tbl_ticket as tb')
            ->select("tb.*","p.phone","p.language","c.city as city","s.name as state",DB::raw("CONCAT(p.firstName,' ',p.lastName) as patientName"))
            ->join('patients as p', 'p.patientID','=','tb.patient_id')
            ->join('cities as c','c.id','=','p.locationID')
            ->join('states as s','s.id','=','c.state_id')
            ->get();          
        

            return $ticket_data;
    }
}