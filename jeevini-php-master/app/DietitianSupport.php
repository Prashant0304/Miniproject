<?php

namespace App;
ob_start();
use DB;
use Illuminate\Database\Eloquent\Model;

class DietitianSupport extends Model
{
    public function addDietitian($insert_dietitian){
        $insert = DB::table('tbl_dietitiansupport')->insertGetID($insert_dietitian);
        return $insert;

    }

    public function getDietitianDetail($dietitianID){
        $details = DB::table('tbl_dietitiansupport')->select('d_id',DB::raw("CONCAT(FirstName,' ',LastName) as Name"),'mobile_no','Location','role','registration_number')->where('d_id', $dietitianID)->first();
        return $details;
    }

    public function check_existance($mobile_no){
        $exist = DB::table('tbl_dietitiansupport')->select('*')->where('mobile_no',$mobile_no)->first();
        if (!empty($exist)) {
            $data = DB::table('tbl_dietitiansupport')->select('*')->where('mobile_no',$mobile_no)->first();
            return 1;
        }
        else {

            return 0;
        }
    }

    public function updateLoginOTP($code, $mobile_no ){
        $insert = DB::table('tbl_dietitiansupport')->where('mobile_no',$mobile_no)->update(array('code'=>$code));

    }

    public function getdetails($mobile_no){
        $details = DB::table('tbl_dietitiansupport')->select('d_id','FirstName','role','registration_number')->where('mobile_no', $mobile_no)->first();
        return $details;
    }

    public function verifyCodeLogin($code, $mobile_no ){

        $there = DB::table('tbl_dietitiansupport')
                ->select('d_id',DB::raw("CONCAT(FirstName,' ',LastName) as Name"),'role','mobile_no','registration_number')
                ->where('mobile_no',$mobile_no)
                ->where('code',$code)
                ->get();
        if (!empty($there)) {

            return $there;
        }
        else {
            $data = array();
            return $data;
        }


    }
    public function doctorlist()
    {
        //$role=Doctor;
        $values = DB::table('tbl_dietitiansupport')
        ->select('registration_number',DB::raw("CONCAT(FirstName,' ',LastName) as Name"))->where('role','Doctor')->get();
        return $values;
    }

    public function check_existancedoctor($d_id,$registration_number){
        $exist = DB::table('tbl_dietitiansupport')->select('*')->where('d_id',$d_id)
        ->where('registration_number',$registration_number)->first();
        if (!empty($exist)) {
           // $data = DB::table('tbl_dietitiansupport')->select('*')->where('mobile_no',$mobile_no)->first();
            return 1;
        }
        else {

            return 0;
        }
    }

    public function getItemData($id){
        $data = DB::table('info_from_customers')->select('*')->where('id',$id)->first();
    }

    public function insertUserDetailsExotel($insertData,$exist){
        $tableData1=0;
        $tableData=DB::table('customer_details_exotel')->insert($insertData);
        if(!$exist)
        {
            $tableData1=DB::table('customer_details_exotel1')->insert($insertData);
        }
        if($tableData1 || $tableData)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function doctor_form($data){

        $insert = DB::table('doctor_form')->insertGetID($data);
        return $insert;

    }

    public function exercise_insert($data)
    {
        $insert=DB::table('exercise')->insertGetID($data);
        return $insert;
    }

    public function submitted_details($data)
    {
        $insert=DB::table('submitted_patient_details')->insertGetID($data);
        return $insert;
    }

    public function incorrect($data)
    {
        $insert=DB::table('incorrect_customer_details')->insertGetID($data);
        return $insert;
     }

     public function followupdate($data)
     {
        $insert=DB::table('firstFollowup')->insertGetID($data);
        return $insert;
     }

     public function insertSixthDayFollowup($data)
     {
        $insert=DB::table('sixthDayFollowup')->insertGetID($data);
        return $insert;
     }


}
