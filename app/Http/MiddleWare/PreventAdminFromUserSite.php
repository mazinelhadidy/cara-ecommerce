<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PreventAdminFromUserSite
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}