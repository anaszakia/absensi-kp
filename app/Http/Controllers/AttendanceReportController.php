<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceReportController extends Controller
{
    /**
     * Menampilkan rekap absensi semua user
     */
    public function index(Request $request)
    {
        // Ambil filter dari request
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $status = $request->input('status');

        // Query dasar
        $attendances = Attendance::with(['user', 'userSchedule.subject'])
            ->whereBetween('date', [$startDate, $endDate])
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest('date')
            ->paginate(15)
            ->appends($request->query());

        // Data untuk filter
        $users = User::where('role', 'user')->orderBy('name')->get();
        $statuses = ['tepat_waktu', 'terlambat', 'ijin', 'tidak_masuk'];

        // Statistik
        $totalPresent = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('check_in')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->count();

        $totalLate = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'terlambat')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->count();

        $totalLeave = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'ijin')
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->count();

        return view('admin.attendance.index', compact(
            'attendances', 
            'users', 
            'statuses',
            'userId',
            'startDate',
            'endDate',
            'status',
            'totalPresent',
            'totalLate',
            'totalLeave'
        ));
    }

    /**
     * Export data absensi ke Excel
     */
    public function export(Request $request)
    {
        $fileName = 'rekap-absensi-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new AttendanceExport($request), $fileName);
    }
}