<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\{Child, VaccinationSchedule, VaccinationRecord};

class VaccinationController extends Controller
{
    public function schedule()
    {
        $childIds = Child::where('user_id', auth()->id())->pluck('id');

        $schedules = VaccinationSchedule::whereIn('child_id', $childIds)
            ->with(['child', 'vaccine'])
            ->orderBy('due_date')
            ->paginate(10);

        return view('parent.vaccinations.schedule', compact('schedules'));
    }

    public function history()
    {
        $childIds = Child::where('user_id', auth()->id())->pluck('id');

        $records = VaccinationRecord::whereIn('child_id', $childIds)
            ->with(['child', 'vaccine', 'hospital'])
            ->latest('administered_at')
            ->paginate(10);

        return view('parent.vaccinations.history', compact('records'));
    }

    public function show(VaccinationRecord $record)
    {
        $childIds = Child::where('user_id', auth()->id())->pluck('id');
        abort_unless($childIds->contains($record->child_id), 403);

        $record->load(['child', 'vaccine', 'hospital']);

        return view('parent.vaccinations.show', compact('record'));
    }
}
