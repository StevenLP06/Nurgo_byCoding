<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'System administrator with full access'
            ],
            [
                'name' => 'doctor',
                'description' => 'Medical doctor who can manage patients, appointments, and prescriptions'
            ],
            [
                'name' => 'patient',
                'description' => 'Patient who can view their medical information and schedule appointments'
            ],
            [
                'name' => 'guardian',
                'description' => 'Guardian responsible for a patient, can manage appointments and report emergencies'
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
