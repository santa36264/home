<?php

namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    /**
     * Display a listing of the user's requests
     */
    public function index(HttpRequest $request)
    {
        $query = Request::select('id','title','description','quantity','unit_price','total','status','user_id','admin_note','created_at')
            ->where('user_id', $request->user()->id)
            ->with(['user:id,name,email']);
        
        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        // Paginate
        $perPage = $request->input('per_page', 15);
        $requests = $query->paginate($perPage);

        return response()->json($requests);
    }

    /**
     * Store a newly created request
     */
    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $requestModel = Request::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return response()->json($requestModel, 201);
    }

    /**
     * Display the specified request
     */
    public function show(HttpRequest $request, Request $requestModel)
    {
        // Ensure user can only view their own requests
        if ($requestModel->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($requestModel->load('user'));
    }

    /**
     * Update the specified request (only if pending)
     */
    public function update(HttpRequest $request, Request $requestModel)
    {
        // Ensure user can only update their own requests
        if ($requestModel->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only pending requests can be updated
        if ($requestModel->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending requests can be updated'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
        ]);

        $requestModel->update($validated);

        return response()->json($requestModel);
    }

    /**
     * Remove the specified request (only if pending)
     */
    public function destroy(HttpRequest $request, Request $requestModel)
    {
        // Ensure user can only delete their own requests
        if ($requestModel->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only pending requests can be deleted
        if ($requestModel->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending requests can be deleted'
            ], 403);
        }

        $requestModel->delete();

        return response()->json(['message' => 'Request deleted successfully']);
    }
}
