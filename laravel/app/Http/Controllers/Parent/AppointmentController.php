<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Child, Hospital, Vaccine, Notification};
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('parent_id', auth()->id())
            ->with(['child', 'hospital', 'vaccine'])
            ->latest()
            ->paginate(10);

        return view('parent.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $children = Child::where('user_id', auth()->id())->where('status', 'active')->get();
        $hospitals = Hospital::where('status', 'active')->get();
        $vaccines = Vaccine::where('status', 'active')->get();

        return view('parent.appointments.create', compact('children', 'hospitals', 'vaccines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'dose' => 'required|string|max:50',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify child belongs to parent
        $child = Child::where('id', $validated['child_id'])
            ->where('user_id', auth()->id())
            ->first();

        abort_unless($child, 403);

        // Prevent duplicate pending/confirmed appointments for same child+vaccine+hospital
        $duplicate = Appointment::where('child_id', $validated['child_id'])
            ->where('hospital_id', $validated['hospital_id'])
            ->where('vaccine_id', $validated['vaccine_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['error' => 'An active appointment already exists for this child, vaccine, and hospital.'])->withInput();
        }

        $validated['parent_id'] = auth()->id();
        $validated['status'] = 'pending';

        $appointment = Appointment::create($validated);

        return redirect()->route('parent.appointments.index')
            ->with('success', 'Appointment request submitted successfully.');
    }

    public function show(Appointment $appointment)
    {
        abort_unless($appointment->parent_id === auth()->id(), 403);

        $appointment->load(['child', 'hospital', 'vaccine']);

        return view('parent.appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment)
    {
        abort_unless($appointment->parent_id === auth()->id(), 403);
        abort_unless(in_array($appointment->status, ['pending', 'confirmed']), 400);

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('parent.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
