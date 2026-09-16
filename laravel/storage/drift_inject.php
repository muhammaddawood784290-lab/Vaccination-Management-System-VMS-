<?php

// Temporary drift-injection script for testing db:seed-check / db:restore-demo
// (run against a THROWAWAY DB COPY only — never the live database).

use App\Models\{Child, Hospital, User, Vaccine, VaccineInventory};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// 1. Extra user not in seed contract (with a child + notification to test cascades)
$extraUser = User::create([
    'name' => 'Drift User', 'email' => 'drift@test.com',
    'password' => Hash::make('x'), 'role' => 'parent',
]);
Child::create([
    'user_id' => $extraUser->id, 'name' => 'AuditTestChild',
    'date_of_birth' => '2023-01-01', 'gender' => 'male', 'blood_group' => 'O+',
    'relationship' => 'father', 'allergies' => 'None', 'status' => 'active',
]);

// 2. Extra hospital not in seed contract (with an inventory row to test cascade)
$extraHospital = Hospital::create([
    'name' => 'Fake Clinic', 'code' => 'FAKE-999', 'email' => 'fake@clinic.com',
    'phone' => '+1-555-9999', 'address' => '1 Nowhere St', 'city' => 'Nowhere',
    'state' => 'Nowhere', 'contact_person' => 'Dr Fake', 'designation' => 'Admin',
    'total_beds' => 10, 'description' => 'Not seeded.', 'status' => 'active', 'rating' => 3.0,
]);
$vaccineId = Vaccine::where('code', 'BCG')->value('id');
VaccineInventory::create([
    'hospital_id' => $extraHospital->id, 'vaccine_id' => $vaccineId,
    'available' => 5, 'capacity' => 10, 'batch_number' => 'BATCH-FAKE-1',
    'expiry_date' => now()->addYear(), 'last_updated' => now(),
]);

// 3. Delete a seeded vaccine (missing seeded vaccine + its inventory combos)
$vaccine = Vaccine::where('code', 'HPV')->first();
$vaccine->delete();

// 4. Status drift on a seeded hospital + seeded vaccine
Hospital::where('code', 'DMC-005')->update(['status' => 'inactive']);
Vaccine::where('code', 'MMR')->update(['status' => 'inactive']);

// 5. Unmapped inventory row: re-point a contract row to the extra hospital
$unmapped = VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'OPV'))
    ->whereHas('hospital', fn ($q) => $q->where('code', 'CGH-001'))->first();
$unmapped->update(['hospital_id' => $extraHospital->id]);

// 6. Invalid counts on a contract inventory row
$badCounts = VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'IPV'))
    ->whereHas('hospital', fn ($q) => $q->where('code', 'CGH-001'))->first();
$badCounts->update(['available' => 500, 'capacity' => 100]);

// 7. Delete a contract inventory combo (missing inventory row)
VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'PCV'))
    ->whereHas('hospital', fn ($q) => $q->where('code', 'WSH-004'))->delete();

echo "Drift injected:\n";
echo '  users: ' . User::count() . " (expected 11)\n";
echo '  children: ' . Child::count() . " (expected 6)\n";
echo '  hospitals: ' . Hospital::count() . " (expected 5)\n";
echo '  vaccines: ' . Vaccine::count() . " (expected 10)\n";
echo '  inventory: ' . VaccineInventory::count() . " (expected 50)\n";
