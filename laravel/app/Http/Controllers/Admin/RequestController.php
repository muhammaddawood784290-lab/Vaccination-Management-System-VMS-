<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ParentRequest::class);

        $query = ParentRequest::with(['user', 'hospital'])->latest();
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $requests = $query->paginate(15)->withQueryString();
        $stats = [
            'pending' => ParentRequest::where('status', 'pending')->count(),
            'in_review' => ParentRequest::where('status', 'in_review')->count(),
            'resolved' => ParentRequest::where('status', 'resolved')->count(),
            'closed' => ParentRequest::where('status', 'closed')->count(),
        ];
        return view('admin.requests.index', compact('requests', 'stats'));
    }

    public function show(ParentRequest $request_model)
    {
        $this->authorize('view', $request_model);

        $request_model->load(['user', 'hospital']);
        return view('admin.requests.show', ['request' => $request_model]);
    }

    public function review(ParentRequest $request_model)
    {
        $this->authorize('update', $request_model);

        if ($request_model->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be reviewed.');
        }

        $request_model->update(['status' => 'in_review']);
        return back()->with('success', 'Request is now under review.');
    }

    public function resolve(Request $httpRequest, ParentRequest $request_model)
    {
        $this->authorize('update', $request_model);

        if ($request_model->status !== 'in_review') {
            return back()->with('error', 'Only requests under review can be resolved.');
        }

        $validated = $httpRequest->validate(['admin_response' => 'required|string|min:3']);
        $request_model->update([
            'status' => 'resolved',
            'admin_response' => $validated['admin_response'],
            'responded_at' => now(),
        ]);
        return back()->with('success', 'Request resolved.');
    }

    public function close(ParentRequest $request_model)
    {
        $this->authorize('update', $request_model);

        if ($request_model->status !== 'in_review') {
            return back()->with('error', 'Only requests under review can be closed.');
        }

        $request_model->update(['status' => 'closed']);
        return back()->with('success', 'Request closed.');
    }
}
