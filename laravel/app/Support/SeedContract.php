<?php

namespace App\Support;

use App\Models\Child;
use App\Models\Hospital;
use App\Models\User;
use App\Models\Vaccine;
use App\Models\VaccineInventory;
use Illuminate\Support\Facades\Hash;

/**
 * Single source of truth for the demo seeder contract.
 *
 * Used by:
 *  - db:seed-check   (detects drift, read-only)
 *  - db:restore-demo (repairs drift)
 *  - tests/Feature/SeedIntegrityTest.php
 *
 * Field values mirror Database\Seeders\{HospitalSeeder,VaccineSeeder,UserSeeder,ChildSeeder}
 * so rows restored by db:restore-demo are identical to seeder output.
 *
 * Inventory note: VaccineInventorySeeder randomises stock values, so the inventory
 * contract is STRUCTURAL, not value-exact:
 *   - exactly one row per active seeded hospital × active seeded vaccine
 *     (the unique(hospital_id, vaccine_id) index guarantees at most one)
 *   - no inventory rows mapped to non-contract hospitals/vaccines
 *   - valid counts: 0 <= available <= capacity
 * Restored inventory rows use seeder-style values (same batch-number format,
 * randomised stock ranges).
 */
class SeedContract
{
    /**
     * Password seeded by UserSeeder for every demo account
     * (also advertised on the login page).
     */
    public const DEMO_PASSWORD = 'password';

    /**
     * Every expected demo user: email => [role, name, phone, city].
     * Order matches UserSeeder. Hospital users have no city.
     */
    public const USERS = [
        'admin@vms.permetheon.com' => ['role' => 'admin', 'name' => 'System Administrator', 'phone' => '+1-555-0001', 'city' => 'New York'],
        'sarah@example.com' => ['role' => 'parent', 'name' => 'Sarah Johnson', 'phone' => '+1-555-1001', 'city' => 'New York'],
        'michael@example.com' => ['role' => 'parent', 'name' => 'Michael Brown', 'phone' => '+1-555-1002', 'city' => 'Brooklyn'],
        'emily@example.com' => ['role' => 'parent', 'name' => 'Emily Davis', 'phone' => '+1-555-1003', 'city' => 'Manhattan'],
        'david@example.com' => ['role' => 'parent', 'name' => 'David Wilson', 'phone' => '+1-555-1004', 'city' => 'Queens'],
        'lisa@example.com' => ['role' => 'parent', 'name' => 'Lisa Anderson', 'phone' => '+1-555-1005', 'city' => 'Bronx'],
        'citygeneral@vms.permetheon.com' => ['role' => 'hospital', 'name' => 'City General Admin', 'phone' => '+1-555-2001', 'city' => null],
        'community@vms.permetheon.com' => ['role' => 'hospital', 'name' => 'Community Health Admin', 'phone' => '+1-555-2002', 'city' => null],
        'childrens@vms.permetheon.com' => ['role' => 'hospital', 'name' => "Children's Medical Admin", 'phone' => '+1-555-2003', 'city' => null],
        'westside@vms.permetheon.com' => ['role' => 'hospital', 'name' => 'Westside Admin', 'phone' => '+1-555-2004', 'city' => null],
        'downtown@vms.permetheon.com' => ['role' => 'hospital', 'name' => 'Downtown Admin', 'phone' => '+1-555-2005', 'city' => null],
    ];

    /**
     * Every expected demo child: name => [parent email, date_of_birth, gender, blood_group, relationship, allergies].
     * Order mirrors ChildSeeder.
     */
    public const CHILDREN = [
        'Emma Johnson' => ['parent' => 'sarah@example.com', 'date_of_birth' => '2023-03-15', 'gender' => 'female', 'blood_group' => 'A+', 'relationship' => 'mother', 'allergies' => 'None'],
        'Liam Johnson' => ['parent' => 'sarah@example.com', 'date_of_birth' => '2022-08-20', 'gender' => 'male', 'blood_group' => 'O+', 'relationship' => 'mother', 'allergies' => 'Penicillin'],
        'Olivia Johnson' => ['parent' => 'sarah@example.com', 'date_of_birth' => '2024-01-10', 'gender' => 'female', 'blood_group' => 'B+', 'relationship' => 'mother', 'allergies' => 'None'],
        'Noah Brown' => ['parent' => 'michael@example.com', 'date_of_birth' => '2023-11-05', 'gender' => 'male', 'blood_group' => 'AB+', 'relationship' => 'father', 'allergies' => 'None'],
        'Ava Davis' => ['parent' => 'emily@example.com', 'date_of_birth' => '2023-06-25', 'gender' => 'female', 'blood_group' => 'A-', 'relationship' => 'mother', 'allergies' => 'Latex'],
        'Ethan Davis' => ['parent' => 'emily@example.com', 'date_of_birth' => '2024-04-18', 'gender' => 'male', 'blood_group' => 'O-', 'relationship' => 'mother', 'allergies' => 'None'],
    ];

    /**
     * Hospital user email => hospital code it must be linked to
     * (UserSeeder links by HospitalSeeder's code column).
     */
    public const HOSPITAL_LINKS = [
        'citygeneral@vms.permetheon.com' => 'CGH-001',
        'community@vms.permetheon.com' => 'CHC-002',
        'childrens@vms.permetheon.com' => 'CMC-003',
        'westside@vms.permetheon.com' => 'WSH-004',
        'downtown@vms.permetheon.com' => 'DMC-005',
    ];

    /**
     * Every expected demo hospital: code => seeder-identical fields (mirrors HospitalSeeder).
     * Keyed by the unique code column; 'code' itself is added on restore.
     */
    public const HOSPITALS = [
        'CGH-001' => [
            'name' => 'City General Hospital',
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
        'CHC-002' => [
            'name' => 'Community Health Center',
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
        'CMC-003' => [
            'name' => "Children's Medical Center",
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
        'WSH-004' => [
            'name' => 'Westside Hospital',
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
        'DMC-005' => [
            'name' => 'Downtown Medical Center',
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

    /**
     * Every expected demo vaccine: code => seeder-identical fields (mirrors VaccineSeeder).
     * Keyed by the unique code column; 'code' itself is added on restore.
     */
    public const VACCINES = [
        'BCG' => [
            'name' => 'Bacillus Calmette-Guerin (BCG)',
            'doses' => 1,
            'age_range' => 'At birth',
            'type' => 'Live attenuated',
            'manufacturer' => 'Serum Institute of India',
            'description' => 'Protects against tuberculosis. Given at birth in countries with high TB prevalence.',
            'status' => 'active',
        ],
        'HEPB' => [
            'name' => 'Hepatitis B',
            'doses' => 3,
            'age_range' => 'Birth, 1 month, 6 months',
            'type' => 'Recombinant',
            'manufacturer' => 'GlaxoSmithKline',
            'description' => 'Protects against Hepatitis B virus infection.',
            'status' => 'active',
        ],
        'OPV' => [
            'name' => 'Oral Polio Vaccine (OPV)',
            'doses' => 4,
            'age_range' => '6 weeks, 10 weeks, 14 weeks, 4-6 years',
            'type' => 'Live attenuated',
            'manufacturer' => 'Sanofi Pasteur',
            'description' => 'Protects against poliomyelitis. Administered orally.',
            'status' => 'active',
        ],
        'PENTA' => [
            'name' => 'Pentavalent Vaccine',
            'doses' => 3,
            'age_range' => '6 weeks, 10 weeks, 14 weeks',
            'type' => 'Combination',
            'manufacturer' => 'Serum Institute of India',
            'description' => 'Protects against Diphtheria, Tetanus, Pertussis, Hepatitis B, and Haemophilus influenzae type b.',
            'status' => 'active',
        ],
        'MMR' => [
            'name' => 'Measles-Mumps-Rubella (MMR)',
            'doses' => 2,
            'age_range' => '9 months, 15 months',
            'type' => 'Live attenuated',
            'manufacturer' => 'Merck & Co.',
            'description' => 'Protects against Measles, Mumps, and Rubella.',
            'status' => 'active',
        ],
        'ROTAV' => [
            'name' => 'Rotavirus Vaccine',
            'doses' => 3,
            'age_range' => '6 weeks, 10 weeks, 14 weeks',
            'type' => 'Live attenuated',
            'manufacturer' => 'GlaxoSmithKline',
            'description' => 'Protects against rotavirus gastroenteritis.',
            'status' => 'active',
        ],
        'PCV' => [
            'name' => 'Pneumococcal Conjugate Vaccine (PCV)',
            'doses' => 4,
            'age_range' => '6 weeks, 10 weeks, 14 weeks, 12-15 months',
            'type' => 'Conjugate',
            'manufacturer' => 'Pfizer',
            'description' => 'Protects against pneumococcal diseases including pneumonia and meningitis.',
            'status' => 'active',
        ],
        'IPV' => [
            'name' => 'Inactivated Polio Vaccine (IPV)',
            'doses' => 1,
            'age_range' => '14 weeks',
            'type' => 'Inactivated',
            'manufacturer' => 'Sanofi Pasteur',
            'description' => 'Injectable polio vaccine used as a booster alongside OPV.',
            'status' => 'active',
        ],
        'DTAP' => [
            'name' => 'Diphtheria-Tetanus-Pertussis (DTaP)',
            'doses' => 5,
            'age_range' => '2 months, 4 months, 6 months, 15-18 months, 4-6 years',
            'type' => 'Toxoid/acellular',
            'manufacturer' => 'Sanofi Pasteur',
            'description' => 'Protects against Diphtheria, Tetanus, and Pertussis (Whooping Cough).',
            'status' => 'active',
        ],
        'HPV' => [
            'name' => 'Human Papillomavirus (HPV)',
            'doses' => 2,
            'age_range' => '9-14 years',
            'type' => 'Recombinant',
            'manufacturer' => 'Merck & Co.',
            'description' => 'Protects against HPV infections that can cause cervical cancer.',
            'status' => 'active',
        ],
    ];

    /**
     * Total inventory rows implied by the contract
     * (one per seeded hospital × seeded vaccine combination).
     */
    public static function expectedInventoryCount(): int
    {
        return count(self::HOSPITALS) * count(self::VACCINES);
    }

    /**
     * Detect every difference between the database and this contract.
     *
     * @return string[] human-readable drift findings (empty = no drift)
     */
    public static function driftFindings(): array
    {
        $failures = [];

        $users = User::all()->keyBy('email');
        $children = Child::all();

        // --- Users: extras, missing, wrong role, password drift ---
        foreach ($users as $email => $user) {
            if (!array_key_exists($email, self::USERS)) {
                $failures[] = "Extra user not in seed contract: {$email} (role={$user->role})";
            }
        }

        foreach (self::USERS as $email => $expected) {
            $user = $users->get($email);

            if ($user === null) {
                $failures[] = "Missing seeded user: {$email}";
                continue;
            }

            if ($user->role !== $expected['role']) {
                $failures[] = "User {$email} has role '{$user->role}', expected '{$expected['role']}'";
            }

            if (!Hash::check(self::DEMO_PASSWORD, $user->password)) {
                $failures[] = "User {$email} password does not match the seeded demo password";
            }
        }

        // --- Hospital users must be linked to their seeded hospital ---
        foreach (self::HOSPITAL_LINKS as $email => $code) {
            $user = $users->get($email);

            if ($user === null) {
                continue; // already reported as missing
            }

            $hospital = Hospital::where('code', $code)->first();

            if ($hospital === null) {
                $failures[] = "Missing seeded hospital with code {$code}";
                continue;
            }

            if ($user->hospital_id !== $hospital->id) {
                $failures[] = "Hospital user {$email} is not linked to seeded hospital {$code}";
            }
        }

        // --- Children: extras, missing, wrong parent, orphans ---
        $usersById = $users->keyBy('id');
        $matchedChildNames = [];

        foreach ($children as $child) {
            $parent = $usersById->get($child->user_id);

            if ($parent === null) {
                $failures[] = "Orphaned child '{$child->name}' (id={$child->id}) points to non-existent user_id {$child->user_id}";
                continue;
            }

            if (!array_key_exists($child->name, self::CHILDREN)) {
                $failures[] = "Extra child not in seed contract: '{$child->name}' (parent={$parent->email})";
                continue;
            }

            $expectedParentEmail = self::CHILDREN[$child->name]['parent'];

            if ($parent->email !== $expectedParentEmail) {
                $failures[] = "Child '{$child->name}' belongs to {$parent->email}, seeder assigns them to {$expectedParentEmail}";
            }

            $matchedChildNames[] = $child->name;
        }

        foreach (self::CHILDREN as $name => $expected) {
            if (!in_array($name, $matchedChildNames, true)) {
                $failures[] = "Missing seeded child: '{$name}' (parent={$expected['parent']})";
            }
        }

        // --- Hospitals: extras, missing, status drift ---
        $hospitals = Hospital::all()->keyBy('code');

        foreach ($hospitals as $code => $hospital) {
            if (!array_key_exists($code, self::HOSPITALS)) {
                $failures[] = "Extra hospital not in seed contract: {$hospital->name} ({$code})";
            }
        }

        foreach (self::HOSPITALS as $code => $expected) {
            $hospital = $hospitals->get($code);

            if ($hospital === null) {
                $failures[] = "Missing seeded hospital: {$code}";
                continue;
            }

            if ($hospital->status !== $expected['status']) {
                $failures[] = "Hospital {$code} has status '{$hospital->status}', expected '{$expected['status']}'";
            }
        }

        // --- Vaccines: extras, missing, status drift ---
        $vaccines = Vaccine::all()->keyBy('code');

        foreach ($vaccines as $code => $vaccine) {
            if (!array_key_exists($code, self::VACCINES)) {
                $failures[] = "Extra vaccine not in seed contract: {$vaccine->name} ({$code})";
            }
        }

        foreach (self::VACCINES as $code => $expected) {
            $vaccine = $vaccines->get($code);

            if ($vaccine === null) {
                $failures[] = "Missing seeded vaccine: {$code}";
                continue;
            }

            if ($vaccine->status !== $expected['status']) {
                $failures[] = "Vaccine {$code} has status '{$vaccine->status}', expected '{$expected['status']}'";
            }
        }

        // --- Inventory: rows unmapped to the contract, invalid counts, missing matrix combos ---
        $contractPairs = [];

        foreach (array_keys(self::HOSPITALS) as $hospitalCode) {
            foreach (array_keys(self::VACCINES) as $vaccineCode) {
                $contractPairs[$hospitalCode . '|' . $vaccineCode] = true;
            }
        }

        $foundPairs = [];

        foreach (VaccineInventory::with(['hospital:id,code', 'vaccine:id,code'])->get() as $row) {
            $hospitalCode = $row->hospital?->code;
            $vaccineCode = $row->vaccine?->code;
            $pair = $hospitalCode . '|' . $vaccineCode;

            if ($hospitalCode === null || $vaccineCode === null || !isset($contractPairs[$pair])) {
                $failures[] = "Inventory row id={$row->id} does not map to the seeded hospital/vaccine contract"
                    . ' (hospital=' . ($hospitalCode ?? 'missing') . ', vaccine=' . ($vaccineCode ?? 'missing') . ')';
                continue;
            }

            if ($row->capacity < 0 || $row->available < 0 || $row->available > $row->capacity) {
                $failures[] = "Inventory row id={$row->id} ({$hospitalCode}/{$vaccineCode}) has invalid counts"
                    . " (available={$row->available}, capacity={$row->capacity})";
            }

            $foundPairs[$pair] = true;
        }

        foreach (array_keys($contractPairs) as $pair) {
            if (!isset($foundPairs[$pair])) {
                [$hospitalCode, $vaccineCode] = explode('|', $pair);
                $failures[] = "Missing inventory row for seeded combination {$hospitalCode}/{$vaccineCode}";
            }
        }

        return $failures;
    }
}
