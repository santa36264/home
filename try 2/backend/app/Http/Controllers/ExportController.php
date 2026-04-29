<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export requests to CSV
     */
    public function exportRequests(HttpRequest $request)
    {
        $user = $request->user();
        
        // Get requests based on user role
        if ($user->role === 'admin') {
            $requests = Request::with('user')->get();
        } else {
            $requests = Request::where('user_id', $user->id)->with('user')->get();
        }
        
        // Create CSV content
        $csvData = $this->generateRequestsCsv($requests);
        
        // Generate filename with timestamp
        $filename = 'requests_' . date('Y-m-d_His') . '.csv';
        
        // Return CSV response
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
    
    /**
     * Export users to CSV (admin only)
     */
    public function exportUsers(HttpRequest $request)
    {
        $users = User::all();
        
        // Create CSV content
        $csvData = $this->generateUsersCsv($users);
        
        // Generate filename with timestamp
        $filename = 'users_' . date('Y-m-d_His') . '.csv';
        
        // Return CSV response
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
    
    /**
     * Generate CSV content for requests
     */
    private function generateRequestsCsv($requests)
    {
        $output = fopen('php://temp', 'r+');
        
        // Add CSV headers
        fputcsv($output, [
            'ID',
            'User Name',
            'User Email',
            'Title',
            'Description',
            'Quantity',
            'Unit Price',
            'Total',
            'Status',
            'Admin Note',
            'Created At',
            'Updated At'
        ]);
        
        // Add data rows
        foreach ($requests as $request) {
            fputcsv($output, [
                $request->id,
                $request->user->name ?? 'N/A',
                $request->user->email ?? 'N/A',
                $request->title,
                $request->description,
                $request->quantity,
                $request->unit_price,
                $request->total,
                $request->status,
                $request->admin_note ?? '',
                $request->created_at->format('Y-m-d H:i:s'),
                $request->updated_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);
        
        return $csvData;
    }
    
    /**
     * Generate CSV content for users
     */
    private function generateUsersCsv($users)
    {
        $output = fopen('php://temp', 'r+');
        
        // Add CSV headers
        fputcsv($output, [
            'ID',
            'Name',
            'Email',
            'Role',
            'Created By Admin',
            'Created At',
            'Updated At'
        ]);
        
        // Add data rows
        foreach ($users as $user) {
            fputcsv($output, [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->created_by_admin ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s'),
                $user->updated_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);
        
        return $csvData;
    }
    
    /**
     * Export requests summary (statistics)
     */
    public function exportRequestsSummary(HttpRequest $request)
    {
        $user = $request->user();
        
        // Get statistics
        if ($user->role === 'admin') {
            $stats = [
                'Total Requests' => Request::count(),
                'Pending Requests' => Request::where('status', 'pending')->count(),
                'Approved Requests' => Request::where('status', 'approved')->count(),
                'Rejected Requests' => Request::where('status', 'rejected')->count(),
                'Total Value (Approved)' => '$' . number_format(Request::where('status', 'approved')->sum('total'), 2),
                'Total Users' => User::where('role', 'user')->count(),
            ];
        } else {
            $stats = [
                'Total Requests' => Request::where('user_id', $user->id)->count(),
                'Pending Requests' => Request::where('user_id', $user->id)->where('status', 'pending')->count(),
                'Approved Requests' => Request::where('user_id', $user->id)->where('status', 'approved')->count(),
                'Rejected Requests' => Request::where('user_id', $user->id)->where('status', 'rejected')->count(),
                'Total Value (Approved)' => '$' . number_format(Request::where('user_id', $user->id)->where('status', 'approved')->sum('total'), 2),
            ];
        }
        
        // Create CSV content
        $output = fopen('php://temp', 'r+');
        
        fputcsv($output, ['Summary Report']);
        fputcsv($output, ['Generated At', date('Y-m-d H:i:s')]);
        fputcsv($output, ['Generated By', $user->name]);
        fputcsv($output, []);
        fputcsv($output, ['Metric', 'Value']);
        
        foreach ($stats as $key => $value) {
            fputcsv($output, [$key, $value]);
        }
        
        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);
        
        $filename = 'summary_' . date('Y-m-d_His') . '.csv';
        
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
