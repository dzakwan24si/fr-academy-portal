<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Action Filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Module Filter
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Date Range Filter
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $activityLogs = $query->paginate($perPage)->withQueryString();

        // Available Modules & Actions for Filter Dropdowns
        $modules = ActivityLog::select('module')->distinct()->whereNotNull('module')->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->whereNotNull('action')->pluck('action');

        // Statistics Summary
        $stats = [
            'total_today'  => ActivityLog::whereDate('created_at', today())->count(),
            'total_tambah' => ActivityLog::where('action', 'Tambah')->count(),
            'total_edit'   => ActivityLog::where('action', 'Edit')->count(),
            'total_hapus'  => ActivityLog::where('action', 'Hapus')->count(),
            'total_import' => ActivityLog::where('action', 'Import')->count(),
        ];

        return view('Administrasi.ActivityLog.index', compact('activityLogs', 'modules', 'actions', 'stats'));
    }

    public function show($id)
    {
        $log = ActivityLog::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $log,
        ]);
    }
}
