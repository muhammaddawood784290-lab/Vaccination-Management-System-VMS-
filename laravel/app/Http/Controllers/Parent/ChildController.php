<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\{Child, VaccinationSchedule, VaccinationRecord, Vaccine};
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::where('user_id', auth()->id())
            ->withCount(['vaccinationRecords', 'appointments'])
            ->get();

        return view('parent.children.index', compact('children'));
    }

    public function create()
    {
        return view('parent.children.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'blood_group' => 'nullable|string|max:10',
            'relationship' => 'required|string|max:50',
            'allergies' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';

        Child::create($validated);

        return redirect()->route('parent.children.index')
            ->with('success', 'Child added successfully.');
    }

    public function show(Child $child)
    {
        abort_unless($child->user_id === auth()->id(), 403);

        $child->load(['vaccinationSchedules.vaccine', 'vaccinationRecords.vaccine', 'vaccinationRecords.hospital']);
        $vaccines = Vaccine::where('status', 'active')->get();

        return view('parent.children.show', compact('child', 'vaccines'));
    }

    public function edit(Child $child)
    {
        abort_unless($child->user_id === auth()->id(), 403);

        return view('parent.children.edit', compact('child'));
    }

    public function update(Request $request, Child $child)
    {
        abort_unless($child->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'blood_group' => 'nullable|string|max:10',
            'relationship' => 'required|string|max:50',
            'allergies' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $child->update($validated);

        return redirect()->route('parent.children.show', $child)
            ->with('success', 'Child updated successfully.');
    }

    public function destroy(Child $child)
    {
        abort_unless($child->user_id === auth()->id(), 403);

        $child->delete();

        return redirect()->route('parent.children.index')
            ->with('success', 'Child removed successfully.');
    }
}
