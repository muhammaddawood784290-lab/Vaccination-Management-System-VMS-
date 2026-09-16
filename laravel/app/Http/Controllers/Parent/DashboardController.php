<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\{Child, Appointment, VaccinationRecord, ParentRequest, VaccinationSchedule};

class DashboardController extends Controller
{
    public function index()
    {
        $parentId = auth()->id();

        $children = Child::where('user_id', $parentId)->withCount(['vaccinationRecords', 'appointments'])->get();

        $totalChildren = $children->count();
        $totalVaccinations = VaccinationRecord::whereIn('child_id', $children->pluck('id'))->count();
        $upcomingAppointments = Appointment::where('parent_id', $parentId)
            ->where('status', 'confirmed')
            ->where('appointment_date', '>=', now()->toDateString())
            ->count();
        $pendingRequests = ParentRequest::where('user_id', $parentId)->where('status', 'pending')->count();

        $nextAppointment = Appointment::where('parent_id', $parentId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->with(['child', 'hospital', 'vaccine'])
            ->orderBy('appointment_date')
            ->first();

        $recentVaccinations = VaccinationRecord::whereIn('child_id', $children->pluck('id'))
            ->with(['child', 'vaccine', 'hospital'])
            ->latest('administered_at')
            ->take(5)
            ->get();

        $recentAppointments = Appointment::where('parent_id', $parentId)
            ->with(['child', 'hospital', 'vaccine'])
            ->latest()
            ->take(5)
            ->get();

        return view('parent.dashboard.index', compact(
            'children', 'totalChildren', 'totalVaccinations', 'upcomingAppointments',
            'pendingRequests', 'nextAppointment', 'recentVaccinations', 'recentAppointments'
        ));
    }
}
