<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\{Hospital, VaccineInventory};

class HospitalController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::where('status', 'active')
            ->withCount('vaccinationRecords')
            ->paginate(12);

        return view('parent.hospitals.index', compact('hospitals'));
    }

    public function show(Hospital $hospital)
    {
        abort_unless($hospital->status === 'active', 404);

        $hospital->loadCount('vaccinationRecords');

        $inventory = VaccineInventory::where('hospital_id', $hospital->id)
            ->where('quantity_available', '>', 0)
            ->with('vaccine')
            ->get();

        return view('parent.hospitals.show', compact('hospital', 'inventory'));
    }
}
