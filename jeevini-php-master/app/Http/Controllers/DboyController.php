<?php

namespace App\Http\Controllers;

use App\Dboy;
use DB;
use Illuminate\Http\Request;

class DboyController extends Controller
{
    public function __construct()
  {
    $this->_Dboy = new Dboy();
  }
  public function user_details(Request $request)
 {
  $data = array();
    	// $data['Id'] = (!empty($request->Id))?$request->:Id'';
	$data['Name'] = (!empty($request->Name))?$request->Name:'';
	$data['Order_no'] = (!empty($request->Order_no))?$request->Order_no:'';
	$data['Address'] = (!empty($request->Address))?$request->Address:'';
	$data['Address_tag'] = (!empty($request->Address_tag))?$request->Address__tag:'';
	$data['Mobile_no'] = (!empty($request->Mobile_no))?$request->Mobile_no:'';
	$data['Payment_mode'] = (!empty($request->Payment_mode))?$request->Payment_mode:'COD';
	$data['Status'] = (!empty($request->Status))?$request->Status:'Pending';
	$data['Purpose'] = (!empty($request->Purpose))?$request->Purpose:'Kit delivery';
    
    if(empty($request->Id))
    	{
    		$resp_data = $this->_Dboy->addusers($data);
    	}
    	else
    	{
    		$resp_data = $this->_Dboy->addusers($data,$request->Id);
    	}
    	
    	$resp = array('code'=>200,'data'=>$resp_data,'message'=>'Data inserted successfully');

    	echo json_encode($resp);
    }

    public function d_details(Request $request)
    {
	$data = array();
	$data['Id'] = (!empty($request->Id))?$request->Id:'';
	$data['Name'] = (!empty($request->Name))?$request->Name:'';
	$data['Address'] = (!empty($request->Address))?$request->Address:'';
	$data['Address_tag'] = (!empty($request->Address_tag))?$request->Address__tag:'';
	$data['Mobile_no'] = (!empty($request->Mobile_no))?$request->Mobile_no:'';
	$data['Payment_mode'] = (!empty($request->Payment_mode))?$request->Payment_mode:'COD';
	$data['Status'] = (!empty($request->Status))?$request->Status:'Pending';
	$data['Purpose'] = (!empty($request->Purpose))?$request->Purpose:'Kit delivery';
	
	
	if(empty($request->Order_no))
	{
		$resp_data = $this->_Dboy->add_details($data);
	}
	else
	{
		$resp_data = $this->_Dboy->add_details($data,$request->Order_no);
	}
	
	$resp = array('code'=>200,'data'=>$resp_data,'message'=>'Data inserted successfully');

	echo json_encode($resp);
}
        
           public function status(Request $request)
         {
            $Order_no = $request->Order_no;

	 DB::table('delivery_user')
	 ->where('order_no', $Order_no)
	 ->update(['status' => 'Delievred']);
	 return "Updated";
          }
 	
    public function count(Request $request)
	{
	   $data = DB::select(DB::raw("select count(*) as pending from delivery_user where Status='Pending'"));
	   
	   $data1 = DB::select(DB::raw("select count(*) as delivered from delivery_user where Status='Delievred'"));
	   return response()->json(array($data,$data1));	
	   //return response()->json($data);	
	}
        
	public function purpose(Request $request)
	{ 
	   
	   if(empty($Purpose))
 	  {
	     
	     $raw_data = DB::table('delivery as d')
	     ->select("d.*","u.Id","u.Name","u.Order_no","u.Address","u.Mobile_no","u.Payment_mode","u.Status","u.Purpose")
	     ->join('delivery_user as u', 'u.Purpose','=','d.Purpose' )
	     ->get(); 

	
	  }
          
	  else
	   {
	       $raw_data = DB::table('delivery as d')
	      ->select("u.Id","u.Name","d.Order_no","u.Address","u.Mobile_no","u.Payment_mode","u.Status","u.Purpose")
	//       ->join('delivery_user as u', 'u.Id','=','d.Id')
	      ->where('d.Purpose',$Purpose)
	      ->get();
	   }
	 
	  if(!empty($raw_data))
    	   {
    	 	$resp = array('code'=>'200','data'=>$raw_data);
    	   }
    	else
    	{
    		$resp = array('code'=>'400','data'=>null);
    	}
              echo json_encode($resp);
	}


	public function addresstag(Request $request)
	{
	   /*$address_tag=$request->address_tag;
	   $data = DB::select(DB::raw("select * from delivery_user where Address_tag='$address_tag'"));
	   return response($data);*/
	   $data=DB::table('delivery_user')->select('Address_tag')->get();
	   return $data;
	}

	public function payment(Request $request)
	{
	   $data = DB::select(DB::raw("select * from  delivery_user where Payment_mode='COD'"));
	   $data1 = DB::select(DB::raw("select * from  delivery_user where Payment_mode='Paid'"));
	    return response()->json(array($data,$data1));	
	   //return response()->json($data);	
	}
}