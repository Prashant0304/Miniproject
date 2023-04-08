<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DietitianController;

Route::post('signup_otp','LoginController@GetOTP');
Route::post('sms','LoginController@SMS');

Route::post('signup_otpverify','LoginController@OTPverificationsignup');

Route::post('register','LoginController@RegisterPatients');

Route::post('login_otp','LoginController@LoginOtp');

Route::post('login_otpverify','LoginController@OTPverificationlogin');

Route::post('listState','LoginController@ListState');

Route::post('listCity','LoginController@ListCity');

Route::post('health_status_list','FeedsController@HealthStatusList');

Route::post('bmi','FeedsController@bmi');
Route::post('allPatients','LoginController@getAllPatients');


 Route::middleware(['verifyApiToken'])->group(function(){

	Route::post('health_status_insert','LoginController@insertHealthStatus');

	
	Route::post('singlepetient','LoginController@singlePatient');

	Route::post('insert_feed','FeedsController@insertFeed');

	Route::post('feed_list','FeedsController@GetFeedList');

	Route::post('foodEaten','FeedsController@foodEaten');

	Route::post('pregnant_patient','LoginController@pregnantPatient');
	
    Route::post('getpeteints','LoginController@GetPatients');

    /* Rohan API Started */

    Route::post('getMealReference','MealController@GetMealReference');
	Route::post('postFoodConsume','MealController@PostFoodConsume');
	Route::post('postFeedConsume','MealController@PostFeedConsume');
	Route::post('getPatientMeal','MealController@GetPatientMeal');



	/* Rohan API End */

        Route::get('/viewprofile','telemedicineController@viewProfile');
    Route::post('offerMessage','LoginController@OfferMessage');

    

});
Route::post('/info_from_customer','telemedicineController@info_from_customer');
    Route::post('/updateCustomer','telemedicineController@UpdatePatientDetails');


Route::post('states','Controller@insertStates');

/* Rohan code started */

Route::post('listGraphParams','MealController@PatientGraphParametersList');
Route::post('insertGraphParams','MealController@PatientGraphParametersInsert');
Route::get('manage_csv','MealController@ManageCsv');
Route::get('manage_message_csv','MealController@ManageMessageCsv');
Route::post('listPatient','LoginController@ListPatient');
Route::post('patientDetail','LoginController@PatientDetail');
Route::post('deletePatient','LoginController@DeletePatient');
Route::post('createDoctor','LoginController@CreateDoctor');
Route::post('listDoctors','LoginController@ListDoctors');
Route::post('deleteDoctor','LoginController@DeleteDoctors');
Route::post('listMealType','LoginController@ListMealType');
Route::post('manageMealType','LoginController@ManageMealType');
Route::post('listFaqCategory','LoginController@ListFaqCategory');
Route::post('manageFaqCategory','LoginController@ManageFaqCategory');
Route::post('deleteFaqCategory','LoginController@DeleteFaqCategory');
Route::post('testMsg','MealController@TestMsg');
Route::post('getGraphReport','MealController@GetGraphReport');
Route::post('patientFactor','LoginController@PatientFactor');
Route::post('checkAppVersion','LoginController@CheckAppVersion');
Route::post('changeAppVersion','LoginController@ChangeAppVersion');
//Route::post('offerMessage','LoginController@OfferMessage');
Route::post('listBmiFactorReference','LoginController@ListBmiFactorReference');
Route::post('updateBmiFactorReference','LoginController@UpdateBmiFactorReference');
Route::post('listRequiredCalorieReference','LoginController@ListRequiredCalorieReference');
Route::post('updateRequiredCalorieReference','LoginController@UpdateRequiredCalorieReference');


//Ravi Code
Route::post('activityReferenceInsert','MealController@activityReferenceInsert');
Route::post('activityReferenceUpdate','MealController@activityReferenceUpdate');
Route::post('activityReferenceList','MealController@activityReferenceList');
Route::post('activityReferenceDelete','MealController@activityReferenceDelete');

Route::post('weightFactorInsert','MealController@weightFactorInsert');
Route::post('weightFactorUpdate','MealController@weightFactorUpdate');
Route::post('weightFactorList','MealController@weightFactorList');
Route::post('weightFactorDelete','MealController@weightFactorDelete');

Route::post('userActivityInsert','MealController@userActivityInsert');
Route::post('userActivityListByDate','MealController@userActivityListByDate');
Route::post('userActivityListToAdmin','MealController@userActivityListToAdmin');
Route::post('mealInsert','MealController@mealInsert');

Route::post('mealUpdate','MealController@mealUpdate');
Route::post('mealDelete','MealController@mealDelete');
Route::post('mealList','MealController@mealList');
Route::post('mealSingle','MealController@mealSingle');


Route::post('reCalculateSingle','MealController@reCalculateSingle');
Route::post('reCalculateAll','MealController@reCalculateAll');
Route::post('mealConsumedByPatient','MealController@mealConsumedByPatient');
Route::post('singlePatientDetails','LoginController@singlePatientDetails');

Route::post('graphLoadReferenceMasterInsert','MealController@graphLoadReferenceMasterInsert');
Route::post('graphLoadReferenceMasterUpdate','MealController@graphLoadReferenceMasterUpdate');
Route::post('graphLoadReferenceMasterDelete','MealController@graphLoadReferenceMasterDelete');
Route::post('graphLoadReferenceMasterList','MealController@graphLoadReferenceMasterList');
Route::post('graphLoadReferenceMasterSingle','MealController@graphLoadReferenceMasterSingle');
Route::post('particularDateMeal','MealController@particularDateMeal');


Route::post('mealVersionUpdate','MealController@mealVersionUpdate');

//Anusha
Route::post('create-groups',"GroupsController@CreateGroup");
Route::post('get-groups',"GroupsController@getGroups");
Route::post('single-groups',"GroupsController@singleGroups");
Route::post('update-groups',"GroupsController@updateGroups");
Route::post('delete-groups',"GroupsController@deleteGroups");

Route::post('group-member',"GroupsController@CreateMember");
Route::post('get-member',"GroupsController@getmember");
Route::post('delete-member',"GroupsController@deletemember");
Route::post('group-rank',"GroupsController@groupRank");
Route::post('group-invite',"GroupsController@groupInvite");

Route::post('high-disease','GroupsController@highDiseaseRisk');
Route::post('high-diabetic','GroupsController@highDiabeticRisk');
Route::post('calories',"GroupsController@calories");
Route::post('protein',"GroupsController@protein");
Route::post('carb',"GroupsController@carb");

Route::post('fat',"GroupsController@fat");
Route::post('graphs',"MealController@graphs");
Route::post('activityStatusInsert','MealController@activityStatusInsert');
Route::post('activityStatusList','MealController@activityStatusList');
Route::post('activityStatusupdate','MealController@activityStatusupdate');

Route::post('activityStatusDelete','MealController@activityStatusDelete');
Route::post('singlePatientTicket','TicketController@singlePatientTicket');
Route::post('assignTicket','TicketController@assignTicket');
Route::post('listAssignee','TicketController@listAssignee');
Route::post('agentDetail','TicketController@agentDetail');

Route::post('manageTicket','TicketController@ManageTicket');
Route::post('listTicket','TicketController@ListTicket');
Route::post('deleteTicket','TicketController@DeleteTicket');
Route::post('addComment','TicketController@AddComment');
Route::post('weeklyData','TicketController@weeklyData');

Route::post('adminCreateUser','LoginController@CreateAdminUser');
Route::post('adminLogin','LoginController@AdminLogin');
Route::post('deleteUser','LoginController@DeleteUser');
Route::post('listUser','LoginController@ListUser');
Route::post('ticketing','TicketController@ticketing');
 


/*ASHWINI*/

Route::post('/listalltoken','TokenController@ListAllTokens');
Route::post('/count','TokenController@countpendingattending');
Route::post('/isattendedupdate','TokenController@isAttendedUpdate');
Route::post('/statusupdate','TokenController@UpdateTokenStatus');
Route::post('/listallpatients','TokenController@ListAllPatient');

Route::post('/addcustomer','TokenController@AddCustomercare');
Route::post('/customercareupdate','TokenController@UpdateCustomercare');
Route::post('/customerdelete','TokenController@customercareDelete');
Route::post('/customercarelist','TokenController@customercarelist');
Route::post('/addcomment','TokenController@AddComment');
Route::post('/list','TokenController@ListToken');

Route::post('/adddoctor','TokenController@AddDoctor');
Route::post('/doctorupdate','TokenController@Updatedoctor');
Route::post('/doctordelete','TokenController@doctorDelete');
Route::post('/listdoctor','TokenController@docotrlist');
Route::post('/doctorisattendedupdate','TokenController@updatedoctorisAttended');
Route::post('/countdoctorpendingattending','TokenController@countdoctorpendingattending');
Route::post('/DRcomment','TokenController@AdddoctorComment');
Route::post('/listdoctortoken','TokenController@ListdoctorToken');

Route::post('/adddietitian','TokenController@AddDietitian');
Route::post('/dietitianupdate','TokenController@Updatedietitian');
Route::post('/dietitiandelete','TokenController@dietitianDelete');
Route::post('/listdietitian','TokenController@dietitianlist');
Route::post('/dietitiantokenattending','TokenController@updatedietitianisAttended');
Route::post('/countdietitianattended','TokenController@countdietitianpendingattending');
Route::post('/dietitiancomment','TokenController@AdddietitianComment');
Route::post('/listdietitiantoken','TokenController@ListdietitianToken');

Route::post('/addfeildgent','TokenController@AddFeildAgent');
Route::post('/feildagentupdate','TokenController@Updatefeildagent');
Route::post('/feildagentdelete','TokenController@feildagentDelete');
Route::post('/listfeildagent','TokenController@feildagentlist');
Route::post('/FEcomment','TokenController@AddfeildagentComment');
Route::post('/listFEtoken','TokenController@ListFEToken');
Route::post('/updateagentisAttended','TokenController@updateagentisAttended');
Route::post('/countagentpendingattending','TokenController@countagentpendingattending');

Route::post('/registerdietitian','DietitianSupportController@RegisterDietitian');
Route::post('/registerdoctor','DietitianSupportController@RegisterDoctor');
Route::post('login','DietitianSupportController@LoginOtp');
Route::post('otpverify','DietitianSupportController@OTPverificationlogin');
Route::post('assign','DietitianSupportController@assigntodoctor');
Route::post('doctorlist','DietitianSupportController@doctorlist');
Route::post('addingpatientdetails','DietitianSupportController@addingpatientdetails');

Route::post('assigndoctor','DietitianSupportController@updatestatus');
Route::post('listassignedpatient','DietitianSupportController@listassignedpatient');
Route::post('dietitianform','DietitianSupportController@dietitianform');
Route::post('dietitianassign','DietitianSupportController@dietitianassign');



/* Delivery Boy */

Route::post('/insertion','DboyController@user_details');

Route::post('/d_boy','DboyController@d_details');

Route::post('/total_count','DboyController@count');

Route::post('/status','DboyController@status');

Route::post('/purpose','DboyController@purpose');

Route::post('/address','DboyController@addresstag');

Route::post('/payment','DboyController@payment');

//Gireesh Telemedicine
Route::post('/info_for_customerSupport','telemedicineController@info_for_customerSupport');
Route::post('/customerSupport','telemedicineController@customerSupport');
//Route::post('/info_from_customer','telemedicineController@info_from_customer');
Route::post('/viewformDetails','telemedicineController@viewformDetails');
//profilepicture update api
Route::post('/updateprofile','telemedicineController@updateprofilepic');
Route::post('/listcustomer','telemedicineController@infofromcustomerlist');
//end 
//Suhas //graphs api
Route::post('/mealcalories','graphController@calories');
Route::post('/protiens','graphController@protiens');
Route::post('/Fats','graphController@Fats');
Route::post('/Carbs','graphController@Carbs');


//Srivatsa

Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){
    //Route::post('/register',[AuthController::class,'register']);
    Route::post('/adminlogin','AuthController@adminlogin1');
    Route::post('/doctorprofile',[AuthController::class,'doctorprofile']);
    Route::post('/agentprofile',[AuthController::class,'agentprofile']);

    //Route::post('/register2',[DoctorController::class,'register2']);
    Route::post('/doctorlogin',[DoctorController::class,'doctorlogin']);
    Route::post('/agentlogin',[AgentController::class,'agentlogin']);
    Route::post('/customerlogin',[CustomerController::class,'customerlogin']);
    Route::post('/dietitianlogin',[DietitianController::class,'dietitianlogin']);

    
    
    

});


Route::post('/contacts','telemedicineController@contacts');
//Srivatsa


Route::post('/addadmin','TokenController@AddAdmin');


//praveen 

Route::get('/exotelCallDetails','DietitianSupportController@exotelGetCallDetails');
Route::post('/getMealDetails','DietitianSupportController@getMealDetails');
Route::post('/getPatientId','LoginController@getPatientId');
Route::post('/getTabletName','LoginController@getTabletName');
Route::post('/getRoles','LoginController@getRoles');
Route::post('/offerRejectedFromFollowup','DietitianSupportController@offerRejectedFromFollowup');
Route::post('/assignPatientToFollowupDoctor','DietitianSupportController@assignPatientToFollowupDoctor');
Route::post('/pushToFollowupDocList','DietitianSupportController@pushToFollowupDocList');
Route::post('/followupCount','DietitianSupportController@followupCount');
Route::post('/updateFollowupCount','DietitianSupportController@updateFollowupCount');
Route::post('/createFollowupCount','DietitianSupportController@createFollowupCount');
Route::post('/prevFollowupRemarks','DietitianSupportController@prevFollowupRemarks');
Route::post('/followupDelivery','DietitianSupportController@followupDelivery');
Route::post('/followupDeliveryexistence','DietitianSupportController@followupDeliveryexistence');
Route::post('/getPPBSReport','DietitianSupportController@getPPBSReport');
Route::post('/prevFollowupComplaints','DietitianSupportController@prevFollowupComplaints');
Route::post('/prevFollowupFoodRecc','DietitianSupportController@prevFollowupFoodRecc');
Route::post('/prevFollowupExcRecc','DietitianSupportController@prevFollowupExcRecc');
Route::post('/treamentForFollowupDoc','DietitianSupportController@treamentForFollowupDoc');

/*Priya Karekar*/

Route::post('/doctor_forms','DietitianSupportController@doctor_forms');
Route::post('/return_info_from_customer','DietitianSupportController@return_info_from_customer');
Route::post('/incorrect_cust_detail','DietitianSupportController@incorrect_cust_detail');
Route::post('/firstFollowup','DietitianSupportController@firstFollowup');
Route::post('/infoFromDoctorForm','DietitianSupportController@infoFromDoctorForm');
Route::post('/listAssignedExotelCustomers','DietitianSupportController@listAssignedExotelCustomers');
Route::post('/sixthDayFollowup','DietitianSupportController@sixthDayFollowup');

Route::post('/assignPatientToCA','DietitianSupportController@assignPatientToCA');
Route::post('/offerRejectedFromCA','DietitianSupportController@offerRejectedFromCA');//Praveen
Route::post('/offerRejectedFromDoctor','DietitianSupportController@offerRejectedFromDoctor');//Praveen
Route::post('/callLaterCA','DietitianSupportController@callLaterCA');
Route::post('/callLaterDoctor','DietitianSupportController@callLaterDoctor');
Route::post('/getDetailsFromDoctorForm','DietitianSupportController@getDetailsFromDoctorForm');
Route::post('/getSubmittedPatientDetails','DietitianSupportController@getSubmittedPatientDetails');
//Route::post('/removedata','DietitianSupportController@removedata');
Route::post('/physicalConsultation','DietitianSupportController@physicalConsultation');
Route::post('/empSubmittedRecordCA','DietitianSupportController@empSubmittedRecordCA');
Route::post('/empSubmittedRecordDoctor','DietitianSupportController@empSubmittedRecordDoctor');
Route::post('/monthlyEarnings','DietitianSupportController@monthlyEarnings');
Route::post('/trialKitDeliveryList','DietitianSupportController@trialKitDeliveryList');
Route::post('/firstFollowupDietitianList','DietitianSupportController@firstFollowupDietitianList');
Route::post('/firstFollowupList','DietitianSupportController@firstFollowupList');//Praveen
//Route::post('/ustatus','DietitianSupportController@ustatus');
Route::post('/assignPatientToDietitian1','DietitianSupportController@assignPatientToDietitian1');
Route::post('/callLaterDietitian1','DietitianSupportController@callLaterDietitian1');
Route::post('/patientDetailsForDietitian1','DietitianSupportController@patientDetailsForDietitian1');
Route::post('/patientRemarksForDietitian1','DietitianSupportController@patientRemarksForDietitian1');
Route::post('/info_from_dietitian1','DietitianSupportController@info_from_dietitian1');
Route::post('/removeExpiredElements','DietitianSupportController@removeExpiredElements');
Route::post('/assignPatientToDietitian2','DietitianSupportController@assignPatientToDietitian2');

Route::post('/followupDietitianList','DietitianSupportController@followupDietitianList');
Route::post('/callLaterDietitian2','DietitianSupportController@callLaterDietitian2');
Route::post('/info_from_dietitian2','DietitianSupportController@info_from_dietitian2');
Route::post('/patientListForFollowupDoctor','DietitianSupportController@patientListForFollowupDoctor');
Route::post('/info_from_followup_doctor','DietitianSupportController@info_from_followup_doctor');
Route::post('/assignToFollowupDoc','DietitianSupportController@assignToFollowupDoc');
Route::post('/callLaterFollowupDoctor','DietitianSupportController@callLaterFollowupDoctor');

Route::post('/patientPPBSReport','DietitianSupportController@patientPPBSReport');
Route::post('/getPPBSReport','DietitianSupportController@getPPBSReport');

//Purushottam


