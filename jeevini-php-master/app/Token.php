<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    
    
    //Listing all tokens created
    public function listalltoken()
    {
        $data = DB::select(DB::raw("select count(*) as Total_Tokens from info_from_customers"));
        $save = DB::table('info_from_customers')
                    ->get();
             return array($data,$save);
    }

    public function countisAttended()
    {
        
        $data = DB::select(DB::raw("select count(*) as Pending from info_from_customers where isAttended='P'"));
        //return $data;
        $data1 = DB::select(DB::raw("select count(*) as Attending from info_from_customers where isAttended='A'"));

             return array($data,$data1);
    }
    
    //storing customeragent details to database
    public function addCustomercare($data){
        $insert = DB::table('diatitian_customer_care')->insertGetID($data);
        return $insert;
       
    }

    public function updateCustomercare($update_customer_data,$customercare_id)
    {
        $insert = DB::table('diatitian_customer_care')->where('customercare_id', $customercare_id)->update($update_customer_data);
    }
    
    //retrieve all customer agent details
    public function listcustomercare()
    {
        $values = DB::table('diatitian_customer_care')
        ->select('customercare_id','FirstName','LastName','Location',
        'speciality','customercare_phone','Age','Email_id','created_at','updated_at')->get();
        return $values;
    }


    //checking whether customer agent comment added or not
    public function allToken($token_id='',$customercare_id='')
    {
        if(empty($token_id) || empty($customercare_id))
        {
             
            $token_data = DB::table('token_history as t')
            ->select("t.*","d.ID","d.patientName","d.mobile_no","d.patientComplent","tb.isAttended","t.token_status","t.customercare_comment","c.customercare_id",DB::raw("CONCAT(c.FirstName,' ',c.LastName) as customercareName"),DB::raw("CONCAT(dr.Dr_FirstName,' ',dr.Dr_LastName) as DoctorName"),"t.created_at","t.updated_at")
            ->join('tbl_token as tb', 'tb.token_id', '=', 't.token_id')
            ->join('info_from_customers as d', 'd.ID','=','tb.diabetes_id')
            ->join('diabetes_patient as d', 'd.diabetes_id','=','tb.diabetes_id')
           ->join('diatitian_customer_care as c', 'c.customercare_id','=','t.customercare_id')
           ->join('tbl_doctor as dr', 'dr.doctor_id','=','t.doctor_id')
            ->get(); 
        }
        else
        {
            $token_data = DB::table('token_history as t')
            ->select("t.*","d.ID","d.patientName","d.mobile_no","d.patientComplent","tb.isAttended","t.token_status","t.customercare_comment","c.customercare_id",DB::raw("CONCAT(c.FirstName,' ',c.LastName) as customercareName"),DB::raw("CONCAT(dr.Dr_FirstName,' ',dr.Dr_LastName) as DoctorName"),"t.created_at","t.updated_at")
            ->join('tbl_token as tb', 'tb.token_id', '=', 't.token_id')
            ->join('info_from_customers as d', 'd.ID','=','tb.diabetes_id')
            // ->join('diabetes_patient as d', 'd.diabetes_id','=','tb.diabetes_id')
           ->join('diatitian_customer_care as c', 't.customercare_id','=','c.customercare_id')
           ->join('tbl_doctor as dr', 'dr.doctor_id','=','t.doctor_id')
            ->where('t.token_id',$token_id)
            ->where('t.customercare_id',$customercare_id)
            ->get(); 
           
            $resp = array();

            if(!empty($token_data))
            {
           
               $i = 0;

               foreach($token_data as $token)
               {
                 if(!empty($token->token_status))
                 {
                     switch($token->token_status)
                     {
                        /* case '1':
                            $token_status = 'Whatsapp Token';
                            break;*/
                         case '1':
                            $token_status = 'Doctor Token';
                            break;
                         case '2':
                            $token_status = 'Dietitian Token';
                            break;
                         case '3':
                            $token_status = 'FE Token';
                            break;

                         case '4':
                            $token_status = 'Solved Token';
                            break;

                         case '5':
                            $token_status = 'Rejected Token';
                            break;
                        }
                    }
                    
                    $resp[$i]['token_id'] = $token->token_id;
                    $resp[$i]['patient_id'] = $token->ID;
                    $resp[$i]['patient_name'] = $token->patientName;
                    $resp[$i]['phone'] = $token->mobile_no;
                    $resp[$i]['patientComplent'] = $token->patientComplent;
                    $resp[$i]['customercare_id'] = $token->customercare_id;
                    $resp[$i]['customercareName'] = $token->customercareName;
                    //$resp[$i]['description'] = $token->customercare_comment;
                    $resp[$i]['created_at'] = $token->created_at;
                    $resp[$i]['updated_at'] = $token->updated_at;
                    $resp[$i]['token_status'] = $token->token_status;
                
                    $resp[$i]['isAttended'] = $token->isAttended;
                
                    if(empty($prev_token_id) || (!empty($prev_token_id) && $prev_token_id!=$token->token_id))
                    {
                        $j = 0;
                        $k = 0;
                        $prev_token_id = $token->token_id;
                        if(!empty($prev_token_id) && $prev_token_id!=$token->token_id)
                        {
                            $i++;
                        }
                    }

                   $resp[$i]['token_history'][$j]['comment'] = $token->customercare_comment;
                   $resp[$i]['token_history'][$k]['doctor_id'] = $token->doctor_id;
                   $resp[$i]['token_history'][$k]['DoctorName'] = $token->DoctorName;
                   $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                   $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;
                   $j++;
                   $k++;
                 //$i++;
                }
            }
            return $resp;
        }
    }
    //storing doctor details to database
    public function addDoctor($insert_doctor_data)
    {
        $insert = DB::table('tbl_doctor')->insertGetID($insert_doctor_data);
        return $insert;
    }  

    public function updatedoctor($update_doctor_data,$doctor_id)
    {
        $insert = DB::table('tbl_doctor')->where('doctor_id', $doctor_id)->update($update_doctor_data);
    }

    //retrieve all doctor data
    public function listdocotr()
    {
        $values = DB::table('tbl_doctor')
        ->select('doctor_id','Dr_FirstName','Dr_LastName','Dr_Location',
        'Dr_speciality','doctor_phone','Dr_Age','Dr_Email_id','Dr_Register_no',
        'created_at','updated_at')->get();
        return $values;
    }

    //check whether doctor comment added or not
    public function alldoctorToken($ID='',$token_id='')
    {
        if((empty($ID)) || (empty($token_id)))
        {
             
            $token_data=DB::table('doctor_token_history as t')
            ->select("t.*")
            
            ->get();
            return ($token_data);
        }

        else
        {
            $token_data=DB::table('doctor_token_history as t')
            ->select("c.ID","c.patientName","c.mobile_no","c.patientComplent","t.token_id","t.doctor_id","t.token_status","t.doctor_comment","t.dietitian_id",
            DB::raw("CONCAT(dt.D_FirstName,' ',dt.D_LastName) as DietitianName"),"t.isAttended",DB::raw("CONCAT(dr.Dr_FirstName,' ',dr.Dr_LastName) as DoctorName"),"t.created_at","t.updated_at")
            ->join('tbl_dietitian as dt','dt.dietitian_id','=','t.dietitian_id')
            ->join('tbl_doctor as dr','dr.doctor_id','=','t.doctor_id')
            //->join('tbl_token as tb', 'tb.token_id', '=', 't.token_id')
            ->join('info_from_customers as c','c.ID','=','t.ID')
            ->where('t.token_id',$token_id)->get();
            

            /*$token_data1=DB::table('token_history as th')->select('th.customercare_comment','th.customercare_id',DB::raw("CONCAT(d.FirstName,' ',d.LastName) as CustomercareName"),'th.created_at','th.updated_at')
            ->join('diatitian_customer_care as d','d.customercare_id','=','th.customercare_id')->where('th.doctor_id',$doctor_id)->get();
            */
               //return array($token_data,$token_data1);

            $resp = array();

            if(!empty($token_data))
            {
           
               $i = 0;

               foreach($token_data as $token)
               {
                 if(!empty($token->token_status))
                 {
                     switch($token->token_status)
                     {
                         
                         case '1':
                            $token_status = 'Doctor Token';
                            break;
                         case '2':
                            $token_status = 'Dietitian Token';
                            break;
                         case '3':
                            $token_status = 'FE Token';
                            break;

                         case '4':
                            $token_status = 'Solved Token';
                            break;

                         case '5':
                            $token_status = 'Rejected Token';
                            break;
                        }
                    }
                    
                    $resp[$i]['ID'] = $ID;
                    $resp[$i]['token_id'] = $token->token_id;
                    $resp[$i]['patient_name'] = $token->patientName;
                    $resp[$i]['phone'] = $token->mobile_no;
                    $resp[$i]['patientComplent'] = $token->patientComplent;
                    $resp[$i]['token_status'] = $token->token_status;
                    $resp[$i]['isAttended'] = $token->isAttended;
                    $resp[$i]['Doctor_id'] = $token->doctor_id;
                    $resp[$i]['DoctorName'] = $token->DoctorName;
    
                    if(empty($prev_token_id) || (!empty($prev_token_id) && $prev_token_id!=$prev_token_id))
                    {
                        $j = 0;
                        $k = 0;
                        $prev_token_id = $token_id;
                        if(!empty($prev_token_id) && $prev_token_id!=$token_id)
                        {
                            $i++;
                    }
                }
                $resp[$i]['token_history'][$j]['Doctor comment'] = $token->doctor_comment;
                $resp[$i]['token_history'][$k]['Dietitian_id'] = $token->dietitian_id;
                $resp[$i]['token_history'][$k]['DietitianName'] = $token->DietitianName;
                $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                $j++;
                $k++;
            }
           /* foreach($token_data1 as $token)
               {
                $resp[$i]['token_history'][$k]['customercare_id'] = $token->customercare_id;
                $resp[$i]['token_history'][$k]['customercare_name'] = $token->CustomercareName;
                $resp[$i]['token_history'][$j]['customercare_comment'] = $token->customercare_comment;
                $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;
                $j++;   
                $k++;
            }*/
            }


        }
        
        return $resp;
    }



    public function countdoctorisAttended()
    {
        
        $data = DB::select(DB::raw("select count(*) as Pending from doctor_token_history where isAttended='P'"));
        //return $data;
        $data1 = DB::select(DB::raw("select count(*) as Attending from doctor_token_history where isAttended='A'"));

             return array($data,$data1);
    }
    //storing dietitian details to database
    public function addDietitian($insert_dietitian_data){
        $insert = DB::table('tbl_dietitian')->insertGetID($insert_dietitian_data);
        return $insert;
       
    }   
    
    public function updatedietitian($update_dietitian_data,$dietitian_id)
    {
        $insert = DB::table('tbl_dietitian')->where('dietitian_id', $dietitian_id)->update($update_dietitian_data);
    }

    //retrieve all dietitian details
    public function listdietitian()
    {
        $values = DB::table('tbl_dietitian')
        ->select('dietitian_id','D_FirstName','D_LastName','D_Location',
        'D_speciality','dietitian_phone','D_Age','D_Email_id','created_at','updated_at')->get();

        return $values;
    }
    
    public function countdietitianisAttended()
    {
        
        $data = DB::select(DB::raw("select count(*) as Pending from dietitian_token_history where isAttended='P'"));
        //return $data;
        $data1 = DB::select(DB::raw("select count(*) as Attending from dietitian_token_history where isAttended='A'"));

             return array($data,$data1);
    }

    //checking dietitian comment added or not
   /* public function alldietitianToken($ID='',$dietitian_id='')
    {
        if((empty($ID)) || (empty($dietitian_id)))
        {
             
            $token_data=DB::table('dietitian_token_history as t')
            ->select("t.*")
            ->get();
            return $token_data;
        }

        else
        {
            $token_data=DB::table('dietitian_token_history as t')
            ->select("c.ID","c.patientName","c.mobile_no","c.patientComplent","t.token_id","t.token_status","t.isAttended","t.dietitian_id",
            "t.dietitian_comment","t.Feildagent_id",DB::raw("CONCAT(dt.D_FirstName,' ',dt.D_LastName) as DietitianName"),
            "t.created_at","t.updated_at",DB::raw("CONCAT(f.F_FirstName,' ',f.F_LastName) as FeilagentName"))
            ->join('feild_agent as f','f.Feildagent_id','=','t.Feildagent_id')
            ->join('tbl_dietitian as dt','dt.dietitian_id','=','t.dietitian_id')
            //->join('tbl_token as tb', 'tb.token_id', '=', 't.token_id')
            ->join('info_from_customers as c','c.ID','=','t.ID')
            ->where('t.dietitian_id',$dietitian_id)->get();
            
            $token_data1=DB::table('doctor_token_history as th')
            ->join('tbl_doctor as td','td.doctor_id','=','th.doctor_id')->select(DB::raw("CONCAT(td.Dr_FirstName,' ',td.Dr_LastName) as DoctorName"),'th.doctor_comment','th.doctor_id','th.created_at','th.updated_at')->where('th.dietitian_id',$dietitian_id)->get();
            //return array($token_data,$token_data1);

            $resp = array();

            if(!empty($token_data) && !empty($token_data1))
            {
           
               $i = 0;

               foreach($token_data as $token)
               {
                 if(!empty($token->token_status))
                 {
                     switch($token->token_status)
                     {
                         
                         case '1':
                            $token_status = 'Doctor Token';
                            break;
                         case '2':
                            $token_status = 'Dietitian Token';
                            break;
                         case '3':
                            $token_status = 'FE Token';
                            break;

                         case '4':
                            $token_status = 'Solved Token';
                            break;

                         case '5':
                            $token_status = 'Rejected Token';
                            break;
                        }
                    }
                    $resp[$i]['ID'] =$ID;
                    $resp[$i]['token_id'] = $token->token_id;
                    $resp[$i]['patient_name'] = $token->patientName;
                    $resp[$i]['phone'] = $token->mobile_no;
                    $resp[$i]['patientComplent'] = $token->patientComplent;
                    $resp[$i]['token_status'] = $token->token_status;
                    $resp[$i]['isAttended'] = $token->isAttended;
                    $resp[$i]['Dietitian_id'] = $token->dietitian_id;
                    $resp[$i]['DietitianName'] = $token->DietitianName;                                  
                    if(empty($prev_patient_id) || (!empty($prev_patient_id) && $prev_patient_id!=$patient_id))
                    {
                        $j = 0;
                        $k = 0;
                        $prev_ID = $ID;
                        if(!empty($prev_ID) && $prev_ID!=$ID)
                        {
                            $i++;
                        }
                    }
               
                 $resp[$i]['token_history'][$j]['Dietitian comment'] = $token->dietitian_comment;
                 $resp[$i]['token_history'][$k]['Feildagent ID'] = $token->Feildagent_id;
                 $resp[$i]['token_history'][$k]['Feildagent name'] = $token->FeilagentName;
                 $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                 $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;
                 $j++;
                 $k++;
                }
                foreach($token_data1 as $token)
                {
                  $resp[$i]['token_history'][$k]['Doctor_id'] = $token->doctor_id;
                  $resp[$i]['token_history'][$k]['Doctor Name'] = $token->DoctorName;
                  $resp[$i]['token_history'][$j]['Doctor comment'] = $token->doctor_comment;
                  $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                  $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                  $j++;   
                  $k++;
                }
            }


        }
        
        return $resp;
    }*/

    public function alldietitianToken($ID='',$token_id='')
    {
        if((empty($ID)) || (empty($token_id)))
        {
             
            $token_data=DB::table('dietitian_token_history as t')
            ->select("t.*")
            ->get();
            return $token_data;
        }

        else
        {
            $token_data=DB::table('dietitian_token_history as t')
            ->select("c.ID","c.patientName","c.mobile_no","c.patientComplent","t.token_id","t.token_status","t.isAttended","t.dietitian_id",
            "t.dietitian_comment","t.Feildagent_id",DB::raw("CONCAT(dt.D_FirstName,' ',dt.D_LastName) as DietitianName"),
            "t.created_at","t.updated_at",DB::raw("CONCAT(f.F_FirstName,' ',f.F_LastName) as FeilagentName"))
            ->join('feild_agent as f','f.Feildagent_id','=','t.Feildagent_id')
            ->join('tbl_dietitian as dt','dt.dietitian_id','=','t.dietitian_id')
            //->join('tbl_token as tb', 'tb.token_id', '=', 't.token_id')
            ->join('info_from_customers as c','c.ID','=','t.ID')
            ->where('t.token_id',$token_id)->get();
            
            $token_data1=DB::table('doctor_token_history as th')
            ->join('tbl_doctor as td','td.doctor_id','=','th.doctor_id')->select(DB::raw("CONCAT(td.Dr_FirstName,' ',td.Dr_LastName) as DoctorName"),'th.doctor_comment','th.doctor_id','th.created_at','th.updated_at')->where('th.token_id',$token_id)->get();
            //return array($token_data,$token_data1);

            $resp = array();

            if(!empty($token_data) && !empty($token_data1))
            {
           
               $i = 0;

               foreach($token_data as $token)
               {
                 if(!empty($token->token_status))
                 {
                     switch($token->token_status)
                     {
                         
                         case '1':
                            $token_status = 'Doctor Token';
                            break;
                         case '2':
                            $token_status = 'Dietitian Token';
                            break;
                         case '3':
                            $token_status = 'FE Token';
                            break;

                         case '4':
                            $token_status = 'Solved Token';
                            break;

                         case '5':
                            $token_status = 'Rejected Token';
                            break;
                        }
                    }
                    $resp[$i]['ID'] = $ID;
                    $resp[$i]['token_id'] = $token->token_id;
                    $resp[$i]['patient_name'] = $token->patientName;
                    $resp[$i]['phone'] = $token->mobile_no;
                    $resp[$i]['patientComplent'] = $token->patientComplent;
                    $resp[$i]['Dietitian_id'] = $token->dietitian_id;
                    $resp[$i]['DietitiaName'] = $token->DietitianName;
                    $resp[$i]['token_status'] = $token->token_status;
                    
                    $resp[$i]['isAttended'] = $token->isAttended;
                                                      
                    if(empty($prev_token_id) || (!empty($prev_token_id) && $prev_token_id!=$token_id))
                    {
                        $j = 0;
                        $k = 0;
                        $prev_token_id = $token_id;
                        if(!empty($prev_token_id) && $prev_token_id!=$token_id)
                        {
                            $i++;
                    }
                }
                $resp[$i]['token_history'][$j]['Dietitian comment'] = $token->dietitian_comment;
                $resp[$i]['token_history'][$k]['Feildagent_id'] = $token->Feildagent_id;
                $resp[$i]['token_history'][$k]['FeildagentName'] = $token->FeilagentName;
                $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                $j++;
                $k++;
            }
            foreach($token_data1 as $token)
               {
                $resp[$i]['token_history'][$k]['Doctor_id'] = $token->doctor_id;
                $resp[$i]['token_history'][$k]['DoctorName'] = $token->DoctorName;
                $resp[$i]['token_history'][$j]['Doctor comment'] = $token->doctor_comment;
                $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                $j++;   
                $k++;
            }

            
            }


        }
        
        return $resp;
    }





    //storing dietitian details to database
    public function addFeildagent($insert_feildagent_data)
    {
        $insert = DB::table('feild_agent')->insertGetID($insert_feildagent_data);
        return $insert;
           
    }   

    public function updatefeildagent($update_feildagent_data,$Feildagent_id)
    {
        $insert = DB::table('feild_agent')->where('Feildagent_id', $Feildagent_id)->update($update_feildagent_data);
    }

    //List all feild agent details
    public function listfeildagent()
    {
        $values = DB::table('feild_agent')
        ->select('Feildagent_id','F_FirstName','F_LastName','F_Location',
        'F_speciality','Feildagent_phone','F_Age','F_Email_id','created_at','updated_at')->get();
            
        return $values;    
    }

    public function countagentisAttended()
    {
        
        $data = DB::select(DB::raw("select count(*) as Pending from agent_token_history where isAttended='P'"));
        //return $data;
        $data1 = DB::select(DB::raw("select count(*) as Attending from agent_token_history where isAttended='A'"));

             return array($data,$data1);
    }

    public function allfeildagentToken($ID='',$token_id='')
    {
        if((empty($ID)) || (empty($token_id)))
        {
             
            $token_data=DB::table('agent_token_history as t')
            ->select("t.*")
            ->get();
            return $token_data;
        }

        else
        {
            $token_data=DB::table('agent_token_history as t')
            ->select("c.ID","c.patientName","c.mobile_no","c.patientComplent","t.token_id","t.token_status","t.isAttended","t.Feildagent_id",
            "t.agent_comment","t.created_at","t.updated_at",DB::raw("CONCAT(f.F_FirstName,' ',f.F_LastName) as FeilagentName"))
            ->join('feild_agent as f','f.Feildagent_id','=','t.Feildagent_id')
            //->join('tbl_dietitian as dt','dt.dietitian_id','=','t.dietitian_id')
            //->join('tbl_token as tb', 'tb.token_id', '=', 't.token_id')
            ->join('info_from_customers as c','c.ID','=','t.ID')
            ->where('t.token_id',$token_id)->get();

            $token_data1=DB::table('dietitian_token_history as th')
            ->join('tbl_dietitian as td','td.dietitian_id','=','th.dietitian_id')->select(DB::raw("CONCAT(td.D_FirstName,' ',td.D_LastName) as DietitianName"),'th.dietitian_comment','th.dietitian_id','th.created_at','th.updated_at')->where('th.ID',$ID)->get();

            $token_data2=DB::table('doctor_token_history as th')
            ->join('tbl_doctor as td','td.doctor_id','=','th.doctor_id')->select(DB::raw("CONCAT(td.Dr_FirstName,' ',td.Dr_LastName) as DoctorName"),'th.doctor_comment','th.doctor_id','th.created_at','th.updated_at')->where('th.ID',$ID)->get();
            //return array($token_data,$token_data1,$token_data2);

            $resp = array();

            if(!empty($token_data) && !empty($token_data1) && !empty($token_data2))
            {
           
               $i = 0;

               foreach($token_data as $token)
               {
                 if(!empty($token->token_status))
                 {
                     switch($token->token_status)
                     {
                         
                         case '1':
                            $token_status = 'Doctor Token';
                            break;
                         case '2':
                            $token_status = 'Dietitian Token';
                            break;
                         case '3':
                            $token_status = 'FE Token';
                            break;

                         case '4':
                            $token_status = 'Solved Token';
                            break;

                         case '5':
                            $token_status = 'Rejected Token';
                            break;
                        }
                    }
                    
                    $resp[$i]['token_id'] = $token->token_id;
                    $resp[$i]['ID'] = $ID;
                    $resp[$i]['patient_name'] = $token->patientName;
                    $resp[$i]['phone'] = $token->mobile_no;
                    $resp[$i]['patientComplent'] = $token->patientComplent;
                    $resp[$i]['Feildagent ID'] = $token->Feildagent_id;
                    $resp[$i]['Feildagent name'] = $token->FeilagentName;
                    $resp[$i]['token_status'] = $token->token_status;
                    $resp[$i]['isAttended'] = $token->isAttended;
                                                      
                    if(empty($prev_token_id) || (!empty($prev_token_id) && $prev_token_id!=$token_id))
                    {
                        $j = 0;
                        $k = 0;
                        $prev_token_id = $token_id;
                        if(!empty($prev_token_id) && $prev_token_id!=$token_id)
                        {
                            $i++;
                        }
                    }
                    
                    $resp[$i]['token_history'][$j]['Feildagent comment'] = $token->agent_comment;
                    $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                    $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                    $j++;
                    $k++;
                }
                foreach($token_data1 as $token)
                {
                  $resp[$i]['token_history'][$k]['Dietitian_id'] = $token->dietitian_id;
                  $resp[$i]['token_history'][$j]['Dietitian_comment'] = $token->dietitian_comment;
                  $resp[$i]['token_history'][$k]['DietitianName'] = $token->DietitianName;
                  $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                  $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                  $j++;   
                  $k++;
                }

                foreach($token_data2 as $token)
                {
                  $resp[$i]['token_history'][$k]['Doctor_id'] = $token->doctor_id;
                  $resp[$i]['token_history'][$j]['Doctor_comment'] = $token->doctor_comment;
                  $resp[$i]['token_history'][$k]['DoctorName'] = $token->DoctorName;
                  $resp[$i]['token_history'][$k]['created_at'] = $token->created_at;
                  $resp[$i]['token_history'][$k]['updated_at'] = $token->updated_at;

                  $j++;   
                  $k++;
                }
            }

        
        }
        
        return $resp;
    }



    public function addAdmin($insert_admin_data)
    {
        $insert = DB::table('tbl_admin')->insertGetID($insert_admin_data);
        return $insert;
    }



    public function registerDietitian($insert_dietitian_data){
        $insert = DB::table('tbl_dietitiansupport')->insertGetID($insert_dietitian);
        return $insert;
       
    }
}
    