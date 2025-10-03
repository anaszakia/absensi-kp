<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $user = auth()->user();
        
        // Data untuk user dashboard
        $data = [
            'user' => $user,
            'totalUsers' => User::count(),
            'todayLogins' => AuditLog::where('action', 'Login')
                ->whereDate('created_at', today())
                ->count(),
            'myLoginHistory' => AuditLog::where('user_id', $user->id)
                ->where('action', 'Login')
                ->latest()
                ->take(5)
                ->get(),
            'recentActivity' => AuditLog::where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get(),
            'accountCreated' => $user->created_at->diffForHumans(),
            'lastLogin' => AuditLog::where('user_id', $user->id)
                ->where('action', 'Login')
                ->latest()
                ->first()?->created_at?->diffForHumans() ?? 'Belum pernah login',
        ];
        
        return view('user.dashboard', $data);
    }

    public function adminDashboard()
    {
        // Data untuk admin dashboard
        $data = [
            'totalUsers' => User::count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalRegularUsers' => User::where('role', 'user')->count(),
            'todayRegistrations' => User::whereDate('created_at', today())->count(),
            'thisWeekRegistrations' => User::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'thisMonthRegistrations' => User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'todayLogins' => AuditLog::where('action', 'Login')
                ->whereDate('created_at', today())
                ->count(),
            'totalAuditLogs' => AuditLog::count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentActivity' => AuditLog::with('user')->latest()->take(10)->get(),
            'userGrowthData' => $this->getUserGrowthData(),
            'loginStats' => $this->getLoginStats(),
        ];
        
        return view('admin.dashboard', $data);
    }
    
    private function getUserGrowthData()
    {
        $months = [];
        $userCounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $userCounts[] = $count;
        }
        
        return [
            'months' => $months,
            'userCounts' => $userCounts
        ];
    }
    
    private function getLoginStats()
    {
        $days = [];
        $loginCounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            $count = AuditLog::where('action', 'Login')
                ->whereDate('created_at', $date)
                ->count();
            $loginCounts[] = $count;
        }
        
        return [
            'days' => $days,
            'loginCounts' => $loginCounts
        ];
    }
}
