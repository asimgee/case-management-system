<?php

namespace App\Http\Controllers;

use App\Models\Case_Model;
use App\Models\Client;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $now = Carbon::now();
        // dd($user);
        if ($user->role_id == 1) {
            return $this->adminDashboard();
        }
        
        return $this->userDashboard($user);
    }

    private function userDashboard($user)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Case Statistics
        $caseStats = [
            'total' => $user->cases()->count(),
            'pending' => $user->cases()->where('case_status', 'pending')->count(),
            'in_hearing' => $user->cases()->where('case_status', 'in_hearing')->count(),
            'closed' => $user->cases()->where('case_status', 'closed')->count(),
            'new_this_month' => $user->cases()->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
        ];
        
        // Recent Cases
        $recentCases = $user->cases()
            ->with(['caseType', 'courtType'])
            ->latest()
            ->take(5)
            ->get();
        
        // Upcoming Hearings
        $upcomingHearings = Hearing::whereHas('case', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('hearing_date', '>=', $now)
            ->with(['case.caseType'])
            ->orderBy('hearing_date')
            ->take(5)
            ->get();
        
        // Document Statistics
        $documentStats = [
            'total' => Document::where('uploaded_by', $user->id)
                ->orWhereHas('case', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
            'recent' => Document::where('uploaded_by', $user->id)
                ->orWhereHas('case', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->latest()
                ->take(5)
                ->get(),
        ];
        
        // Client Statistics
        $clientStats = [
            'total' => Client::where('user_id', $user->id)->count(),
            'new_this_month' => Client::where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
        ];
        
        // Monthly Case Chart Data
        $monthlyCases = $this->getMonthlyCaseData($user);
        
        // Case Status Distribution
        $caseDistribution = $this->getCaseDistribution($user);
        
        // Recent Activity
        $recentActivity = $this->getRecentActivity($user);
        
        // Subscription Info
        $subscriptionInfo = $this->getSubscriptionInfo($user);
        
        return view('dashboard.user', compact(
            'caseStats',
            'recentCases',
            'upcomingHearings',
            'documentStats',
            'clientStats',
            'monthlyCases',
            'caseDistribution',
            'recentActivity',
            'subscriptionInfo'
        ));
    }

    private function adminDashboard()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Overall Statistics
        $stats = [
            'total_users' => User::count(),
            'new_users_this_month' => User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'total_cases' => Case_Model::count(),
            'new_cases_this_month' => Case_Model::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'active_cases' => Case_Model::whereIn('case_status', ['pending', 'in_hearing'])->count(),
            'total_documents' => Document::count(),
            'total_clients' => Client::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];
        
        // Recent Users
        $recentUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();
        
        // Recent Cases
        $recentCases = Case_Model::with(['user', 'caseType'])
            ->latest()
            ->take(5)
            ->get();
        
        // Case Status Distribution
        $caseDistribution = Case_Model::select('case_status', DB::raw('count(*) as count'))
            ->groupBy('case_status')
            ->pluck('count', 'case_status')
            ->toArray();
        
        // Monthly Case Chart Data
        $monthlyCases = $this->getMonthlyCaseData();
        
        // User Growth Chart Data
        $userGrowth = $this->getUserGrowthData();
        
        // Revenue Statistics (if you have payment system)
        $revenueStats = $this->getRevenueStats();
        
        // System Health
        $systemHealth = $this->getSystemHealth();
        
        // Recent Activity Log
        $recentActivity = $this->getAdminRecentActivity();
        
        return view('dashboard.admin', compact(
            'stats',
            'recentUsers',
            'recentCases',
            'caseDistribution',
            'monthlyCases',
            'userGrowth',
            'revenueStats',
            'systemHealth',
            'recentActivity'
        ));
    }

    public function adminDashboardView()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return $this->adminDashboard();
    }

    private function getMonthlyCaseData($user = null)
    {
        $now = Carbon::now();
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $query = Case_Model::whereBetween('created_at', [$start, $end]);
            
            if ($user) {
                $query->where('user_id', $user->id);
            }
            
            $data[] = $query->count();
        }
        
        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    private function getCaseDistribution($user = null)
    {
        $query = Case_Model::select('case_status', DB::raw('count(*) as count'))
            ->groupBy('case_status');
            
        if ($user) {
            $query->where('user_id', $user->id);
        }
        
        $distribution = $query->pluck('count', 'case_status')->toArray();
        
        $colors = [
            'pending' => '#f59e0b',
            'in_hearing' => '#3b82f6',
            'closed' => '#6b7280',
        ];
        
        $labels = [];
        $data = [];
        $backgroundColors = [];
        
        foreach ($distribution as $status => $count) {
            $labels[] = ucfirst(str_replace('_', ' ', $status));
            $data[] = $count;
            $backgroundColors[] = $colors[$status] ?? '#9ca3af';
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $backgroundColors,
        ];
    }

    private function getUserGrowthData()
    {
        $now = Carbon::now();
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $data[] = User::whereBetween('created_at', [$start, $end])->count();
        }
        
        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    private function getRecentActivity($user)
    {
        $activities = [];
        
        // Recent cases
        $recentCases = $user->cases()
            ->with(['caseType'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($case) {
                return [
                    'type' => 'case',
                    'title' => 'New case created',
                    'description' => $case->case_title,
                    'time' => $case->created_at->diffForHumans(),
                    'icon' => 'fas fa-gavel',
                    'color' => 'blue',
                ];
            });
        
        // Recent documents
        $recentDocuments = Document::where('uploaded_by', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function($document) {
                return [
                    'type' => 'document',
                    'title' => 'Document uploaded',
                    'description' => $document->name,
                    'time' => $document->created_at->diffForHumans(),
                    'icon' => 'fas fa-file',
                    'color' => 'green',
                ];
            });
        
        // Recent hearings
        $recentHearings = Hearing::whereHas('case', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['case'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($hearing) {
                return [
                    'type' => 'hearing',
                    'title' => 'Hearing scheduled',
                    'description' => $hearing->case->case_title,
                    'time' => $hearing->created_at->diffForHumans(),
                    'icon' => 'fas fa-calendar',
                    'color' => 'purple',
                ];
            });
        
        $activities = $recentCases->merge($recentDocuments)->merge($recentHearings);
        $activities = $activities->sortByDesc(function($activity) {
            return $activity['time'];
        })->take(5)->values();
        
        return $activities;
    }

    private function getAdminRecentActivity()
    {
        $activities = [];
        
        // Recent users
        $recentUsers = User::latest()
            ->take(3)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'user',
                    'title' => 'New user registered',
                    'description' => $user->name . ' (' . $user->email . ')',
                    'time' => $user->created_at->diffForHumans(),
                    'icon' => 'fas fa-user',
                    'color' => 'blue',
                ];
            });
        
        // Recent cases
        $recentCases = Case_Model::with(['user'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($case) {
                return [
                    'type' => 'case',
                    'title' => 'New case created',
                    'description' => $case->case_title . ' by ' . $case->user->name,
                    'time' => $case->created_at->diffForHumans(),
                    'icon' => 'fas fa-gavel',
                    'color' => 'green',
                ];
            });
        
        // Recent subscriptions
        $recentSubscriptions = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($subscription) {
                return [
                    'type' => 'subscription',
                    'title' => 'New subscription',
                    'description' => $subscription->user->name . ' subscribed to ' . $subscription->plan->name,
                    'time' => $subscription->created_at->diffForHumans(),
                    'icon' => 'fas fa-credit-card',
                    'color' => 'purple',
                ];
            });
        
        $activities = $recentUsers->merge($recentCases)->merge($recentSubscriptions);
        $activities = $activities->sortByDesc(function($activity) {
            return $activity['time'];
        })->take(5)->values();
        
        return $activities;
    }

    private function getSubscriptionInfo($user)
    {
        if (!$user->currentPlan) {
            return null;
        }
        
        $activeSubscription = $user->activeSubscription;
        
        if (!$activeSubscription) {
            return [
                'plan_name' => $user->currentPlan->name,
                'status' => 'inactive',
                'remaining_cases' => $user->getRemainingCases(),
                'expires_at' => null,
                'is_trial' => false,
            ];
        }
        
        return [
            'plan_name' => $user->currentPlan->name,
            'status' => $activeSubscription->status,
            'remaining_cases' => $user->getRemainingCases(),
            'expires_at' => $activeSubscription->ends_at ? $activeSubscription->ends_at->format('M d, Y') : 'Never',
            'is_trial' => $activeSubscription->isOnTrial(),
            'trial_ends_at' => $activeSubscription->trial_ends_at ? $activeSubscription->trial_ends_at->format('M d, Y') : null,
        ];
    }

    private function getRevenueStats()
    {
        // This is a placeholder - implement based on your payment system
        return [
            'total_revenue' => 0,
            'monthly_revenue' => 0,
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'renewals_this_month' => 0,
        ];
    }

    private function getSystemHealth()
    {
        $storagePath = storage_path();
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        
        $storagePercentage = ($usedSpace / $totalSpace) * 100;
        
        return [
            'storage' => [
                'total' => $this->formatBytes($totalSpace),
                'used' => $this->formatBytes($usedSpace),
                'free' => $this->formatBytes($freeSpace),
                'percentage' => round($storagePercentage, 2),
                'status' => $storagePercentage > 90 ? 'critical' : ($storagePercentage > 75 ? 'warning' : 'healthy'),
            ],
            'database' => [
                'size' => $this->getDatabaseSize(),
                'tables' => $this->getTableCount(),
                'status' => 'healthy',
            ],
            'application' => [
                'version' => config('app.version', '1.0.0'),
                'environment' => config('app.env'),
                'debug_mode' => config('app.debug'),
                'status' => config('app.debug') ? 'debug' : 'production',
            ],
        ];
    }

    private function getDatabaseSize()
    {
        try {
            $databaseName = config('database.connections.mysql.database');
            $size = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb FROM information_schema.TABLES WHERE table_schema = ?", [$databaseName]);
            
            return isset($size[0]->size_mb) ? $size[0]->size_mb . ' MB' : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getTableCount()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            return count($tables);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // Other dashboard methods
    public function cases()
    {
        return redirect()->route('cases.index');
    }

    public function clients()
    {
        return redirect()->route('clients.index');
    }

    public function hearings()
    {
        return redirect()->route('hearings.index');
    }

    public function documents()
    {
        return redirect()->route('documents.index');
    }

    public function reports()
    {
        return redirect()->route('reports.index');
    }

    public function settings()
    {
        return view('settings.index');
    }

    public function adminSettings()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('admin.settings');
    }

    public function updateGeneralSettings(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement settings update logic
        return back()->with('success', 'General settings updated successfully.');
    }

    public function updateEmailSettings(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement email settings update logic
        return back()->with('success', 'Email settings updated successfully.');
    }

    public function updateSecuritySettings(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement security settings update logic
        return back()->with('success', 'Security settings updated successfully.');
    }

    public function auditLogs()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement audit logs
        return view('admin.audit-logs');
    }

    public function showAuditLog($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement single audit log view
        return view('admin.audit-log-show');
    }

    public function backup()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement backup management
        return view('admin.backup');
    }

    public function createBackup(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement backup creation
        return back()->with('success', 'Backup created successfully.');
    }

    public function restoreBackup(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement backup restoration
        return back()->with('success', 'Backup restored successfully.');
    }

    public function deleteBackup($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Implement backup deletion
        return back()->with('success', 'Backup deleted successfully.');
    }

    public function systemHealth()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $systemHealth = $this->getSystemHealth();
        return view('admin.system-health', compact('systemHealth'));
    }
}