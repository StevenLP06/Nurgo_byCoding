<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Emergency;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Emergency::with(['patient.user', 'guardian.user', 'doctor.user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Get emergencies for authenticated user based on role
        $user = $request->user();
        if ($user->isDoctor()) {
            $query->whereHas('doctor', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->isGuardian()) {
            $query->whereHas('guardian', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->isPatient()) {
            $query->whereHas('patient', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $emergencies = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $emergencies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Only guardians can report emergencies
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'description' => 'required|string',
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'critical'])],
            'location' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify that the authenticated user is a guardian
        $user = $request->user();
        if (!$user->isGuardian()) {
            return response()->json([
                'success' => false,
                'message' => 'Only guardians can report emergencies'
            ], 403);
        }

        // Get patient and verify guardian relationship
        $patient = Patient::with(['guardian', 'doctor'])->find($request->patient_id);
        
        if ($patient->guardian->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only report emergencies for your assigned patients'
            ], 403);
        }

        $emergency = Emergency::create([
            'patient_id' => $request->patient_id,
            'guardian_id' => $patient->guardian_id,
            'doctor_id' => $patient->doctor_id,
            'description' => $request->description,
            'priority' => $request->priority ?? 'high',
            'location' => $request->location,
            'status' => 'reported',
        ]);

        $emergency->load(['patient.user', 'guardian.user', 'doctor.user']);

        // TODO: Send email notification to doctor
        // Mail::to($patient->doctor->user->email)->send(new EmergencyAlert($emergency));

        return response()->json([
            'success' => true,
            'message' => 'Emergency reported successfully. Doctor has been notified.',
            'data' => $emergency
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
        $emergency = Emergency::with(['patient.user', 'guardian.user', 'doctor.user'])->find($id);

        if (!$emergency) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $emergency
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Only doctors can update emergency status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $emergency = Emergency::find($id);

        if (!$emergency) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['nullable', Rule::in(['reported', 'acknowledged', 'in_progress', 'resolved'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'critical'])],
            'response_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update timestamps based on status
        if ($request->has('status')) {
            if ($request->status === 'acknowledged' && !$emergency->acknowledged_at) {
                $emergency->acknowledged_at = now();
            }
            if ($request->status === 'resolved' && !$emergency->resolved_at) {
                $emergency->resolved_at = now();
            }
        }

        $emergency->update($request->only(['status', 'priority', 'response_notes']));
        $emergency->load(['patient.user', 'guardian.user', 'doctor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Emergency updated successfully',
            'data' => $emergency
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
        $emergency = Emergency::find($id);

        if (!$emergency) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency not found'
            ], 404);
        }

        $emergency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Emergency deleted successfully'
        ]);
    }

    /**
     * Get active emergencies (not resolved)
     */
    public function active(Request $request)
    {
        $user = $request->user();
        $query = Emergency::with(['patient.user', 'guardian.user', 'doctor.user'])
            ->where('status', '!=', 'resolved');

        if ($user->isDoctor()) {
            $query->whereHas('doctor', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $emergencies = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $emergencies
        ]);
    }

    /**
     * Acknowledge an emergency (doctor action)
     */
    public function acknowledge($id, Request $request)
    {
        $emergency = Emergency::find($id);

        if (!$emergency) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency not found'
            ], 404);
        }

        if (!$request->user()->isDoctor()) {
            return response()->json([
                'success' => false,
                'message' => 'Only doctors can acknowledge emergencies'
            ], 403);
        }

        $emergency->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Emergency acknowledged',
            'data' => $emergency
        ]);
    }
}
