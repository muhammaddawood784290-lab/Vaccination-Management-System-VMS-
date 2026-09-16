<?php
namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\VaccineInventory;
use Illuminate\Http\Request;

class VaccineInventoryController extends Controller
{
    private function hospitalId(): int
    {
        return auth()->user()->hospital_id;
    }

    public function index()
    {
        $inventory = VaccineInventory::where('hospital_id', $this->hospitalId())
            ->with('vaccine')
            ->paginate(15);

        return view('hospital.vaccines.index', compact('inventory'));
    }

    public function update(Request $request, VaccineInventory $inventory)
    {
        abort_unless($inventory->hospital_id === $this->hospitalId(), 403);

        $validated = $request->validate([
            'available' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:0|gte:available',
        ]);

        $inventory->update([
            'available' => $validated['available'],
            'capacity' => $validated['capacity'],
            'last_updated' => now(),
        ]);

        return redirect()->route('hospital.vaccines.index')
            ->with('success', 'Inventory updated successfully.');
    }
}
