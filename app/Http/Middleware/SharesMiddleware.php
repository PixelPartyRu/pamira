<?php

namespace App\Http\Middleware;
use App\Http\Requests\Request;
use Closure;

class SharesMiddleware
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
       // d(2);
       // d($request);
       // d($next);
        return $next($request);
    }
}
