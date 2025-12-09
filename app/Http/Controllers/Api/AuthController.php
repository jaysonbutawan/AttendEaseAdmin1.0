<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    // -----------------------------
    // Utility Methods
    // -----------------------------

    /**
     * Generate a new student ID like STU-202500001
     */
    private function generateUserId(string $prefix, string $table, string $column): string
    {
        $year = date('Y');

        $lastUser = DB::table($table)
            ->where($column, 'like', "$prefix-$year%")
            ->orderBy($column, 'desc')
            ->first();

        $newNumber = $lastUser
            ? str_pad((int)substr($lastUser->{$column}, -5) + 1, 5, '0', STR_PAD_LEFT)
            : '00001';

        return "$prefix-$year$newNumber";
    }

    // -----------------------------
    // Registration & Login
    // -----------------------------

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:student,teacher,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'student',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => ['user' => $user, 'token' => $token]
        ], 201);
    }

    public function firebaseRegister(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|string|in:student,teacher',
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',
        ]);

        $uid = $request->uid;
        $email = $request->email;
        $role = $request->role;
        $firstname = $request->firstname ?? 'FirstName';
        $lastname = $request->lastname ?? 'LastName';

        try {
            if ($role === 'student') {
                $table = 'students';
                $idColumn = 'student_id';
                $prefix = 'STU';
            } else { // teacher
                $table = 'teachers';
                $idColumn = 'teacher_id';
                $prefix = 'TEA';
            }

            $exists = DB::table($table)->where('firebase_uid', $uid)->exists();

            if (!$exists) {
                $data = [
                    'firebase_uid' => $uid,
                    'email' => $email,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'status' => '',
                ];

                // Generate ID for students or teachers
                $data[$idColumn] = $this->generateUserId($prefix, $table, $idColumn);

                DB::table($table)->insert(array_merge($data, ['created_at' => now()]));
                \Log::info("Firebase {$role} created: UID={$uid}, Email={$email}, ID={$data[$idColumn]}");
            } else {
                \Log::info("Firebase {$role} already exists: UID={$uid}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Firebase user registered successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error("Firebase registration failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Firebase registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => ['user' => $user, 'token' => $token]
        ], 200);
    }

    public function googleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'avatar' => 'nullable|string',
            'role' => 'nullable|string|in:student,teacher,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)
            ->orWhere('google_id', $request->google_id)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $request->google_id,
                'name' => $request->name,
                'avatar' => $request->avatar ?? $user->avatar,
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'google_id' => $request->google_id,
                'avatar' => $request->avatar,
                'role' => $request->role ?? 'student',
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Google authentication successful',
            'data' => ['user' => $user, 'token' => $token]
        ], 200);
    }

    // -----------------------------
    // User Profile & Logout
    // -----------------------------

    public function profile(Request $request)
    {
        return response()->json(['success' => true, 'data' => ['user' => $request->user()]], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logout successful'], 200);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out from all devices'], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'avatar' => 'sometimes|string',
            'password' => 'sometimes|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['name', 'email', 'avatar']);
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['user' => $user->fresh()]
        ], 200);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Account deleted successfully'], 200);
    }
}
