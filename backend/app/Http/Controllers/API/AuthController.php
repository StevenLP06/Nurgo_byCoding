<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Doctor;
use App\Models\Guardian;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_name' => ['required', 'string', Rule::in(['admin', 'doctor', 'patient', 'guardian'])],
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'document_number' => 'required|string|max:50|unique:users',
            
            // Doctor specific fields
            'specialty' => 'required_if:role_name,doctor|string|max:255',
            'license_number' => 'required_if:role_name,doctor|string|max:50|unique:doctors,license_number',
            
            // Guardian specific fields
            'relationship' => ['required_if:role_name,guardian', Rule::in(['parent', 'spouse', 'sibling', 'child', 'other'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate guardian age (must be 18+)
        if ($request->role_name === 'guardian') {
            $birthDate = new \DateTime($request->birth_date);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
            
            if ($age < 18) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guardian must be 18 years or older'
                ], 422);
            }
        }

        $role = Role::where('name', $request->role_name)->first();

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role'
            ], 422);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'document_number' => $request->document_number,
        ]);

        // Create role-specific profile
        switch ($request->role_name) {
            case 'doctor':
                Doctor::create([
                    'user_id' => $user->id,
                    'specialty' => $request->specialty,
                    'license_number' => $request->license_number,
                    'is_available' => true,
                ]);
                break;

            case 'guardian':
                Guardian::create([
                    'user_id' => $user->id,
                    'relationship' => $request->relationship,
                    'is_primary_contact' => true,
                ]);
                break;

            case 'patient':
                Patient::create([
                    'user_id' => $user->id,
                    // Estos campos se pueden asignar después
                ]);
                break;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Cargar relación del usuario
        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Cargar relaciones
        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['role', 'doctor', 'guardian', 'patient']);

        return response()->json($user);
    }
}
