<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    /**
     * Display all requests (admin view)
     */
    public function index(HttpRequest $request)
    {
        $query = Request::select('id','title','description','quantity','unit_price','total','status','user_id','admin_note','created_at')
            ->with(['user:id,name,email']);
        
        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Filter by user
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
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
     * Approve a request
     */
    public function approve(HttpRequest $request, Request $requestModel)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $requestModel->update([
            'status'     => 'approved',
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        ActivityLog::log('approve', "Request #{$requestModel->id} approved", 'Request', $requestModel->id);

        return response()->json([
            'message' => 'Request approved successfully',
            'request' => $requestModel->fresh()->load('user'),
        ]);
    }

    /**
     * Reject a request
     */
    public function reject(HttpRequest $request, Request $requestModel)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $requestModel->update([
            'status'     => 'rejected',
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        ActivityLog::log('reject', "Request #{$requestModel->id} rejected", 'Request', $requestModel->id);

        return response()->json([
            'message' => 'Request rejected successfully',
            'request' => $requestModel->fresh()->load('user'),
        ]);
    }

    /**
     * Update admin note
     */
    public function updateNote(HttpRequest $request, Request $requestModel)
    {
        $validated = $request->validate([
            'admin_note' => 'required|string',
        ]);

        $requestModel->update([
            'admin_note' => $validated['admin_note'],
        ]);

        return response()->json([
            'message' => 'Note updated successfully',
            'request' => $requestModel->load('user'),
        ]);
    }
}
