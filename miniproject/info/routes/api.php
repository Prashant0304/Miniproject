<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\telemedicineController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/info_from_customers',[telemedicineController::class,'info_from_customers']);

Route::post('/doctor_form',[telemedicineController::class,'doctor_form']);

Route::post('/patientDetails',[telemedicineController::class,'patientDetails']);
