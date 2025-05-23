<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\User;

class AuthController extends Controller
{
    public function redirectToProvider(Request $request)
    {
        // Validasi parameter
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'redirect_uri' => 'required|url',
            'response_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        // Cari client
        $client = Client::where('id', $request->client_id)->first();

        if (!$client || $client->redirect !== $request->redirect_uri) {
            return response()->json(['error' => 'Invalid client or redirect URI'], 400);
        }

        // Simpan informasi client dan redirect URI ke session
        Session::put([
            'oauth_client_id' => $request->client_id,
            'oauth_redirect_uri' => $request->redirect_uri,
        ]);

        // Arahkan ke halaman login Aplikasi 1 (dari Jetstream)
        return redirect()->route('login');
    }

    public function getToken(Request $request)
    {
        // Validasi parameter
        $validator = Validator::make($request->all(), [
            'grant_type' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'code' => 'required|string',
            'redirect_uri' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        // Cari client
        $client = Client::where('id', $request->client_id)
            ->where('secret', $request->client_secret)
            ->first();

        if (!$client || $client->redirect !== $request->redirect_uri) {
            return response()->json(['error' => 'Invalid client or redirect URI'], 400);
        }

        // Dapatkan user dari sesi atau database
        $user = Auth::user(); // Gunakan user yang sedang login

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Buat token
        $token = $user->createToken('app2-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }
}