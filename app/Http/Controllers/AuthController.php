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

    // Manual authentication tanpa session
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response()->json([
        'message' => 'Invalid login credentials'
      ], 401);
    }

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

  public function updateProfile(Request $request)
  {
    $user = $request->user();

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'position' => 'nullable|string|max:255',
      'company_address' => 'nullable|string|max:500',
      'work_field' => 'nullable|string|max:255',
      // Email dan username tidak bisa diubah
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    try {
      $user->update([
        'name' => $request->name,
        'position' => $request->position,
        'company_address' => $request->company_address,
        'work_field' => $request->work_field,
      ]);

      return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user->fresh() // Get updated user data
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Failed to update profile',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function changePassword(Request $request)
  {
    $user = $request->user();

    $validator = Validator::make($request->all(), [
      'current_password' => 'required|string',
      'new_password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    // Check current password
    if (!Hash::check($request->current_password, $user->password)) {
      return response()->json([
        'errors' => [
          'current_password' => ['Current password is incorrect']
        ]
      ], 422);
    }

    try {
      $user->update([
        'password' => Hash::make($request->new_password)
      ]);

      // Optionally: logout semua device dengan menghapus semua token
      // $user->tokens()->delete();

      return response()->json([
        'message' => 'Password changed successfully'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Failed to change password',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
