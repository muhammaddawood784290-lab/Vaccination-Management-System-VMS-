<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@vms.permetheon.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1-555-0001',
            'city' => 'New York',
        ]);

        // Parent users
        $parents = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com', 'city' => 'New York'],
            ['name' => 'Michael Brown', 'email' => 'michael@example.com', 'city' => 'Brooklyn'],
            ['name' => 'Emily Davis', 'email' => 'emily@example.com', 'city' => 'Manhattan'],
            ['name' => 'David Wilson', 'email' => 'david@example.com', 'city' => 'Queens'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa@example.com', 'city' => 'Bronx'],
        ];

        foreach ($parents as $parent) {
            User::create([
                'name' => $parent['name'],
                'email' => $parent['email'],
                'password' => Hash::make('password'),
                'role' => 'parent',
                'phone' => '+1-555-1' . str_pad(array_search($parent, $parents) + 1, 3, '0', STR_PAD_LEFT),
                'city' => $parent['city'],
            ]);
        }

        // Hospital users - link to existing hospitals
        $hospitalUsers = [
            ['email' => 'citygeneral@vms.permetheon.com', 'hospital_code' => 'CGH-001', 'name' => 'City General Admin'],
            ['email' => 'community@vms.permetheon.com', 'hospital_code' => 'CHC-002', 'name' => 'Community Health Admin'],
            ['email' => 'childrens@vms.permetheon.com', 'hospital_code' => 'CMC-003', 'name' => "Children's Medical Admin"],
            ['email' => 'westside@vms.permetheon.com', 'hospital_code' => 'WSH-004', 'name' => 'Westside Admin'],
            ['email' => 'downtown@vms.permetheon.com', 'hospital_code' => 'DMC-005', 'name' => 'Downtown Admin'],
        ];

        foreach ($hospitalUsers as $hu) {
            $hospital = Hospital::where('code', $hu['hospital_code'])->first();
            User::create([
                'name' => $hu['name'],
                'email' => $hu['email'],
                'password' => Hash::make('password'),
                'role' => 'hospital',
                'phone' => '+1-555-2' . str_pad(array_search($hu, $hospitalUsers) + 1, 3, '0', STR_PAD_LEFT),
                'hospital_id' => $hospital?->id,
            ]);
        }
    }
}
