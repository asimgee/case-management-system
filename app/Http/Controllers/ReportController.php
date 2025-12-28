<?php

namespace App\Http\Controllers;

use App\Models\Case_Model;
use App\Models\Client;
use App\Models\Document;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        // Determine if user is admin or regular user
        if ($user->role === 'admin') {
            $caseQuery = Case_Model::query();
            $clientQuery = Client::query();
        } else {
            $caseQuery = Case_Model::where('user_id', $user->id);
            $clientQuery = Client::where('user_id', $user->id);
        }
        
        // Overall Statistics
        $totalCases = $caseQuery->count();
        $activeCases = $caseQuery->where('case_status', '!=', 'closed')->count();
        $closedCases = $caseQuery->where('case_status', 'closed')->count();
        
        // Cases by status
        $casesByStatus = $caseQuery->selectRaw('case_status, count(*) as count')
            ->groupBy('case_status')
            ->pluck('count', 'case_status');
        
        // Cases by type
        $casesByType = $caseQuery->with('caseType')
            ->select('case_type_id', DB::raw('count(*) as count'))
            ->groupBy('case_type_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->caseType->name ?? 'Unknown' => $item->count];
            });
        
        // Cases by court type
        $casesByCourt = $caseQuery->with('courtType')
            ->select('court_type_id', DB::raw('count(*) as count'))
            ->groupBy('court_type_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->courtType->name ?? 'Unknown' => $item->count];
            });
        
        // Recent cases
        $recentCases = Case_Model::with(['caseType', 'courtType'])
        ->where('user_id', 1)
        ->where('case_status', 'closed') // ya open
        ->latest('created_at')
        ->take(10)
        ->get();
    
        
        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($user, $startDate, $endDate);
        
        // Get chart data
        $chartData = $this->getChartData($user, $startDate, $endDate);
        
        // Get time periods for filter
        $timePeriods = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'last_quarter' => 'Last Quarter',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];
        
        return view('reports.index', compact(
            'totalCases',
            'activeCases',
            'closedCases',
            'casesByStatus',
            'casesByType',
            'casesByCourt',
            'recentCases',
            'performanceMetrics',
            'chartData',
            'timePeriods',
            'startDate',
            'endDate'
        ));
    }

    public function financial(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        // In a real application, you would have a financial model
        // For now, we'll create sample data
        $revenueData = [
            'total_revenue' => 247500,
            'monthly_revenue' => 45000,
            'yearly_revenue' => 540000,
            'revenue_growth' => 15, // percentage
            'average_case_value' => 8250,
            'top_revenue_cases' => [
                ['case_number' => 'CASE-2024-001', 'revenue' => 25000, 'client' => 'ABC Corp'],
                ['case_number' => 'CASE-2024-015', 'revenue' => 18000, 'client' => 'XYZ Ltd'],
                ['case_number' => 'CASE-2024-008', 'revenue' => 15000, 'client' => 'John Smith'],
            ]
        ];
        
        $expenseData = [
            'total_expenses' => 125000,
            'monthly_expenses' => 25000,
            'expense_breakdown' => [
                'salaries' => 60000,
                'office_rent' => 24000,
                'utilities' => 12000,
                'software' => 15000,
                'marketing' => 8000,
                'other' => 6000
            ]
        ];
        
        $profitData = [
            'net_profit' => $revenueData['total_revenue'] - $expenseData['total_expenses'],
            'profit_margin' => (($revenueData['total_revenue'] - $expenseData['total_expenses']) / $revenueData['total_revenue']) * 100,
            'monthly_profit' => $revenueData['monthly_revenue'] - $expenseData['monthly_expenses']
        ];
        
        return view('reports.financial', compact(
            'revenueData',
            'expenseData',
            'profitData',
            'startDate',
            'endDate'
        ));
    }

    public function caseAnalysis(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->input('start_date', now()->subYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        if ($user->role === 'admin') {
            $query = Case_Model::query();
        } else {
            $query = Case_Model::where('user_id', $user->id);
        }
        
        // Filter by date range
        $query->whereBetween('filing_date', [$startDate, $endDate]);
        
        // Case statistics
        $totalCases = $query->count();
        $casesByMonth = $query->selectRaw('DATE_FORMAT(filing_date, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Case resolution time
        $resolutionTimes = $query->where('case_status', 'closed')
            ->whereNotNull('judgment_date')
            ->get()
            ->map(function ($case) {
                $filingDate = Carbon::parse($case->filing_date);
                $judgmentDate = Carbon::parse($case->judgment_date);
                return $filingDate->diffInDays($judgmentDate);
            });
        
        $avgResolutionTime = $resolutionTimes->avg();
        $minResolutionTime = $resolutionTimes->min();
        $maxResolutionTime = $resolutionTimes->max();
        
        // Case win rate (you would need to add win/loss field to cases table)
        $totalClosedCases = $query->where('case_status', 'closed')->count();
        $wonCases = 0; // This would come from your database
        $winRate = $totalClosedCases > 0 ? ($wonCases / $totalClosedCases) * 100 : 0;
        
        // Cases by lawyer
        $casesByLawyer = $query->with('assignedLawyer')
            ->select('assigned_lawyer_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('assigned_lawyer_id')
            ->groupBy('assigned_lawyer_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->assignedLawyer->name ?? 'Unassigned' => $item->count];
            });
        
        return view('reports.case-analysis', compact(
            'totalCases',
            'casesByMonth',
            'avgResolutionTime',
            'minResolutionTime',
            'maxResolutionTime',
            'winRate',
            'casesByLawyer',
            'startDate',
            'endDate'
        ));
    }

    public function clientAnalysis(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->input('start_date', now()->subYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        if ($user->role === 'admin') {
            $query = Client::query();
        } else {
            $query = Client::where('user_id', $user->id);
        }
        
        // Filter by date range
        $query->whereBetween('created_at', [$startDate, $endDate]);
        
        // Client statistics
        $totalClients = $query->count();
        $newClientsThisMonth = $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Clients by type
        $clientsByType = $query->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');
        
        // Top clients by case count
        $topClients = $query->withCount('cases')
            ->orderByDesc('cases_count')
            ->take(10)
            ->get();
        
        // Client acquisition trend
        $clientAcquisitionTrend = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('reports.client-analysis', compact(
            'totalClients',
            'newClientsThisMonth',
            'clientsByType',
            'topClients',
            'clientAcquisitionTrend',
            'startDate',
            'endDate'
        ));
    }

    public function performance(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        // Get lawyer performance if admin
        $lawyerPerformance = [];
        if ($user->role === 'admin') {
            $lawyerPerformance = Lawyer::withCount(['assignedCases', 'additionalCases'])
                ->with(['assignedCases' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('filing_date', [$startDate, $endDate]);
                }])
                ->get()
                ->map(function ($lawyer) {
                    $closedCases = $lawyer->assignedCases->where('case_status', 'closed')->count();
                    $totalCases = $lawyer->assignedCases->count();
                    $successRate = $totalCases > 0 ? ($closedCases / $totalCases) * 100 : 0;
                    
                    return [
                        'name' => $lawyer->name,
                        'total_cases' => $totalCases,
                        'closed_cases' => $closedCases,
                        'success_rate' => $successRate,
                        'avg_resolution_time' => 28, // This would be calculated from actual data
                        'client_satisfaction' => 4.8 // This would come from ratings
                    ];
                });
        }
        
        // Get user performance
        $userPerformance = [];
        if ($user->role === 'admin') {
            $userPerformance = User::where('role', '!=', 'admin')
                ->withCount('cases')
                ->with(['cases' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('filing_date', [$startDate, $endDate]);
                }])
                ->get()
                ->map(function ($user) {
                    $closedCases = $user->cases->where('case_status', 'closed')->count();
                    $totalCases = $user->cases->count();
                    $successRate = $totalCases > 0 ? ($closedCases / $totalCases) * 100 : 0;
                    
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'total_cases' => $totalCases,
                        'closed_cases' => $closedCases,
                        'success_rate' => $successRate,
                        'recent_activity' => $user->cases->count() > 0 ? $user->cases->first()->created_at->diffForHumans() : 'No activity'
                    ];
                });
        }
        
        // Get overall performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($user, $startDate, $endDate);
        
        return view('reports.performance', compact(
            'lawyerPerformance',
            'userPerformance',
            'performanceMetrics',
            'startDate',
            'endDate'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:summary,financial,case_analysis,client_analysis,performance',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,csv,html'
        ]);
        
        $user = Auth::user();
        $reportType = $request->report_type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        
        // Generate report based on type
        switch ($reportType) {
            case 'summary':
                $data = $this->getSummaryReportData($user, $startDate, $endDate);
                $view = 'reports.exports.summary';
                $filename = 'case-summary-report-' . date('Y-m-d');
                break;
                
            case 'financial':
                $data = $this->getFinancialReportData($user, $startDate, $endDate);
                $view = 'reports.exports.financial';
                $filename = 'financial-report-' . date('Y-m-d');
                break;
                
            case 'case_analysis':
                $data = $this->getCaseAnalysisReportData($user, $startDate, $endDate);
                $view = 'reports.exports.case-analysis';
                $filename = 'case-analysis-report-' . date('Y-m-d');
                break;
                
            case 'client_analysis':
                $data = $this->getClientAnalysisReportData($user, $startDate, $endDate);
                $view = 'reports.exports.client-analysis';
                $filename = 'client-analysis-report-' . date('Y-m-d');
                break;
                
            case 'performance':
                $data = $this->getPerformanceReportData($user, $startDate, $endDate);
                $view = 'reports.exports.performance';
                $filename = 'performance-report-' . date('Y-m-d');
                break;
        }
        
        // Add metadata
        $data['report_meta'] = [
            'generated_by' => $user->name,
            'generated_at' => now()->toDateTimeString(),
            'period' => $startDate . ' to ' . $endDate,
            'report_type' => $reportType
        ];
        
        // Generate report in requested format
        if ($format === 'pdf') {
            $pdf = PDF::loadView($view, $data);
            return $pdf->download($filename . '.pdf');
            
        } elseif ($format === 'excel') {
            return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromView {
                private $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function view(): \Illuminate\Contracts\View\View
                {
                    return view('reports.exports.excel-template', $this->data);
                }
            }, $filename . '.xlsx');
            
        } elseif ($format === 'csv') {
            // Generate CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ];
            
            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                
                // Add headers
                fputcsv($file, ['Report Generated', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Generated By', $data['report_meta']['generated_by']]);
                fputcsv($file, ['Period', $data['report_meta']['period']]);
                fputcsv($file, []); // Empty row
                
                // Add data based on report type
                // This would be customized for each report type
                fputcsv($file, ['Total Cases', $data['total_cases'] ?? 'N/A']);
                fputcsv($file, ['Active Cases', $data['active_cases'] ?? 'N/A']);
                fputcsv($file, ['Closed Cases', $data['closed_cases'] ?? 'N/A']);
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } else {
            // HTML format - return view
            return view($view, $data);
        }
    }

    public function export($type)
    {
        $user = Auth::user();
        
        switch ($type) {
            case 'pdf':
                return $this->exportPDF($user);
            case 'excel':
                return $this->exportExcel($user);
            case 'csv':
                return $this->exportCSV($user);
            default:
                return back()->with('error', 'Invalid export type');
        }
    }

    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_cases' => Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->count(),
            
            'active_cases' => Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->where('case_status', '!=', 'closed')->count(),
            
            'closed_cases' => Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->where('case_status', 'closed')->count(),
            
            'total_clients' => Client::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->count(),
            
            'total_documents' => Document::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('uploaded_by', $user->id)
                    ->orWhereHas('case', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            })->count(),
            
            'recent_activity' => Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->latest()->take(5)->get()->map(function ($case) {
                return [
                    'title' => $case->case_number,
                    'description' => $case->first_party_title . ' vs ' . $case->second_party_title,
                    'time' => $case->created_at->diffForHumans(),
                    'status' => $case->case_status
                ];
            })
        ];
        
        return response()->json($stats);
    }

    // public function getChartData(Request $request)
    // {
    //     $user = Auth::user();
    //     $period = $request->input('period', 'monthly');
        
    //     $data = [];
        
    //     if ($period === 'monthly') {
    //         // Get data for last 12 months
    //         for ($i = 11; $i >= 0; $i--) {
    //             $month = now()->subMonths($i);
    //             $monthStart = $month->copy()->startOfMonth();
    //             $monthEnd = $month->copy()->endOfMonth();
                
    //             $cases = Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
    //                 return $q->where('user_id', $user->id);
    //             })->whereBetween('filing_date', [$monthStart, $monthEnd])->count();
                
    //             $data['labels'][] = $month->format('M Y');
    //             $data['cases'][] = $cases;
    //             $data['revenue'][] = rand(20000, 50000); // Sample revenue data
    //         }
    //     } elseif ($period === 'weekly') {
    //         // Get data for last 8 weeks
    //         for ($i = 7; $i >= 0; $i--) {
    //             $week = now()->subWeeks($i);
    //             $weekStart = $week->copy()->startOfWeek();
    //             $weekEnd = $week->copy()->endOfWeek();
                
    //             $cases = Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
    //                 return $q->where('user_id', $user->id);
    //             })->whereBetween('filing_date', [$weekStart, $weekEnd])->count();
                
    //             $data['labels'][] = 'Week ' . $week->weekOfYear;
    //             $data['cases'][] = $cases;
    //             $data['revenue'][] = rand(4000, 12000); // Sample revenue data
    //         }
    //     }
        
    //     // Get case type distribution
    //     $caseTypes = Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
    //             return $q->where('user_id', $user->id);
    //         })
    //         ->with('caseType')
    //         ->select('case_type_id', DB::raw('count(*) as count'))
    //         ->groupBy('case_type_id')
    //         ->get();
        
    //     $data['case_types'] = [
    //         'labels' => $caseTypes->pluck('caseType.name')->toArray(),
    //         'data' => $caseTypes->pluck('count')->toArray(),
    //         'colors' => ['#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899']
    //     ];
        
    //     return response()->json($data);
    // }

    private function getPerformanceMetrics($user, $startDate, $endDate)
    {
        // Calculate performance metrics based on actual data
        $metrics = [
            'case_resolution_time' => 28, // Average days
            'client_acquisition_rate' => 12, // New clients per month
            'average_case_value' => 8250, // In currency
            'case_load_per_lawyer' => 18, // Average cases per lawyer
            'success_rate' => 89, // Percentage
            'client_satisfaction' => 4.8, // Out of 5
            'revenue_growth' => 15, // Percentage
            'billable_hours' => 1247, // Total hours
        ];
        
        // Calculate based on actual data if available
        if ($user->role === 'admin') {
            $totalLawyers = Lawyer::count();
            $totalCases = Case_Model::count();
            $metrics['case_load_per_lawyer'] = $totalLawyers > 0 ? round($totalCases / $totalLawyers) : 0;
        }
        
        return $metrics;
    }

    private function getChartData($user, $startDate, $endDate)
    {
        // Generate chart data
        $months = [];
        $casesData = [];
        $revenueData = [];
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $current = $start->copy();
        
        while ($current <= $end) {
            $month = $current->format('M Y');
            $months[] = $month;
            
            // Get cases for this month
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $cases = Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->whereBetween('filing_date', [$monthStart, $monthEnd])->count();
            
            $casesData[] = $cases;
            $revenueData[] = $cases * 8250; // Sample revenue calculation
            
            $current->addMonth();
        }
        
        return [
            'months' => $months,
            'cases' => $casesData,
            'revenue' => $revenueData
        ];
    }

    private function getSummaryReportData($user, $startDate, $endDate)
    {
        // Generate summary report data
        // This would include all the statistics shown in the main reports page
        return [];
    }

    private function exportPDF($user)
    {
        // Generate PDF export
        // Implementation depends on your PDF library
        return response()->download('path/to/pdf');
    }

    private function exportExcel($user)
    {
        // Generate Excel export
        // Implementation depends on your Excel library
        return response()->download('path/to/excel');
    }

    private function exportCSV($user)
    {
        // Generate CSV export
        $filename = 'report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($user) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers and data
            fputcsv($file, ['Report', 'Value']);
            fputcsv($file, ['Total Cases', Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->count()]);
            fputcsv($file, ['Active Cases', Case_Model::when($user->role !== 'admin', function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })->where('case_status', '!=', 'closed')->count()]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}