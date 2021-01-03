<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Subscriber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->subscribed('cashier')) {
            // This user is not a paying customer...
            return redirect('subscribe');
        }

        return $next($request);

    }
}
