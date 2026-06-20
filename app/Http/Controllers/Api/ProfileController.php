<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ApiController
{
    public function show(Request $request)
{
    $user = $request->user('api');

    $user->load([
        'teacher',
        'student',
        'students',
    ]);

    return $this->success([

        'user' => $user,

    ], 'Profile berhasil diambil.');
}

    public function update(Request $request)
    {
        $user = $request->user('api');

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $this->success(['user' => $user->only(['id', 'name', 'username', 'email', 'role', 'is_active'])], 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user('api');
        $user->delete();

        return $this->success([], 'Akun berhasil dihapus.');
    }
}
