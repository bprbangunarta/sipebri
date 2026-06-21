<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $codextUrl  = config('services.codex.endpoint');
            $codexToken = config('services.codex.token');
            $urlPath    = "/api/web-auth";

            $requestHeader = [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $codexToken,
            ];

            $requestBody = [
                'username' => $request->username,
                'password' => $request->password,
            ];

            $response = Http::withHeaders($requestHeader)
                ->post($codextUrl . $urlPath, $requestBody);

            if (!$response->successful()) {
                Log::error('API Web Auth:' . $response);
                throw new \Exception($response->json('message') ?? 'Terjasi kesalahan pada response API');
            }

            $data    = $response->json('data');
            $payload = [
                'id'            => $data['user']['id'],
                'name'          => $data['user']['name'],
                'username'      => $data['user']['username'],
                'email'         => $data['user']['email'],
                'code_user'     => $data['user']['alias'],
                'kode_kolektor' => $data['user']['collector_code'],
                'password'      => bcrypt($request->password),
            ];

            $user = User::withTrashed()
                ->where('id', $payload['id'])
                ->first();

            if ($user) {
                $user->update($payload);
            } else {
                $user = User::create($payload);
            }

            if ($data['user']['is_active'] != true) {
                $user->delete();
                throw new \Exception('Autentikasi tidak dapat dilanjutkan, pengguna sudah tidak aktif');
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        } catch (\Throwable $e) {
            Log::error('Login Process:' . $e->getMessage());

            return redirect('/login')
                ->with('error', $e->getMessage() ?? 'Proses tidak dapat dilanjutkan, terjadi kesalahan pada server');
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|current_password',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
        ]);
        try {
            $codextUrl  = config('services.codex.endpoint');
            $codexToken = config('services.codex.token');
            $urlPath    = "/api/web-auth/password";

            $requestHeader = [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $codexToken,
            ];

            $requestBody = [
                'username'           => Auth::user()->email ?? Auth::user()->username,
                'current_password'   => $request->current_password,
                'password'           => $request->password,
                'confirmed_password' => $request->password_confirmation,
            ];

            $response = Http::withHeaders($requestHeader)
                ->post($codextUrl . $urlPath, $requestBody);

            if (!$response->successful()) {
                Log::error('API Change Password:' . $response);
                throw new \Exception($response->json('message') ?? 'Terjasi kesalahan pada response API');
            }

            Auth::logout();
            return redirect('/login')->with('success', 'Kata sandi berhasil diubah');
        } catch (\Throwable $e) {
            Log::error('Change Password:' . $e->getMessage());

            return back()
                ->with('error', $e->getMessage() ?? 'Proses tidak dapat dilanjutkan, terjadi kesalahan pada server');
        }
    }

    public function logoutSession(Request $request)
    {
        $request->validate([
            'browser_session' => ['required', 'current_password'],
        ], [
            'browser_session.required'         => 'Kata sandi saat ini wajib diisi',
            'browser_session.current_password' => 'Kata sandi salah'
        ]);

        try {
            $user = Auth::user();
            $currentSessionId = Session::getId();

            DB::transaction(function () use ($user, $currentSessionId) {
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $currentSessionId)
                    ->delete();
            });

            return redirect()
                ->back()
                ->with('success', 'Mohon tunggu dan jangan refresh halaman, berhasil menyimpan data');
        } catch (\Throwable $e) {
            Log::error('Logout Session:' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Proses tidak dapat dilanjutkan, terjadi kesalahan saat menyimpan data');
        }
    }
}
