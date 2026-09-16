<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospital::withCount(['users', 'vaccinationRecords'])->latest();
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('city', 'like', '%'.$request->search.'%');
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $hospitals = $query->paginate(15)->withQueryString();
        $stats = [
            'total' => Hospital::count(),
            'active' => Hospital::where('status', 'active')->count(),
            'pending' => Hospital::where('status', 'pending')->count(),
        ];
        return view('admin.hospitals.index', compact('hospitals', 'stats'));
    }

    public function create() { return view('admin.hospitals.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:hospitals,code',
            'email' => 'required|email|max:255|unique:hospitals,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'designation' => 'nullable|string|max:100',
            'total_beds' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,pending',
        ]);
        Hospital::create($validated);
        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital created successfully.');
    }

    public function show(Hospital $hospital)
    {
        $hospital->loadCount(['users', 'vaccinationRecords', 'appointments'])->load(['vaccineInventory.vaccine']);
        return view('admin.hospitals.show', compact('hospital'));
    }

    public function edit(Hospital $hospital) { return view('admin.hospitals.edit', compact('hospital')); }

    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:hospitals,code,'.$hospital->id,
            'email' => 'required|email|max:255|unique:hospitals,email,'.$hospital->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'designation' => 'nullable|string|max:100',
            'total_beds' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,pending',
        ]);
        $hospital->update($validated);
        return redirect()->route('admin.hospitals.show', $hospital)->with('success', 'Hospital updated successfully.');
    }

    public function toggleStatus(Hospital $hospital)
    {
        $hospital->update(['status' => $hospital->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Hospital status updated.');
    }

    public function approve(Hospital $hospital)
    {
        $hospital->update(['status' => 'active']);
        return back()->with('success', 'Hospital approved and activated.');
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->update(['status' => 'inactive']);
        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital deactivated.');
    }
}
