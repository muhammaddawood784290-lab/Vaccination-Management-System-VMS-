<?php
namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, VaccinationRecord, VaccineInventory, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    private function hospitalId(): int
    {
        return auth()->user()->hospital_id;
    }

    public function index()
    {
        $appointments = Appointment::where('hospital_id', $this->hospitalId())
            ->with(['child', 'parent', 'vaccine'])
            ->latest('appointment_date')
            ->paginate(15);

        return view('hospital.appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        abort_unless($appointment->hospital_id === $this->hospitalId(), 403);

        $appointment->load(['child', 'parent', 'vaccine', 'vaccinationRecord']);

        return view('hospital.appointments.show', compact('appointment'));
    }

    public function confirm(Appointment $appointment)
    {
        abort_unless($appointment->hospital_id === $this->hospitalId(), 403);
        abort_unless(in_array($appointment->status, ['pending', 'approved']), 400);

        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => 'confirmed']);

            // Notify parent
            Notification::create([
                'user_id' => $appointment->parent_id,
                'title' => 'Appointment Confirmed',
                'message' => "Your appointment for {$appointment->child->name} has been confirmed by " . auth()->user()->hospital->name . ".",
                'type' => 'appointment_confirmed',
                'data' => ['appointment_id' => $appointment->id],
            ]);
        });

        return redirect()->route('hospital.appointments.show', $appointment)
            ->with('success', 'Appointment confirmed.');
    }

    public function complete(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->hospital_id === $this->hospitalId(), 403);
        abort_unless(in_array($appointment->status, ['confirmed']), 400);

        $validated = $request->validate([
            'dose_number' => 'required|integer|min:1',
            'batch_number' => 'nullable|string|max:100',
            'administered_by' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($appointment, $validated) {
            // Prevent duplicate vaccination records (inside transaction to avoid race condition)
            $existing = VaccinationRecord::where('appointment_id', $appointment->id)->exists();
            abort_if($existing, 409, 'Vaccination already recorded for this appointment.');

            // Create vaccination record
            VaccinationRecord::create([
                'child_id' => $appointment->child_id,
                'hospital_id' => $this->hospitalId(),
                'vaccine_id' => $appointment->vaccine_id,
                'appointment_id' => $appointment->id,
                'dose_number' => $validated['dose_number'],
                'batch_number' => $validated['batch_number'] ?? null,
                'administered_by' => $validated['administered_by'],
                'administered_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update appointment status
            $appointment->update(['status' => 'completed']);

            // Deduct inventory if available
            $inventory = VaccineInventory::where('hospital_id', $this->hospitalId())
                ->where('vaccine_id', $appointment->vaccine_id)
                ->first();

            if ($inventory && $inventory->available > 0) {
                $inventory->decrement('available');
                $inventory->refresh();
                $inventory->update(['last_updated' => now()]);
            }

            // Notify parent
            Notification::create([
                'user_id' => $appointment->parent_id,
                'title' => 'Vaccination Completed',
                'message' => "Vaccination for {$appointment->child->name} has been recorded successfully.",
                'type' => 'vaccination_completed',
                'data' => ['appointment_id' => $appointment->id],
            ]);
        });

        return redirect()->route('hospital.appointments.show', $appointment)
            ->with('success', 'Vaccination recorded successfully.');
    }

    public function noShow(Appointment $appointment)
    {
        abort_unless($appointment->hospital_id === $this->hospitalId(), 403);
        abort_unless(in_array($appointment->status, ['confirmed', 'pending', 'approved']), 400);

        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => 'no_show']);

            // Notify parent
            Notification::create([
                'user_id' => $appointment->parent_id,
                'title' => 'Appointment Missed',
                'message' => "Your appointment for {$appointment->child->name} was marked as no-show.",
                'type' => 'appointment_no_show',
                'data' => ['appointment_id' => $appointment->id],
            ]);
        });

        return redirect()->route('hospital.appointments.show', $appointment)
            ->with('success', 'Appointment marked as no-show.');
    }
}
