<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Guardian::with('user');

        // Search by name
        if ($request->has('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $guardians = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $guardians
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'required|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string|max:50',
            'document_number' => 'nullable|string|max:50|unique:users',
            'relationship' => ['required', Rule::in(['parent', 'spouse', 'sibling', 'child', 'other'])],
            'relationship_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate guardian age (must be 18+)
        $birthDate = new \DateTime($request->birth_date);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        
        if ($age < 18) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian must be 18 years or older'
            ], 422);
        }

        $guardianRole = Role::where('name', 'guardian')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $guardianRole->id,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
        ]);

        $guardian = Guardian::create([
            'user_id' => $user->id,
            'relationship' => $request->relationship,
            'relationship_notes' => $request->relationship_notes,
            'is_primary_contact' => true,
        ]);

        $guardian->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Guardian created successfully',
            'data' => $guardian
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $guardian = Guardian::with(['user', 'patients.user', 'patients.doctor.user'])->find($id);

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $guardian
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $guardian = Guardian::find($id);

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $guardian->user_id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string|max:50',
            'document_number' => 'nullable|string|max:50|unique:users,document_number,' . $guardian->user_id,
            'relationship' => ['nullable', Rule::in(['parent', 'spouse', 'sibling', 'child', 'other'])],
            'relationship_notes' => 'nullable|string',
            'is_primary_contact' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user information
        $userFields = $request->only(['name', 'email', 'phone', 'birth_date', 'gender', 'address', 'document_type', 'document_number']);
        if (!empty($userFields)) {
            $guardian->user->update(array_filter($userFields));
        }

        // Update guardian information
        $guardianFields = $request->only(['relationship', 'relationship_notes', 'is_primary_contact']);
        if (!empty($guardianFields)) {
            $guardian->update(array_filter($guardianFields));
        }

        $guardian->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Guardian updated successfully',
            'data' => $guardian
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guardian = Guardian::find($id);

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian not found'
            ], 404);
        }

        // Check if guardian has patients
        if ($guardian->patients()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete guardian with assigned patients'
            ], 422);
        }

        $user = $guardian->user;
        $guardian->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guardian deleted successfully'
        ]);
    }

    /**
     * Get guardian's patients
     */
    public function patients($id)
    {
        $guardian = Guardian::with(['patients.user', 'patients.doctor.user'])->find($id);

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $guardian->patients
        ]);
    }
}
