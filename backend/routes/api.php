<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\GuardianController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\MedicationController;
use App\Http\Controllers\API\PrescriptionController;
use App\Http\Controllers\API\HomeVisitController;
use App\Http\Controllers\API\EmergencyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Doctor routes
    Route::apiResource('doctors', DoctorController::class);
    Route::get('/doctors/{id}/appointments', [DoctorController::class, 'appointments']);
    Route::get('/doctors-available', [DoctorController::class, 'available']);

    // Patient routes
    Route::apiResource('patients', PatientController::class);
    Route::get('/patients/{id}/medical-history', [PatientController::class, 'medicalHistory']);

    // Guardian routes
    Route::apiResource('guardians', GuardianController::class);
    Route::get('/guardians/{id}/patients', [GuardianController::class, 'patients']);

    // Appointment routes
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('/appointments-upcoming', [AppointmentController::class, 'upcoming']);

    // Medication routes
    Route::apiResource('medications', MedicationController::class);
    Route::get('/medications-active', [MedicationController::class, 'active']);

    // Prescription routes
    Route::apiResource('prescriptions', PrescriptionController::class);
    Route::get('/prescriptions-active', [PrescriptionController::class, 'active']);

    // Home Visit routes
    Route::apiResource('home-visits', HomeVisitController::class);
    Route::get('/home-visits-upcoming', [HomeVisitController::class, 'upcoming']);

    // Emergency routes
    Route::apiResource('emergencies', EmergencyController::class);
    Route::get('/emergencies-active', [EmergencyController::class, 'active']);
    Route::post('/emergencies/{id}/acknowledge', [EmergencyController::class, 'acknowledge']);
});


