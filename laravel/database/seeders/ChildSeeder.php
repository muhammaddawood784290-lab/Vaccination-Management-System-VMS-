<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChildSeeder extends Seeder
{
    public function run(): void
    {
        // Sarah's children (3 children as documented)
        $sarah = User::where('email', 'sarah@example.com')->first();
        if ($sarah) {
            Child::create([
                'user_id' => $sarah->id,
                'name' => 'Emma Johnson',
                'date_of_birth' => '2023-03-15',
                'gender' => 'female',
                'blood_group' => 'A+',
                'relationship' => 'mother',
                'allergies' => 'None',
                'status' => 'active',
            ]);
            Child::create([
                'user_id' => $sarah->id,
                'name' => 'Liam Johnson',
                'date_of_birth' => '2022-08-20',
                'gender' => 'male',
                'blood_group' => 'O+',
                'relationship' => 'mother',
                'allergies' => 'Penicillin',
                'status' => 'active',
            ]);
            Child::create([
                'user_id' => $sarah->id,
                'name' => 'Olivia Johnson',
                'date_of_birth' => '2024-01-10',
                'gender' => 'female',
                'blood_group' => 'B+',
                'relationship' => 'mother',
                'allergies' => 'None',
                'status' => 'active',
            ]);
        }

        // Michael's children (1 child)
        $michael = User::where('email', 'michael@example.com')->first();
        if ($michael) {
            Child::create([
                'user_id' => $michael->id,
                'name' => 'Noah Brown',
                'date_of_birth' => '2023-11-05',
                'gender' => 'male',
                'blood_group' => 'AB+',
                'relationship' => 'father',
                'allergies' => 'None',
                'status' => 'active',
            ]);
        }

        // Emily's children (2 children)
        $emily = User::where('email', 'emily@example.com')->first();
        if ($emily) {
            Child::create([
                'user_id' => $emily->id,
                'name' => 'Ava Davis',
                'date_of_birth' => '2023-06-25',
                'gender' => 'female',
                'blood_group' => 'A-',
                'relationship' => 'mother',
                'allergies' => 'Latex',
                'status' => 'active',
            ]);
            Child::create([
                'user_id' => $emily->id,
                'name' => 'Ethan Davis',
                'date_of_birth' => '2024-04-18',
                'gender' => 'male',
                'blood_group' => 'O-',
                'relationship' => 'mother',
                'allergies' => 'None',
                'status' => 'active',
            ]);
        }
    }
}
