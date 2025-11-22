<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HomeVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class HomeVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = HomeVisit::with(['patient.user', 'doctor.user']);

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
            $query->where('visit_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('visit_date', '<=', $request->end_date);
        }

        // Get home visits for authenticated user based on role
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

        $homeVisits = $query->orderBy('visit_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $homeVisits
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Only doctors can schedule home visits
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'visit_date' => 'required|date|after:now',
            'estimated_duration_minutes' => 'nullable|integer|min:30|max:480',
            'address' => 'required|string',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate that visit date is not in the past
        $visitDate = Carbon::parse($request->visit_date);
        if ($visitDate->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot schedule home visits in the past'
            ], 422);
        }

        $homeVisit = HomeVisit::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'visit_date' => $request->visit_date,
            'estimated_duration_minutes' => $request->estimated_duration_minutes ?? 60,
            'address' => $request->address,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        $homeVisit->load(['patient.user', 'patient.guardian.user', 'doctor.user']);

        // TODO: Send notification to patient and guardian

        return response()->json([
            'success' => true,
            'message' => 'Home visit scheduled successfully',
            'data' => $homeVisit
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
        $homeVisit = HomeVisit::with(['patient.user', 'patient.guardian.user', 'doctor.user'])->find($id);

        if (!$homeVisit) {
            return response()->json([
                'success' => false,
                'message' => 'Home visit not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $homeVisit
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
        $homeVisit = HomeVisit::find($id);

        if (!$homeVisit) {
            return response()->json([
                'success' => false,
                'message' => 'Home visit not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'visit_date' => 'nullable|date|after:now',
            'estimated_duration_minutes' => 'nullable|integer|min:30|max:480',
            'address' => 'nullable|string',
            'status' => ['nullable', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'findings' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // If updating visit date, validate it's not in the past
        if ($request->has('visit_date')) {
            $visitDate = Carbon::parse($request->visit_date);
            if ($visitDate->isPast() && $homeVisit->status === 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reschedule to a past date'
                ], 422);
            }
        }

        $homeVisit->update($request->only([
            'visit_date',
            'estimated_duration_minutes',
            'address',
            'status',
            'reason',
            'notes',
            'findings'
        ]));

        $homeVisit->load(['patient.user', 'doctor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Home visit updated successfully',
            'data' => $homeVisit
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
        $homeVisit = HomeVisit::find($id);

        if (!$homeVisit) {
            return response()->json([
                'success' => false,
                'message' => 'Home visit not found'
            ], 404);
        }

        // Instead of deleting, mark as cancelled
        $homeVisit->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Home visit cancelled successfully'
        ]);
    }

    /**
     * Get upcoming home visits
     */
    public function upcoming(Request $request)
    {
        $user = $request->user();
        $query = HomeVisit::with(['patient.user', 'doctor.user'])
            ->where('visit_date', '>', now())
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

        $homeVisits = $query->orderBy('visit_date', 'asc')->limit(10)->get();

        return response()->json([
            'success' => true,
            'data' => $homeVisits
        ]);
    }
}
