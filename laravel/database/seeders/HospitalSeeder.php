<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'City General Hospital',
                'code' => 'CGH-001',
                'email' => 'info@citygeneral.com',
                'phone' => '+1-555-0101',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'New York',
                'contact_person' => 'Dr. James Wilson',
                'designation' => 'Chief Medical Officer',
                'total_beds' => 250,
                'description' => 'A leading general hospital providing comprehensive healthcare services.',
                'status' => 'active',
                'rating' => 4.5,
            ],
            [
                'name' => 'Community Health Center',
                'code' => 'CHC-002',
                'email' => 'info@communityhealth.com',
                'phone' => '+1-555-0102',
                'address' => '456 Oak Avenue',
                'city' => 'Brooklyn',
                'state' => 'New York',
                'contact_person' => 'Dr. Maria Santos',
                'designation' => 'Director',
                'total_beds' => 100,
                'description' => 'Community-focused health center with emphasis on preventive care and vaccinations.',
                'status' => 'active',
                'rating' => 4.2,
            ],
            [
                'name' => "Children's Medical Center",
                'code' => 'CMC-003',
                'email' => 'info@childrensmedical.com',
                'phone' => '+1-555-0103',
                'address' => '789 Pediatric Drive',
                'city' => 'Manhattan',
                'state' => 'New York',
                'contact_person' => 'Dr. Sarah Chen',
                'designation' => 'Pediatric Chief',
                'total_beds' => 150,
                'description' => 'Specialized children medical center with world-class pediatric vaccination programs.',
                'status' => 'active',
                'rating' => 4.8,
            ],
            [
                'name' => 'Westside Hospital',
                'code' => 'WSH-004',
                'email' => 'info@westsidehospital.com',
                'phone' => '+1-555-0104',
                'address' => '321 West End Avenue',
                'city' => 'New York',
                'state' => 'New York',
                'contact_person' => 'Dr. Robert Kim',
                'designation' => 'Administrator',
                'total_beds' => 200,
                'description' => 'Full-service hospital on the west side with modern vaccination facilities.',
                'status' => 'active',
                'rating' => 4.3,
            ],
            [
                'name' => 'Downtown Medical Center',
                'code' => 'DMC-005',
                'email' => 'info@downtownmedical.com',
                'phone' => '+1-555-0105',
                'address' => '555 Broadway',
                'city' => 'New York',
                'state' => 'New York',
                'contact_person' => 'Dr. Amanda Foster',
                'designation' => 'Medical Director',
                'total_beds' => 175,
                'description' => 'Downtown medical center offering a wide range of vaccination and health services.',
                'status' => 'active',
                'rating' => 4.1,
            ],
        ];

        foreach ($hospitals as $hospital) {
            Hospital::create($hospital);
        }
    }
}
