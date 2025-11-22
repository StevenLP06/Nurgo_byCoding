<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Doctor;
use App\Models\Guardian;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        $guardianRole = Role::where('name', 'guardian')->first();
        $patientRole = Role::where('name', 'patient')->first();

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@nurgo.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'phone' => '+1234567890',
            'birth_date' => '1985-01-15',
            'gender' => 'male',
            'address' => '123 Admin Street, Admin City',
            'document_type' => 'DNI',
            'document_number' => 'ADMIN001',
        ]);

        // Create doctor users with profiles
        $doctor1User = User::create([
            'name' => 'Dr. John Smith',
            'email' => 'john.smith@nurgo.com',
            'password' => Hash::make('password'),
            'role_id' => $doctorRole->id,
            'phone' => '+1234567891',
            'birth_date' => '1980-03-20',
            'gender' => 'male',
            'address' => '456 Medical Plaza, Health City',
            'document_type' => 'DNI',
            'document_number' => 'DOC001',
        ]);

        $doctor1 = Doctor::create([
            'user_id' => $doctor1User->id,
            'specialty' => 'General Medicine',
            'license_number' => 'MED123456',
            'bio' => 'Experienced general practitioner with 15 years of practice.',
            'is_available' => true,
        ]);

        $doctor2User = User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah.johnson@nurgo.com',
            'password' => Hash::make('password'),
            'role_id' => $doctorRole->id,
            'phone' => '+1234567892',
            'birth_date' => '1982-07-12',
            'gender' => 'female',
            'address' => '789 Hospital Road, Med Town',
            'document_type' => 'DNI',
            'document_number' => 'DOC002',
        ]);

        $doctor2 = Doctor::create([
            'user_id' => $doctor2User->id,
            'specialty' => 'Pediatrics',
            'license_number' => 'MED789012',
            'bio' => 'Pediatrician specialized in child healthcare.',
            'is_available' => true,
        ]);

        // Create guardian user
        $guardian1User = User::create([
            'name' => 'Maria Garcia',
            'email' => 'maria.garcia@email.com',
            'password' => Hash::make('password'),
            'role_id' => $guardianRole->id,
            'phone' => '+1234567893',
            'birth_date' => '1975-05-10',
            'gender' => 'female',
            'address' => '321 Family Avenue, Care City',
            'document_type' => 'DNI',
            'document_number' => 'GUAR001',
        ]);

        $guardian1 = Guardian::create([
            'user_id' => $guardian1User->id,
            'relationship' => 'parent',
            'relationship_notes' => 'Mother of the patient',
            'is_primary_contact' => true,
        ]);

        // Create patient user
        $patient1User = User::create([
            'name' => 'Carlos Garcia',
            'email' => 'carlos.garcia@email.com',
            'password' => Hash::make('password'),
            'role_id' => $patientRole->id,
            'phone' => '+1234567894',
            'birth_date' => '2010-08-15',
            'gender' => 'male',
            'address' => '321 Family Avenue, Care City',
            'document_type' => 'DNI',
            'document_number' => 'PAT001',
        ]);

        Patient::create([
            'user_id' => $patient1User->id,
            'guardian_id' => $guardian1->id,
            'doctor_id' => $doctor2->id,
            'blood_type' => 'O+',
            'allergies' => 'Penicillin',
            'medical_history' => 'No significant medical history',
            'current_medications' => 'None',
            'emergency_contact_name' => 'Maria Garcia',
            'emergency_contact_phone' => '+1234567893',
        ]);

        // Create more guardian and patient pairs
        $guardian2User = User::create([
            'name' => 'Robert Williams',
            'email' => 'robert.williams@email.com',
            'password' => Hash::make('password'),
            'role_id' => $guardianRole->id,
            'phone' => '+1234567895',
            'birth_date' => '1970-11-22',
            'gender' => 'male',
            'address' => '555 Guardian Lane, Safe Town',
            'document_type' => 'DNI',
            'document_number' => 'GUAR002',
        ]);

        $guardian2 = Guardian::create([
            'user_id' => $guardian2User->id,
            'relationship' => 'parent',
            'relationship_notes' => 'Father of the patient',
            'is_primary_contact' => true,
        ]);

        $patient2User = User::create([
            'name' => 'Emily Williams',
            'email' => 'emily.williams@email.com',
            'password' => Hash::make('password'),
            'role_id' => $patientRole->id,
            'phone' => '+1234567896',
            'birth_date' => '2015-03-08',
            'gender' => 'female',
            'address' => '555 Guardian Lane, Safe Town',
            'document_type' => 'DNI',
            'document_number' => 'PAT002',
        ]);

        Patient::create([
            'user_id' => $patient2User->id,
            'guardian_id' => $guardian2->id,
            'doctor_id' => $doctor1->id,
            'blood_type' => 'A+',
            'allergies' => 'None',
            'medical_history' => 'Routine checkups, all normal',
            'current_medications' => 'None',
            'emergency_contact_name' => 'Robert Williams',
            'emergency_contact_phone' => '+1234567895',
        ]);
    }
}
