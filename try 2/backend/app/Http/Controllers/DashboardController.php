<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return $this->getAdminStats();
        }
        
        return $this->getUserStats($user->id);
    }
    
    /**
     * Get admin dashboard statistics
     */
    private function getAdminStats()
    {
        // Basic counts
        $stats = [
            'total_requests' => Request::count(),
            'pending_requests' => Request::where('status', 'pending')->count(),
            'approved_requests' => Request::where('status', 'approved')->count(),
            'rejected_requests' => Request::where('status', 'rejected')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_value' => Request::where('status', 'approved')->sum('total'),
        ];
        
        // Trend data (last 7 days)
        $trendData = Request::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        // Status distribution
        $statusData = Request::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Recent activity
        $recentActivity = Request::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'user' => $request->user->name,
                    'title' => $request->title,
                    'status' => $request->status,
                    'total' => $request->total,
                    'created_at' => $request->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'stats' => $stats,
            'trend_data' => $trendData,
            'status_data' => $statusData,
            'recent_activity' => $recentActivity,
        ]);
    }
    
    /**
     * Get user dashboard statistics
     */
    private function getUserStats($userId)
    {
        $stats = [
            'total_requests' => Request::where('user_id', $userId)->count(),
            'pending_requests' => Request::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved_requests' => Request::where('user_id', $userId)->where('status', 'approved')->count(),
            'rejected_requests' => Request::where('user_id', $userId)->where('status', 'rejected')->count(),
            'total_value' => Request::where('user_id', $userId)->where('status', 'approved')->sum('total'),
        ];
        
        // User's trend data (last 7 days)
        $trendData = Request::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('user_id', $userId)
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        // User's status distribution
        $statusData = Request::select('status', DB::raw('COUNT(*) as count'))
            ->where('user_id', $userId)
            ->groupBy('status')
            ->get();
        
        // User's recent activity
        $recentActivity = Request::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'title' => $request->title,
                    'status' => $request->status,
                    'total' => $request->total,
                    'created_at' => $request->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'stats' => $stats,
            'trend_data' => $trendData,
            'status_data' => $statusData,
            'recent_activity' => $recentActivity,
        ]);
    }
}
