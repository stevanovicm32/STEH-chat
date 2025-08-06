<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\KorisnikOnlineStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registracija novog korisnika
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Korisnik uspešno registrovan',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Prijava korisnika
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Neispravni podaci za prijavu'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Postavljanje online statusa
        $user->update([
            'je_online' => true,
            'poslednja_aktivnost' => now()
        ]);

        // Broadcast online statusa
        event(new KorisnikOnlineStatus($user, true));

        return response()->json([
            'success' => true,
            'message' => 'Uspešna prijava',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    /**
     * Odjava korisnika
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Postavljanje offline statusa
        $user->update([
            'je_online' => false,
            'poslednja_aktivnost' => now()
        ]);

        // Broadcast offline statusa
        event(new KorisnikOnlineStatus($user, false));

        // Brisanje tokena
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Uspešna odjava'
        ]);
    }

    /**
     * Dohvatanje informacija o trenutnom korisniku
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }
}
