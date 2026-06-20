<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', 1)
            ->whereIn('role', ['admin', 'teacher'])
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah.', 401);
        }

        $user->api_token = Str::random(80);
        $user->save();

        return $this->success([
            'token' => $user->api_token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 'Login berhasil.');
    }

    public function loginStudent(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginType, $request->username)
            ->where('is_active', 1)
            ->where('role', 'student')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('NIS/Email atau password salah.', 401);
        }

        $user->api_token = Str::random(80);
        $user->save();

        return $this->success([
            'token' => $user->api_token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
            ],
        ], 'Login siswa berhasil.');
    }

    public function loginParent(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', 1)
            ->where('role', 'parent')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah, atau akun bukan Orang Tua.', 401);
        }

        $user->api_token = Str::random(80);
        $user->save();

        return $this->success([
            'token' => $user->api_token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 'Login orang tua berhasil.');
    }

public function logout(Request $request)
{
    /** @var \App\Models\User|null $user */

    $user = auth('api')->user();

    if ($user) {

        $user->api_token = null;

        $user->save();

    }

    return $this->success([], 'Logout berhasil.');
}
}
