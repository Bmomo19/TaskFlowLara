<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('API Token')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $http = new Client();

        try {
//            $response = $http->post(config('app.url') . '/oauth/token', [
            $response = $http->post('http://127.0.0.1:8000/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSWORD_CLIENT_ID'),
                    'client_secret' => env('PASSWORD_CLIENT_SECRET'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (BadResponseException $e) {
            return response()->json(['error' => 'Unauthorized', 'error_description' => $e->getMessage()], 401);
        }
//        try {
//            $credentials = $request->only('email', 'password');
//
//            if (!Auth::attempt($credentials)) {
//                return response()->json(['error' => 'Unauthorized'], 401);
//            }
//
//            $user = Auth::user();
//            $token = $user->createToken('API Token')->accessToken;
//
//            return response()->json(['user' => $user, 'token' => $token]);
//        } catch (\Throwable $e) {
//            return response()->json(['error' => $e->getMessage()], 500);
//        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me(Request $request): JsonResponse
    {
        Log::info($request);
        return response()->json($request->user());
    }
}

