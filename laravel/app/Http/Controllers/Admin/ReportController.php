<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{VaccinationRecord, Child, Hospital, Appointment};

class ReportController extends Controller
{
    public function index()
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%m', administered_at)" : "MONTH(administered_at)";
        $yearExpr = $driver === 'sqlite' ? "strftime('%Y', administered_at)" : "YEAR(administered_at)";
        $monthlyData = VaccinationRecord::selectRaw("{$monthExpr} as month, COUNT(*) as count")
            ->whereRaw("{$yearExpr} = ?", [now()->year])
            ->groupBy('month')->orderBy('month')->pluck('count', 'month')->toArray();

        $data = [
            'totalRecords' => VaccinationRecord::count(),
            'totalChildren' => Child::count(),
            'totalHospitals' => Hospital::where('status', 'active')->count(),
            'totalAppointments' => Appointment::count(),
            'monthlyData' => $monthlyData,
            'vaccineDistribution' => VaccinationRecord::with('vaccine')
                ->selectRaw('vaccine_id, COUNT(*) as count')
                ->groupBy('vaccine_id')->orderByDesc('count')->get(),
            'hospitalPerformance' => Hospital::withCount('vaccinationRecords')
                ->where('status', 'active')->orderByDesc('vaccination_records_count')->get(),
            'recentRecords' => VaccinationRecord::with(['child', 'vaccine', 'hospital'])->latest()->take(20)->get(),
        ];
        return view('admin.reports', $data);
    }
}
