<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->intended('dashboard');
        }
        return view('auth.login');
    }
    
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $validateAuth = Auth::attempt($credentials);

        if ($validateAuth) {
            $request->session()->regenerate();

            $userRoleInfo = getUserRoleInfo($request->email);
            $roleAccess = getRoleAccessData($userRoleInfo->role_id);
            $menu = getMenu($userRoleInfo->role_id);

            $request->session()->put('menu_session', $menu);
            $request->session()->put('roleaccess_session', $roleAccess);
            $request->session()->put('role_info_session', $userRoleInfo);

            return redirect()->intended('dashboard');
        };

        return back()->withErrors(['messages' => 'Email atau Password Anda salah']);
    }

    public function logout()
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('login')->with('message', 'Anda berhasil keluar');
    }
}
