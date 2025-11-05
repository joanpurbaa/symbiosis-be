<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'username' => 'required|string|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'position' => 'nullable|string|max:255',
      'company_address' => 'nullable|string|max:500',
      'work_field' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'username' => $request->username,
      'password' => Hash::make($request->password),
      'position' => $request->position,
      'company_address' => $request->company_address,
      'work_field' => $request->work_field,
      'role' => 'user',
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'User registered successfully',
      'user' => $user,
      'access_token' => $token,
      'token_type' => 'Bearer',
    ], 201);
  }

  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    if (!Auth::attempt($request->only('email', 'password'))) {
      return response()->json([
        'message' => 'Invalid login credentials'
      ], 401);
    }

    $user = User::where('email', $request->email)->firstOrFail();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Login successful',
      'user' => $user,
      'access_token' => $token,
      'token_type' => 'Bearer',
    ]);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'Logged out successfully'
    ]);
  }

  public function user(Request $request)
  {
    return response()->json($request->user());
  }
}
