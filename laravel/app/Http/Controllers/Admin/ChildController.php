<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::with(['user', 'vaccinationRecords.vaccine'])->latest();
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->gender && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }
        $children = $query->paginate(15)->withQueryString();
        return view('admin.children.index', compact('children'));
    }

    public function show(Child $child)
    {
        $child->load(['user', 'vaccinationRecords.vaccine', 'vaccinationRecords.hospital', 'appointments.hospital', 'vaccinationSchedules.vaccine', 'appointments.vaccine']);
        return view('admin.children.show', compact('child'));
    }
}
