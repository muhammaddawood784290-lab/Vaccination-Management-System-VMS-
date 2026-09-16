<?php
namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\VaccinationRecord;

class VaccinationController extends Controller
{
    private function hospitalId(): int
    {
        return auth()->user()->hospital_id;
    }

    public function index()
    {
        $records = VaccinationRecord::where('hospital_id', $this->hospitalId())
            ->with(['child', 'vaccine', 'appointment'])
            ->latest('administered_at')
            ->paginate(15);

        return view('hospital.vaccinations.index', compact('records'));
    }

    public function show(VaccinationRecord $record)
    {
        abort_unless($record->hospital_id === $this->hospitalId(), 403);

        $record->load(['child', 'vaccine', 'appointment', 'hospital']);

        return view('hospital.vaccinations.show', compact('record'));
    }
}
