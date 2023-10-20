<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        //Cek Role User
        $data = auth()->user()->code_user;
        $user = DB::table('v_users')->where('code_user', $data)->first();

        if ($user->role_name == 'Staff Analis' || $user->role_name == 'Kasi Analis') {
            return redirect('/themes/dashboard')->with('toast_success', 'Welcome back!');
        } else {
            return redirect('/dashboard')->with('toast_success', 'Welcome back!');
        }
        // return redirect()->intended(RouteServiceProvider::HOME)->with('toast_success', 'Welcome back!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
