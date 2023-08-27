<?php

namespace App\Http\Middleware;

namespace App\Http\Middleware;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Exception;
class AssignGuard extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($guard != null){

            auth()->shouldUse($guard); //shoud you user guard / table
//            $token = $request->header('auth-token');
//            $request->headers->set('auth-token', (string) $token, true);
//            $request->headers->set('Authorization', 'Bearer '.$token, true);
//            try {
////              $this->auth->authenticate($request);  //check authenticted user
//             JWTAuth::parseToken()->authenticate();
//             JWTAuth::getToken();
//            } catch (TokenExpiredException $e) {
//                return response()->json(['error'=>'unauthorite']);
//            } catch (JWTException $e) {
//                return response()->json(['error'=>'token_invalid ']);
//            }
       }
        return $next($request);
//return "un authorized";

    }
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}


