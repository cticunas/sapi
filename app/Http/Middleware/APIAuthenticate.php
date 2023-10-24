<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\DB;

class APIAuthenticate
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
    
        //dd($request->header('Authorization') ." = ".md5( $request->user->remember_token ) );
        //dd($request->header('Authorization'));
        //$u=\App\Models\User::whereRaw("md5(remember_token) = '".$request->header('Authorization')."' ")->first();
        //if(!$u) {
            //return redirect('home');
        //    throw new \Exception("Usuario No Autorizado");
        //}
        return $next($request);
    }
}
