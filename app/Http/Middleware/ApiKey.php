<?php

namespace App\Http\Middleware;

use Closure;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('accept', 'application/json');
        $apikey = $request->header('x-api-key');
        if($apikey == '') {
            return response()->json([
                'message' => 'You are not authorized',
                'status' => 401
            ], 401);
        } else {
            $authkey = $apikey == 'XXXX-YYYY-ZZZZ';
            if(!$authkey) {
                return response()->json([
                    'message' => 'Authentication Failed',
                    'status' => 401
                ], 401);
            } else {
                return $next($request);
            }
        } 
    }
}
