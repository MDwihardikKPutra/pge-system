<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request - PURE Spatie Permission
     * 
     * Enterprise-grade RBAC using Spatie Laravel Permission
     * Supports: Role checking, Permission checking, Multiple roles
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        // Admin can access all routes (superuser)
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Pure Spatie Permission Check for other roles
        if (!$user->hasRole($role)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}


