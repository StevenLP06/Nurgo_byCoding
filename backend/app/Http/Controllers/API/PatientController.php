<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Patient::with(['user', 'guardian.user', 'doctor.user']);

        // Filter by doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by guardian
        if ($request->has('guardian_id')) {
            $query->where('guardian_id', $request->guardian_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // If user is a doctor, show only their patients
        $user = $request->user();
        if ($user->isDoctor()) {
            $query->whereHas('doctor', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // If user is a guardian, show only their patients
        if ($user->isGuardian()) {
            $query->whereHas('guardian', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $patients = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $patients
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
            'guardian_id' => 'required|exists:guardians,id',
            'doctor_id' => 'required|exists:doctors,id',
            'blood_type' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $patientRole = Role::where('name', 'patient')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $patientRole->id,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
            'guardian_id' => $request->guardian_id,
            'doctor_id' => $request->doctor_id,
            'blood_type' => $request->blood_type,
            'allergies' => $request->allergies,
            'medical_history' => $request->medical_history,
            'current_medications' => $request->current_medications,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        $patient->load(['user', 'guardian.user', 'doctor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Patient created successfully',
            'data' => $patient
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
        $patient = Patient::with([
            'user',
            'guardian.user',
            'doctor.user',
            'appointments' => function($query) {
                $query->orderBy('appointment_date', 'desc')->limit(10);
            },
            'prescriptions.medication'
        ])->find($id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $patient
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
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $patient->user_id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string|max:50',
            'document_number' => 'nullable|string|max:50|unique:users,document_number,' . $patient->user_id,
            'guardian_id' => 'nullable|exists:guardians,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'blood_type' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
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
            $patient->user->update(array_filter($userFields));
        }

        // Update patient information
        $patientFields = $request->only([
            'guardian_id', 'doctor_id', 'blood_type', 'allergies', 
            'medical_history', 'current_medications', 'emergency_contact_name', 
            'emergency_contact_phone'
        ]);
        if (!empty($patientFields)) {
            $patient->update(array_filter($patientFields));
        }

        $patient->load(['user', 'guardian.user', 'doctor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Patient updated successfully',
            'data' => $patient
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
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        }

        $user = $patient->user;
        $patient->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Patient deleted successfully'
        ]);
    }

    /**
     * Get patient's medical history
     */
    public function medicalHistory($id)
    {
        $patient = Patient::with([
            'appointments' => function($query) {
                $query->where('status', 'completed')
                      ->orderBy('appointment_date', 'desc');
            },
            'appointments.doctor.user',
            'prescriptions' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'prescriptions.medication',
            'prescriptions.doctor.user'
        ])->find($id);

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => $patient->only(['id', 'blood_type', 'allergies', 'medical_history', 'current_medications']),
                'appointments' => $patient->appointments,
                'prescriptions' => $patient->prescriptions
            ]
        ]);
    }
}
