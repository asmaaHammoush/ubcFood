<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrEmployeeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('employee-api')->user() ||
            Auth::guard('customer-api')->user()) {
            return $next($request);
        }
        return response()->json(['error'=>'unauthorite']);
    }
}
