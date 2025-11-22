<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Medication::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by requires prescription
        if ($request->has('requires_prescription')) {
            $query->where('requires_prescription', $request->requires_prescription);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $medications = $query->orderBy('name', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $medications
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
            'description' => 'nullable|string',
            'dosage_info' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'requires_prescription' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $medication = Medication::create([
            'name' => $request->name,
            'description' => $request->description,
            'dosage_info' => $request->dosage_info,
            'side_effects' => $request->side_effects,
            'contraindications' => $request->contraindications,
            'requires_prescription' => $request->requires_prescription ?? true,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Medication created successfully',
            'data' => $medication
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
        $medication = Medication::find($id);

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medication
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
        $medication = Medication::find($id);

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'dosage_info' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'requires_prescription' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $medication->update($request->only([
            'name',
            'description',
            'dosage_info',
            'side_effects',
            'contraindications',
            'requires_prescription',
            'is_active'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Medication updated successfully',
            'data' => $medication
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
        $medication = Medication::find($id);

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        }

        // Check if medication is used in prescriptions
        if ($medication->prescriptions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete medication that is used in prescriptions. Consider deactivating it instead.'
            ], 422);
        }

        $medication->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medication deleted successfully'
        ]);
    }

    /**
     * Get all active medications
     */
    public function active()
    {
        $medications = Medication::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medications
        ]);
    }
}
