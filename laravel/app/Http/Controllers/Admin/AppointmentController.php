<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Notification};
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['child', 'parent', 'hospital', 'vaccine'])->latest();
        if ($request->search) {
            $query->whereHas('child', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
                ->orWhereHas('hospital', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $appointments = $query->paginate(15)->withQueryString();
        $stats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'no_show' => Appointment::where('status', 'no_show')->count(),
        ];
        return view('admin.appointments.index', compact('appointments', 'stats'));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['child', 'parent', 'hospital', 'vaccine', 'vaccinationRecord']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function approve(Appointment $appointment)
    {
        if ($appointment->status !== 'pending') return back()->with('error', 'Only pending appointments can be approved.');
        $appointment->update(['status' => 'approved']);

        Notification::create([
            'user_id' => $appointment->parent_id,
            'title' => 'Appointment Approved',
            'message' => "Your appointment for {$appointment->child->name} at {$appointment->hospital->name} has been approved.",
            'type' => 'appointment_approved',
            'data' => ['appointment_id' => $appointment->id],
        ]);

        return back()->with('success', 'Appointment approved.');
    }

    public function reject(Appointment $appointment)
    {
        if ($appointment->status !== 'pending') return back()->with('error', 'Only pending appointments can be rejected.');
        $appointment->update(['status' => 'rejected']);

        Notification::create([
            'user_id' => $appointment->parent_id,
            'title' => 'Appointment Rejected',
            'message' => "Your appointment for {$appointment->child->name} at {$appointment->hospital->name} has been rejected.",
            'type' => 'appointment_rejected',
            'data' => ['appointment_id' => $appointment->id],
        ]);

        return back()->with('success', 'Appointment rejected.');
    }

    public function cancel(Appointment $appointment)
    {
        if (in_array($appointment->status, ['completed', 'cancelled'])) return back()->with('error', 'Cannot cancel this appointment.');
        $appointment->update(['status' => 'cancelled']);

        Notification::create([
            'user_id' => $appointment->parent_id,
            'title' => 'Appointment Cancelled',
            'message' => "Your appointment for {$appointment->child->name} has been cancelled by admin.",
            'type' => 'appointment_cancelled',
            'data' => ['appointment_id' => $appointment->id],
        ]);

        return back()->with('success', 'Appointment cancelled.');
    }
}
