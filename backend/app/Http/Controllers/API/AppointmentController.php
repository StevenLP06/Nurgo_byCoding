<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user', 'creator']);

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('appointment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('appointment_date', '<=', $request->end_date);
        }

        // Get appointments for authenticated user based on role
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

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments
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
            'appointment_date' => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:240',
            'type' => ['required', Rule::in(['consultation', 'follow_up', 'emergency', 'routine_checkup'])],
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate that appointment date is not in the past
        $appointmentDate = Carbon::parse($request->appointment_date);
        if ($appointmentDate->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot schedule appointments in the past'
            ], 422);
        }

        // Validate that the doctor doesn't have overlapping appointments
        $duration = $request->duration_minutes ?? 30;
        $endTime = $appointmentDate->copy()->addMinutes($duration);

        $hasConflict = Appointment::where('doctor_id', $request->doctor_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($appointmentDate, $endTime) {
                $query->whereBetween('appointment_date', [$appointmentDate, $endTime])
                    ->orWhere(function($q) use ($appointmentDate, $endTime) {
                        $q->where('appointment_date', '<=', $appointmentDate)
                          ->whereRaw('DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > ?', [$appointmentDate]);
                    });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'success' => false,
                'message' => 'The doctor already has an appointment at this time'
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'duration_minutes' => $duration,
            'type' => $request->type,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'scheduled',
            'created_by' => $request->user()->id,
        ]);

        $appointment->load(['patient.user', 'doctor.user']);

        // TODO: Send notification to patient and guardian

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully',
            'data' => $appointment
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
        $appointment = Appointment::with(['patient.user', 'patient.guardian.user', 'doctor.user', 'creator', 'prescriptions.medication'])->find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
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
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'nullable|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:240',
            'status' => ['nullable', Rule::in(['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled'])],
            'type' => ['nullable', Rule::in(['consultation', 'follow_up', 'emergency', 'routine_checkup'])],
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // If updating appointment date, validate conflicts
        if ($request->has('appointment_date')) {
            $appointmentDate = Carbon::parse($request->appointment_date);
            
            if ($appointmentDate->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reschedule to a past date'
                ], 422);
            }

            $duration = $request->duration_minutes ?? $appointment->duration_minutes;
            $endTime = $appointmentDate->copy()->addMinutes($duration);

            $hasConflict = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('id', '!=', $id)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($appointmentDate, $endTime) {
                    $query->whereBetween('appointment_date', [$appointmentDate, $endTime])
                        ->orWhere(function($q) use ($appointmentDate, $endTime) {
                            $q->where('appointment_date', '<=', $appointmentDate)
                              ->whereRaw('DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > ?', [$appointmentDate]);
                        });
                })
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'The doctor already has an appointment at this time'
                ], 422);
            }
        }

        $appointment->update($request->only([
            'appointment_date',
            'duration_minutes',
            'status',
            'type',
            'reason',
            'notes',
            'diagnosis'
        ]));

        $appointment->load(['patient.user', 'doctor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'data' => $appointment
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
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        // Instead of deleting, mark as cancelled
        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
    }

    /**
     * Get upcoming appointments
     */
    public function upcoming(Request $request)
    {
        $user = $request->user();
        $query = Appointment::with(['patient.user', 'doctor.user'])
            ->where('appointment_date', '>', now())
            ->where('status', '!=', 'cancelled');

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

        $appointments = $query->orderBy('appointment_date', 'asc')->limit(10)->get();

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }
}
