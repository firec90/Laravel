<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Hash password
        $hashed = Hash::make($request->password);

        // Masukkan data ke database (Raw SQL)
        DB::insert(
            'INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
            [$request->name, $request->email, $hashed]
        );

        return response()->json(['message' => 'User registered successfully']);
    }

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek apakah user ada
        $user = DB::selectOne('SELECT * FROM users WHERE email = ? LIMIT 1', [$request->email]);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        // Buat token manual (tanpa Sanctum)
        $token = Str::random(60);

        // Simpan token ke tabel personal_access_tokens
        DB::insert(
            'INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            ['App\\Models\\User', $user->id, 'auth_token', hash('sha256', $token), '["*"]']
        );

        return response()->json(['token' => $token]);
    }

    // logout
    public function logout(Request $request)
    {
        // Ambil token dari header Authorization: Bearer <token>
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['message' => 'Token tidak ditemukan'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $hashed = hash('sha256', $token);

        // Hapus token dari tabel personal_access_tokens
        DB::delete('DELETE FROM personal_access_tokens WHERE token = ?', [$hashed]);

        return response()->json(['message' => 'Logout berhasil']);
    }
}
//SAMPAI SINI