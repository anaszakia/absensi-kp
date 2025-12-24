<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\WorkingHour;
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
        // Ambil jam kerja umum
        $workingHour = WorkingHour::where('nama', 'Jam Kerja Umum')->first();
        $jamMasuk = $workingHour ? Carbon::parse($workingHour->jam_masuk) : Carbon::parse('08:00:00');
        
        // Hitung jumlah terlambat hari ini
        $terlambatHariIni = Attendance::whereDate('date', today())
            ->whereNotNull('check_in')
            ->where(function($query) use ($jamMasuk) {
                $query->whereRaw("TIME(check_in) > ?", [$jamMasuk->format('H:i:s')]);
            })
            ->count();
        
        // Data untuk admin dashboard
        $data = [
            'totalUsers' => User::count(),
            'absenHariIni' => Attendance::whereDate('date', today())->count(),
            'ijinHariIni' => LeaveRequest::whereDate('date', today())
                ->where('status', 'approved')
                ->count(),
            'terlambatHariIni' => $terlambatHariIni,
            'recentUsers' => User::latest()->take(5)->get(),
            'recentActivity' => AuditLog::with('user')->latest()->take(10)->get(),
        ];
        
        return view('admin.dashboard', $data);
    }
}
