<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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

    public function logout()
    {
        session()->flush();
        
        return redirect()->route('map')
                       ->with('success', 'Anda telah logout.');
    }
}