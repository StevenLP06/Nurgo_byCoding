<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $medications = [
            [
                'name' => 'Paracetamol',
                'description' => 'Pain reliever and fever reducer',
                'dosage_info' => '500mg-1000mg',
                'side_effects' => 'Rare: nausea, rash',
                'contraindications' => 'Severe liver disease',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Ibuprofen',
                'description' => 'Nonsteroidal anti-inflammatory drug (NSAID)',
                'dosage_info' => '200mg-400mg',
                'side_effects' => 'Stomach upset, heartburn, dizziness',
                'contraindications' => 'History of stomach ulcers, kidney disease',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Amoxicillin',
                'description' => 'Antibiotic used to treat bacterial infections',
                'dosage_info' => '250mg-500mg',
                'side_effects' => 'Diarrhea, nausea, rash',
                'contraindications' => 'Allergy to penicillin',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Omeprazole',
                'description' => 'Proton pump inhibitor for acid reflux',
                'dosage_info' => '20mg-40mg',
                'side_effects' => 'Headache, abdominal pain, nausea',
                'contraindications' => 'Hypersensitivity to the drug',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Metformin',
                'description' => 'Medication for type 2 diabetes',
                'dosage_info' => '500mg-1000mg',
                'side_effects' => 'Diarrhea, nausea, abdominal discomfort',
                'contraindications' => 'Kidney disease, liver disease',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Lisinopril',
                'description' => 'ACE inhibitor for high blood pressure',
                'dosage_info' => '10mg-40mg',
                'side_effects' => 'Cough, dizziness, headache',
                'contraindications' => 'Pregnancy, history of angioedema',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cetirizine',
                'description' => 'Antihistamine for allergies',
                'dosage_info' => '5mg-10mg',
                'side_effects' => 'Drowsiness, dry mouth',
                'contraindications' => 'Severe kidney disease',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Atorvastatin',
                'description' => 'Statin for lowering cholesterol',
                'dosage_info' => '10mg-80mg',
                'side_effects' => 'Muscle pain, digestive problems',
                'contraindications' => 'Liver disease, pregnancy',
                'requires_prescription' => true,
                'is_active' => true,
            ],
        ];

        foreach ($medications as $medication) {
            Medication::create($medication);
        }
    }
}
