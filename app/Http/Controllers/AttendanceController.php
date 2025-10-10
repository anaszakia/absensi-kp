<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSchedule;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Tampilkan halaman dashboard absensi
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Dapatkan jadwal mata pelajaran untuk hari ini
        $today = Carbon::now()->locale('id')->dayName;
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        // Jika hari dalam bahasa Inggris, konversi ke Indonesia
        if (isset($dayMapping[$today])) {
            $today = $dayMapping[$today];
        }
        
        // Dapatkan jadwal hari ini
        $todaySchedules = $user->schedules()
            ->where('day', $today)
            ->where('is_active', true)
            ->with('subject')
            ->orderBy('start_time')
            ->get();
        
        // Ambil absensi hari ini untuk setiap jadwal
        $todayAttendances = $user->todayAttendances();
        
        // Untuk kompatibilitas dengan view lama
        $todayAttendance = $user->todayAttendance();
        
        // Ambil riwayat absensi 7 hari terakhir
        $recentAttendances = $user->attendances()
            ->with(['userSchedule', 'userSchedule.subject'])
            ->latest('date')
            ->take(7)
            ->get();
        
        // Statistik absensi bulan ini
        $currentMonth = now()->format('Y-m');
        $monthlyStats = [
            'tepat_waktu' => $user->attendances()
                ->where('status', 'tepat_waktu')
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                ->count(),
            'terlambat' => $user->attendances()
                ->where('status', 'terlambat')
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                ->count(),
            'tidak_masuk' => $user->attendances()
                ->where('status', 'tidak_masuk')
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                ->count(),
            'ijin' => $user->attendances()
                ->where('status', 'ijin')
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                ->count(),
        ];
        
        return view('user.dashboard', compact('user', 'todaySchedules', 'todayAttendance', 'todayAttendances', 'recentAttendances', 'monthlyStats'));
    }
    
    /**
     * Proses check-in (absen masuk)
     */
    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $userScheduleId = $request->input('user_schedule_id');
        $notes = $request->input('notes');
        
        // Validasi user schedule
        if (!$userScheduleId) {
            return back()->with('error', 'Pilih jadwal mata pelajaran untuk absensi.');
        }
        
        $userSchedule = \App\Models\UserSchedule::find($userScheduleId);
        if (!$userSchedule) {
            return back()->with('error', 'Jadwal mata pelajaran tidak ditemukan.');
        }
        
        // Pastikan jadwal adalah milik user yang sedang login
        if ($userSchedule->user_id != $user->id) {
            return back()->with('error', 'Jadwal mata pelajaran tidak sesuai.');
        }
        
        // Pastikan jadwal adalah untuk hari ini
        $today = Carbon::now()->locale('id')->dayName;
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        if (isset($dayMapping[$today])) {
            $today = $dayMapping[$today];
        }
        
        if ($userSchedule->day !== $today) {
            return back()->with('error', 'Jadwal mata pelajaran hanya berlaku untuk hari ' . $userSchedule->day . '.');
        }
        
        // Cek apakah sudah ada absensi untuk jadwal ini hari ini
        $existingAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->where('user_schedule_id', $userScheduleId)
            ->whereDate('date', now()->toDateString())
            ->first();
            
        if ($existingAttendance && $existingAttendance->check_in) {
            return back()->with('error', 'Anda sudah melakukan absen masuk untuk jadwal ini hari ini.');
        }
        
        $now = now();
        $jamMasuk = \Carbon\Carbon::createFromFormat('H:i:s', $userSchedule->start_time);
        
        // Cek keterlambatan (toleransi 15 menit)
        $telatDalamMenit = $now->diffInMinutes($jamMasuk, false);
        
        // Jika terlambat lebih dari 15 menit dari waktu mulai, status = terlambat
        if ($now->format('H:i:s') > $jamMasuk->addMinutes(15)->format('H:i:s')) {
            $status = 'terlambat';
        } else {
            $status = 'tepat_waktu';
        }
        
        // Buat atau update absensi
        if (!$existingAttendance) {
            $attendance = \App\Models\Attendance::create([
                'user_id' => $user->id,
                'user_schedule_id' => $userScheduleId,
                'date' => now()->toDateString(),
                'check_in' => now()->toTimeString(),
                'status' => $status,
                'notes' => $notes
            ]);
        } else {
            // Update absensi yang ada
            $existingAttendance->update([
                'check_in' => now()->toTimeString(),
                'status' => $status,
                'notes' => $notes
            ]);
        }
        
        // Log aktivitas
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Absen Masuk',
            'description' => 'Melakukan absen masuk pada ' . now()->format('H:i'),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return back()->with('success', 'Absen masuk berhasil dicatat.');
    }
    
    /**
     * Proses check-out (absen pulang)
     */
    public function checkOut(Request $request)
    {
        $user = auth()->user();
        $userScheduleId = $request->input('user_schedule_id');
        $notes = $request->input('notes');
        
        // Validasi user schedule
        if (!$userScheduleId) {
            return back()->with('error', 'Pilih jadwal mata pelajaran untuk absensi pulang.');
        }
        
        // Cek apakah sudah ada absensi untuk jadwal ini hari ini
        $attendance = \App\Models\Attendance::where('user_id', $user->id)
            ->where('user_schedule_id', $userScheduleId)
            ->whereDate('date', now()->toDateString())
            ->first();
            
        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk untuk jadwal ini hari ini.');
        }
        
        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan absen pulang untuk jadwal ini hari ini.');
        }
        
        // Cek apakah sudah waktunya pulang berdasarkan jadwal
        $userSchedule = \App\Models\UserSchedule::find($userScheduleId);
        $jamPulang = \Carbon\Carbon::createFromFormat('H:i:s', $userSchedule->end_time);
        $now = now();
        
        // Update absensi
        $attendance->update([
            'check_out' => now()->toTimeString(),
            'notes' => $notes ? $attendance->notes . ' | Pulang: ' . $notes : $attendance->notes
        ]);
        
        // Log aktivitas
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Absen Pulang',
            'description' => 'Melakukan absen pulang pada ' . now()->format('H:i'),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return back()->with('success', 'Absen pulang berhasil dicatat.');
    }
    
    /**
     * Tampilkan riwayat absensi user
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        
        // Filter berdasarkan bulan dan tahun
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));
        
        $attendances = $user->attendances()
            ->with('userSchedule.subject')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->latest('date')
            ->paginate(15);
        
        return view('user.attendance.history', compact('user', 'attendances', 'month', 'year'));
    }
    
    /**
     * Pengajuan ijin tidak masuk
     */
    public function requestLeave(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'user_schedule_id' => 'required|exists:user_schedules,id',
            'notes' => 'required|string|min:5',
        ]);
        
        // Validasi jadwal milik user
        $userSchedule = \App\Models\UserSchedule::find($request->user_schedule_id);
        if ($userSchedule->user_id != $user->id) {
            return back()->with('error', 'Jadwal mata pelajaran tidak sesuai.');
        }
        
        // Cek apakah sudah ada absensi pada tanggal dan jadwal tersebut
        $existingAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->where('user_schedule_id', $request->user_schedule_id)
            ->whereDate('date', $request->date)
            ->first();
            
        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah memiliki catatan absensi untuk jadwal ini pada tanggal tersebut.');
        }
        
        // Buat absensi dengan status ijin
        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'user_schedule_id' => $request->user_schedule_id,
            'date' => $request->date,
            'status' => 'ijin',
            'notes' => $request->notes
        ]);
        
        // Log aktivitas
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Pengajuan Ijin',
            'description' => 'Mengajukan ijin untuk tanggal ' . \Carbon\Carbon::parse($request->date)->format('d M Y'),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return back()->with('success', 'Pengajuan ijin berhasil disimpan.');
    }
}
