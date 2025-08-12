<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if the user has the required role
        // This can be a single role or multiple roles separated by '|'
        $roles = explode('|', $role);
        
        foreach ($roles as $roleName) {
            if ($user->hasRole($roleName)) {
                return $next($request);
            }
        }
        
        // If we reach here, the user doesn't have any of the required roles
        abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة.');
    }
}
