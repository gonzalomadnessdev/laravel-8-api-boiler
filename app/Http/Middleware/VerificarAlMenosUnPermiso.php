<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\App\Exceptions\PermissionDeniedException;

class VerificarAlMenosUnPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next , ...$permission)
    {
        if(auth()->user()->hasOnePermission($permission)){
            return $next($request);
        }
        $permission = join(',', $permission);
        throw new PermissionDeniedException($permission);
    }
}
