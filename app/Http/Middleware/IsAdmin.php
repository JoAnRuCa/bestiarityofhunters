<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
public function handle(Request $request, Closure $next)
{
    // Si el usuario no está logueado o su rol no es 1, lo echamos
    if (auth()->check() && auth()->user()->role == 'admin') {
        return $next($request);
    }

    return redirect('/')->with('error', 'No tienes permisos de investigación.');
}
}
