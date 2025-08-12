<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (Auth::guest()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        foreach ($permissions as $permission) {
            if (Auth::user()->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        abort(403, 'لا تملك الصلاحيات الكافية للوصول إلى هذه الصفحة');
    }
}
