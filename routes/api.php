<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', function (Request $request) {
    \Log::info('Login API Hit', [
        'raw_username' => $request->username,
        'raw_password' => $request->password,
        'trimmed_username' => trim($request->username),
        'trimmed_password' => trim($request->password),
    ]);
    
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // Cari user berdasarkan username
    $user = User::where('username', $request->username)->first();

    // Jika user tidak ditemukan atau password salah
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Buat token Sanctum
    try {
        $token = $user->createToken('auth_token')->plainTextToken;
    } catch (\Exception $e) {
        \Log::error('Token creation failed: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to generate token',
            'message' => $e->getMessage()
        ], 500);
    }

    \Log::info('Login attempt from App2', [
        'input_username' => $request->username,
        'user_found' => $user ? $user->username : null,
        'password_check' => $user ? Hash::check($request->password, $user->password) : null,
    ]);


    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->fullname,
            'username' => $user->username,
            'email' => $user->email,
        ],
    ]);
});

Route::get('/authorize', [AuthController::class, 'redirectToProvider'])->name('oauth.authorize');
Route::post('/token', [AuthController::class, 'getToken'])->name('oauth.token');

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json($request->user());
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});