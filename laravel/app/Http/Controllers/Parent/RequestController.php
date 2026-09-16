<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\ParentRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        $requests = ParentRequest::where('user_id', auth()->id())
            ->with('hospital')
            ->latest()
            ->paginate(15);

        return view('parent.requests.index', compact('requests'));
    }

    public function create()
    {
        $this->authorize('create', ParentRequest::class);

        $hospitals = Hospital::where('status', 'active')->get();

        return view('parent.requests.create', compact('hospitals'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ParentRequest::class);

        $validated = $request->validate([
            'type' => 'required|in:vaccination_request,general_inquiry,complaint,feedback',
            'subject' => 'required|string|max:255',
            'details' => 'required|string',
            'hospital_id' => 'nullable|exists:hospitals,id',
        ]);

        $requestModel = ParentRequest::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]));

        return redirect()->route('parent.requests.show', $requestModel)
            ->with('success', 'Request submitted successfully.');
    }

    public function show(ParentRequest $requestModel)
    {
        $this->authorize('view', $requestModel);

        $requestModel->load('hospital');

        return view('parent.requests.show', compact('requestModel'));
    }
}
