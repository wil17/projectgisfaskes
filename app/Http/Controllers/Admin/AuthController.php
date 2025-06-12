<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    public function __construct()
    {
        // Berbagi status admin ke semua view
        View::composer('*', function ($view) {
            $view->with('is_admin_logged_in', session()->has('admin_logged_in'))
                 ->with('admin_username', session('admin_username'))
                 ->with('admin_email', session('admin_email'));
        });
    }

    public function showLogin()
    {
        // Redirect jika sudah login
        if (session()->has('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session([
                'admin_logged_in' => true,
                'admin_id' => $admin->id,
                'admin_username' => $admin->username,
                'admin_email' => $admin->email
            ]);

            return redirect()->intended(route('admin.dashboard'))
                           ->with('success', 'Login berhasil! Selamat datang ' . $admin->username);
        }

        return back()->withErrors([
            'login' => 'Username atau password salah!'
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        // Ambil parameter redirect jika ada
        $redirectTo = $request->input('redirect', 'map');
        
        session()->forget(['admin_logged_in', 'admin_id', 'admin_username', 'admin_email']);
        
        return redirect()->route($redirectTo)
                       ->with('success', 'Anda telah logout.');
    }
}