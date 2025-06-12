<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShareAdminStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Bagikan status login admin ke semua view
        View::share('is_admin_logged_in', session()->has('admin_logged_in'));
        View::share('admin_username', session('admin_username'));
        
        return $next($request);
    }
}