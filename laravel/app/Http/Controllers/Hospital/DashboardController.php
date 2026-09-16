<?php
namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, VaccinationRecord, VaccineInventory};

class DashboardController extends Controller
{
    public function index()
    {
        $hospitalId = auth()->user()->hospital_id;
        $today = now()->toDateString();

        $todayAppointments = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->with(['parent', 'child', 'vaccine'])
            ->get();

        $upcomingAppointments = Appointment::where('hospital_id', $hospitalId)
            ->where('appointment_date', '>=', $today)
            ->where('status', 'confirmed')
            ->with(['parent', 'child', 'vaccine'])
            ->orderBy('appointment_date')
            ->take(10)
            ->get();

        $pendingCount = Appointment::where('hospital_id', $hospitalId)
            ->where('status', 'pending')
            ->count();

        $completedToday = VaccinationRecord::where('hospital_id', $hospitalId)
            ->whereDate('administered_at', $today)
            ->count();

        $totalVaccinations = VaccinationRecord::where('hospital_id', $hospitalId)->count();

        $lowStockVaccines = VaccineInventory::where('hospital_id', $hospitalId)
            ->where('available', '<=', 5)
            ->with('vaccine')
            ->get();

        $inventoryCount = VaccineInventory::where('hospital_id', $hospitalId)
            ->where('available', '>', 0)
            ->count();

        return view('hospital.dashboard.index', compact(
            'todayAppointments', 'upcomingAppointments', 'pendingCount',
            'completedToday', 'totalVaccinations', 'lowStockVaccines', 'inventoryCount'
        ));
    }
}
