<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $allowedModules = ActivityLog::getAllowedModulesForUser($user);

        $query = ActivityLog::with('user')->latest();

        // Terapkan pembatasan modul jika bukan Superadmin / Top Management
        if ($allowedModules !== null) {
            $query->whereIn('module', $allowedModules);
        }

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
            $reqModule = $request->module;
            // Jika modul yang diminta tidak termasuk modul yang diizinkan untuk user ini, paksa kosong
            if ($allowedModules !== null && !in_array($reqModule, $allowedModules)) {
                $query->whereRaw('0 = 1');
            } else {
                $query->where('module', $reqModule);
            }
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

        // Available Modules & Actions for Filter Dropdowns (Hanya modul yang diizinkan)
        $modulesQuery = ActivityLog::select('module')->distinct()->whereNotNull('module');
        if ($allowedModules !== null) {
            $modulesQuery->whereIn('module', $allowedModules);
        }
        $modules = $modulesQuery->pluck('module');

        $actionsQuery = ActivityLog::select('action')->distinct()->whereNotNull('action');
        if ($allowedModules !== null) {
            $actionsQuery->whereIn('module', $allowedModules);
        }
        $actions = $actionsQuery->pluck('action');

        // Statistics Summary (Hanya dihitung dari modul yang diizinkan)
        $statsBase = ActivityLog::query();
        if ($allowedModules !== null) {
            $statsBase->whereIn('module', $allowedModules);
        }

        $stats = [
            'total_today' => (clone $statsBase)->whereDate('created_at', today())->count(),
            'total_tambah' => (clone $statsBase)->where('action', 'Tambah')->count(),
            'total_edit' => (clone $statsBase)->where('action', 'Edit')->count(),
            'total_hapus' => (clone $statsBase)->where('action', 'Hapus')->count(),
            'total_import' => (clone $statsBase)->where('action', 'Import')->count(),
        ];

        return view('Administrasi.ActivityLog.index', compact('activityLogs', 'modules', 'actions', 'stats'));
    }

    public function show($id)
    {
        $log = ActivityLog::findOrFail($id);

        $allowedModules = ActivityLog::getAllowedModulesForUser(Auth::user());
        if ($allowedModules !== null && !in_array($log->module, $allowedModules)) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat log aktivitas modul ini.');
        }

        return response()->json([
            'status' => 'success',
            'data' => $log,
        ]);
    }
}
