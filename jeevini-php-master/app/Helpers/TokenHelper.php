<?php

namespace App\Helpers;
ob_start();

use Emarref\Jwt\Jwt;
use Emarref\Jwt\Token;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Encryption\Factory;
use Emarref\Jwt\Verification\Context;
use Illuminate\Support\Facades\Config;
use Emarref\Jwt\Exception\VerificationException;
use Illuminate\Support\Facades\Log;

class TokenHelper {
    
    public static function generateToken($payload) {
        //Payload Parameter should be an Associative Array with Key & Value.
        
        $serializedToken = '';
        
        if(sizeof($payload) > 0) { 
            $token = new Token();
            $token->addClaim(new Claim\IssuedAt(date('Y-m-d H:i:s')));
            
            foreach($payload as $key => $value) {
                $token->addClaim(new Claim\PrivateClaim($key,$value));
                $token->addClaim(new Claim\Expiration(new \DateTime('168 hours')));
            }
            
            $jwt = new Jwt();
            $secret = Config::get('constants.secret');
            $algorithm  = new Hs256($secret);
            $encryption = Factory::create($algorithm);
            $serializedToken = $jwt->serialize($token, $encryption); 
        } 
        
        return $serializedToken;
    }
    
    public static function verifyToken($serializedToken) {
        try {
            $validToken = false;
            
            $jwt = new Jwt();
            $token = $jwt->deserialize($serializedToken);
            
            $secret = Config::get('constants.secret');
            $algorithm  = new Hs256($secret);
            $encryption = Factory::create($algorithm);
            $context = new Context($encryption);

            try {
                $validToken = $jwt->verify($token, $context);
            } catch (VerificationException $e) {
                Log::info('In-valid Token');
                Log::info($e->getMessage());
            }
        } catch (\Exception $e) {
            Log::info('Error Decoding Token');
            Log::info($e->getMessage());
        }

        return $validToken;
    }
    
    public static function getTokenPayload($serializedToken) {
        try {
            $result = array();
            $jwt = new Jwt();
            $token = $jwt->deserialize($serializedToken);
            
            $header = $token->getHeader()->jsonSerialize();
            $playload = $token->getPayload()->jsonSerialize();
            
            $result = json_decode($playload,true);
            
        } catch (\Exception $e) {
            Log::info('Error Decoding Token');
            Log::info($e->getMessage());
        }

        return $result;
    }



    public function RegisterDietitian(Request $request)
    {
        $raw_data = $request->json()->all();
        $FirstName = $raw_data['FirstName'];
        $LastName = $raw_data['LastName'];
        $mobile_no = $raw_data['mobile_no'];
        $validator = Validator::make($request->all(),[
            'mobile_no' => 'required|unique:tbl_dietitiansupport',
        ]);
        if ($validator->fails()) 
        {
            $response = [
                'message' => "User Exist",
                'key' => 0,
            ];
            return response()->json($validator->errors(), 400);
        }
        $Location  = $raw_data['Location'];
        $role=$raw_data['role'];
        
       $insert_dietitian = array(
                "id" => null,
                "FirstName" => $raw_data['FirstName'],
                "LastName" => $raw_data['LastName'],
                "mobile_no"=>$raw_data['mobile_no'],
                "Location" => $raw_data['Location'],
                "role"=>$raw_data['role'],
                );
        $dietitianID = $this->_token->registerDietitian($insert_dietitian);
        
        return array('Dietitian_id'=>$dietitianID,'status'=>"Registration successfully");
    }

}
