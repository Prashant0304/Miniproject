<?php

namespace App\Http\Middleware;

use Closure;

use App\Helpers\TokenHelper;
use Illuminate\Support\Facades\Log;

class AuthenticateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        $token = $request->header('token');
        
        $result = array(
            'success' => false,
            'error' => array(
                'error_code' => 'T001',
                'message' => 'Token Verification Failed'
            )
        );
        
        if(!empty($token) && $token != "") {
            $validToken = TokenHelper::verifyToken($token);
            
            if($validToken) {
                return $next($request);
            } else {
                return response()->json($result,401);
            }
        } else {
            return response()->json($result,401);
        }
    }
}
