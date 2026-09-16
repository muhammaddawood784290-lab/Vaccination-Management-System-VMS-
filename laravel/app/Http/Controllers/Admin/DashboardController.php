<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Child, Vaccine, Hospital, Appointment, VaccinationRecord, ParentRequest, User};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%m', administered_at)" : "MONTH(administered_at)";
        $yearExpr = $driver === 'sqlite' ? "strftime('%Y', administered_at)" : "YEAR(administered_at)";
        $monthlyVaccinations = VaccinationRecord::selectRaw("{$monthExpr} as month, COUNT(*) as count")
            ->whereRaw("{$yearExpr} = ?", [now()->year])
            ->groupBy("month")
            ->orderBy("month")
            ->pluck("count", "month")
            ->toArray();

        $data = [
            "totalChildren" => Child::count(),
            "totalVaccines" => Vaccine::where("status", "active")->count(),
            "totalHospitals" => Hospital::where("status", "active")->count(),
            "totalAppointments" => Appointment::count(),
            "pendingRequests" => ParentRequest::where("status", "pending")->count(),
            "upcomingAppointments" => Appointment::where("status", "confirmed")
                ->where("appointment_date", ">=", now()->toDateString())
                ->count(),
            "completedVaccinations" => VaccinationRecord::count(),
            "pendingApprovals" => Hospital::where("status", "pending")->count(),
            "recentAppointments" => Appointment::with(["child", "hospital", "vaccine", "parent"])
                ->latest()->take(5)->get(),
            "recentRecords" => VaccinationRecord::with(["child", "hospital", "vaccine"])
                ->latest()->take(5)->get(),
            "recentRequests" => ParentRequest::with(["user", "hospital"])
                ->latest()->take(5)->get(),
            "appointmentStats" => [
                "pending" => Appointment::where("status", "pending")->count(),
                "approved" => Appointment::where("status", "approved")->count(),
                "confirmed" => Appointment::where("status", "confirmed")->count(),
                "completed" => Appointment::where("status", "completed")->count(),
                "cancelled" => Appointment::where("status", "cancelled")->count(),
                "no_show" => Appointment::where("status", "no_show")->count(),
            ],
            "monthlyVaccinations" => $monthlyVaccinations,
            "topHospitals" => Hospital::withCount("vaccinationRecords")
                ->orderByDesc("vaccination_records_count")
                ->take(5)
                ->get(),
            "vaccineUsage" => VaccinationRecord::with("vaccine")
                ->selectRaw("vaccine_id, COUNT(*) as count")
                ->groupBy("vaccine_id")
                ->orderByDesc("count")
                ->take(5)
                ->get(),
        ];
        return view("admin.dashboard.index", $data);
    }
}
