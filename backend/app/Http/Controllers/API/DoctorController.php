<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Doctor::with('user');

        // Filter by availability
        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        // Filter by specialty
        if ($request->has('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        // Search by name
        if ($request->has('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $doctors = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $doctors
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
            'specialty' => 'required|string|max:255',
            'license_number' => 'required|string|max:50|unique:doctors',
            'bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $doctorRole = Role::where('name', 'doctor')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $doctorRole->id,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'specialty' => $request->specialty,
            'license_number' => $request->license_number,
            'bio' => $request->bio,
            'is_available' => true,
        ]);

        $doctor->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Doctor created successfully',
            'data' => $doctor
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
        $doctor = Doctor::with(['user', 'patients.user'])->find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $doctor
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
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $doctor->user_id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string|max:50',
            'document_number' => 'nullable|string|max:50|unique:users,document_number,' . $doctor->user_id,
            'specialty' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:50|unique:doctors,license_number,' . $id,
            'bio' => 'nullable|string',
            'is_available' => 'nullable|boolean',
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
            $doctor->user->update(array_filter($userFields));
        }

        // Update doctor information
        $doctorFields = $request->only(['specialty', 'license_number', 'bio', 'is_available']);
        if (!empty($doctorFields)) {
            $doctor->update(array_filter($doctorFields));
        }

        $doctor->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Doctor updated successfully',
            'data' => $doctor
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
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        // Check if doctor has active appointments
        $hasActiveAppointments = $doctor->appointments()
            ->where('status', '!=', 'cancelled')
            ->where('appointment_date', '>', now())
            ->exists();

        if ($hasActiveAppointments) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete doctor with active appointments'
            ], 422);
        }

        $user = $doctor->user;
        $doctor->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully'
        ]);
    }

    /**
     * Get doctor's appointments
     */
    public function appointments($id, Request $request)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        $query = $doctor->appointments()->with(['patient.user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->where('appointment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('appointment_date', '<=', $request->end_date);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get available doctors
     */
    public function available()
    {
        $doctors = Doctor::with('user')
            ->where('is_available', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }
}
