<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Prescription::with(['patient.user', 'doctor.user', 'medication', 'appointment']);

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Get prescriptions for authenticated user based on role
        $user = $request->user();
        if ($user->isDoctor()) {
            $query->whereHas('doctor', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->isPatient()) {
            $query->whereHas('patient', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->isGuardian()) {
            $query->whereHas('patient.guardian', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $prescriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $prescriptions
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
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'medication_id' => 'required|exists:medications,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays($request->duration_days);

        $prescription = Prescription::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'medication_id' => $request->medication_id,
            'appointment_id' => $request->appointment_id,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration_days' => $request->duration_days,
            'instructions' => $request->instructions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
        ]);

        $prescription->load(['patient.user', 'doctor.user', 'medication']);

        return response()->json([
            'success' => true,
            'message' => 'Prescription created successfully',
            'data' => $prescription
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
        $prescription = Prescription::with(['patient.user', 'doctor.user', 'medication', 'appointment'])->find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $prescription
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
        $prescription = Prescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration_days' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Recalculate end date if duration changes
        if ($request->has('duration_days')) {
            $endDate = Carbon::parse($prescription->start_date)->addDays($request->duration_days);
            $prescription->end_date = $endDate;
        }

        $prescription->update($request->only([
            'dosage',
            'frequency',
            'duration_days',
            'instructions',
            'is_active'
        ]));

        $prescription->load(['patient.user', 'doctor.user', 'medication']);

        return response()->json([
            'success' => true,
            'message' => 'Prescription updated successfully',
            'data' => $prescription
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
        $prescription = Prescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found'
            ], 404);
        }

        $prescription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prescription deleted successfully'
        ]);
    }

    /**
     * Get active prescriptions
     */
    public function active(Request $request)
    {
        $user = $request->user();
        $query = Prescription::with(['patient.user', 'doctor.user', 'medication'])
            ->where('is_active', true)
            ->where('end_date', '>=', now());

        if ($user->isPatient()) {
            $query->whereHas('patient', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->isGuardian()) {
            $query->whereHas('patient.guardian', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $prescriptions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $prescriptions
        ]);
    }
}
