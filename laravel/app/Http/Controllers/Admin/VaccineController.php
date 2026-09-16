<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vaccine;
use Illuminate\Http\Request;

class VaccineController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaccine::latest();
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('code', 'like', '%'.$request->search.'%');
        }
        $vaccines = $query->paginate(15)->withQueryString();
        $stats = [
            'total' => Vaccine::count(),
            'active' => Vaccine::where('status', 'active')->count(),
            'inactive' => Vaccine::where('status', 'inactive')->count(),
        ];
        return view('admin.vaccines.index', compact('vaccines', 'stats'));
    }

    public function create() { return view('admin.vaccines.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:vaccines,code',
            'doses' => 'required|integer|min:1',
            'age_range' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'manufacturer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        Vaccine::create($validated);
        return redirect()->route('admin.vaccines.index')->with('success', 'Vaccine created successfully.');
    }

    public function edit(Vaccine $vaccine) { return view('admin.vaccines.edit', compact('vaccine')); }

    public function update(Request $request, Vaccine $vaccine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:vaccines,code,'.$vaccine->id,
            'doses' => 'required|integer|min:1',
            'age_range' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'manufacturer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $vaccine->update($validated);
        return redirect()->route('admin.vaccines.index')->with('success', 'Vaccine updated successfully.');
    }

    public function destroy(Vaccine $vaccine)
    {
        $vaccine->update(['status' => 'inactive']);
        return redirect()->route('admin.vaccines.index')->with('success', 'Vaccine deactivated.');
    }
}
