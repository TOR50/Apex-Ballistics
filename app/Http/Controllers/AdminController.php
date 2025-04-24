<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Services\IntegrityService;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::with('role')->paginate(15);
        $roles = Role::all();
        
        return view('admin.users', [
            'users' => $users,
            'roles' => $roles
        ]);
    }
    
    public function updateRole($id, Request $request)
    {
        $user = User::findOrFail($id);
        $role = Role::findOrFail($request->role_id);
        
        // Require re-authentication for role changes
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Password confirmation failed.');
        }
        
        $user->role_id = $role->id;
        $user->save();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_user_role',
            'description' => 'Updated role for user ' . $user->name . ' to ' . $role->name,
        ]);
        
        return back()->with('success', 'User role updated successfully.');
    }
    
    public function deleteUser($id, Request $request)
    {
        $user = User::findOrFail($id);
        
        // GDPR compliant anonymization
        $user->name = 'Anonymized User';
        $user->email = 'anonymized_' . $user->id . '@example.com';
        $user->password = Hash::make(str_random(32));
        $user->is_active = false;
        $user->anonymized_at = now();
        $user->save();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'anonymize_user',
            'description' => 'Anonymized user #' . $user->id . ' per GDPR requirements',
        ]);
        
        return back()->with('success', 'User has been anonymized per GDPR requirements.');
    }
    
    public function auditLogs(Request $request)
    {
        $query = ActivityLog::with(['user', 'case']);
        
        // Apply filters if present
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        
        return view('admin.audit', [
            'logs' => $logs,
            'filters' => $request->all()
        ]);
    }
    
    public function exportAuditLogs(Request $request)
    {
        // Logic to export audit logs
        $logs = ActivityLog::with(['user', 'case'])
                ->orderBy('created_at', 'desc')
                ->get();
        
        // Format for chain of custody
        $exportData = $logs->map(function ($log) {
            return [
                'timestamp' => $log->created_at->toIso8601String(),
                'user' => $log->user->name,
                'action' => $log->action,
                'description' => $log->description,
                'case' => $log->case ? $log->case->case_number : null,
                'ip_address' => $log->ip_address,
                'hash' => $log->integrity_hash,
            ];
        });
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_audit_logs',
            'description' => 'Exported audit logs in Chain of Custody format',
        ]);
        
        return response()->json($exportData)
            ->header('Content-Disposition', 'attachment; filename="audit-logs.json"')
            ->header('Content-Type', 'application/json');
    }
    
    public function verifyIntegrity(Request $request)
    {
        $integrityService = new IntegrityService();
        $results = $integrityService->verifyLogIntegrity();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'verify_integrity',
            'description' => 'Ran integrity verification on audit logs',
        ]);
        
        return response()->json([
            'status' => $results['passed'] ? 'success' : 'failure',
            'message' => $results['message'],
            'details' => $results['details'],
        ]);
    }
}
