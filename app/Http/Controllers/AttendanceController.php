<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Tampilkan halaman dashboard absensi
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Dapatkan jam kerja yang terkait dengan user
        $workingHours = $user->workingHours;
        
        // Ambil absensi hari ini
        $todayAttendance = $user->todayAttendance();
        
        // Ambil riwayat absensi 7 hari terakhir
        $recentAttendances = $user->attendances()
            ->with('workingHour')
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
        
        // Cari jam kerja default user untuk hari ini (jam kerja pertama yang ditemukan)
        $defaultWorkingHour = $workingHours->first();
        
        return view('user.dashboard', compact('user', 'workingHours', 'todayAttendance', 'recentAttendances', 'monthlyStats', 'defaultWorkingHour'));
    }
    
    /**
     * Proses check-in (absen masuk)
     */
    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $workingHourId = $request->input('working_hour_id');
        $notes = $request->input('notes');
        
        // Validasi working hour
        if (!$workingHourId) {
            // Jika tidak ada working_hour_id, ambil jam kerja pertama user
            $defaultWorkingHour = $user->workingHours->first();
            if (!$defaultWorkingHour) {
                return back()->with('error', 'Anda belum memiliki jadwal jam kerja. Silakan hubungi admin.');
            }
            $workingHourId = $defaultWorkingHour->id;
        }
        
        $workingHour = \App\Models\WorkingHour::find($workingHourId);
        if (!$workingHour) {
            return back()->with('error', 'Jam kerja tidak ditemukan.');
        }
        
        // Cek apakah sudah ada absensi hari ini
        $todayAttendance = $user->todayAttendance();
        if ($todayAttendance && $todayAttendance->check_in) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }
        
        $now = now();
        $jamMasuk = \Carbon\Carbon::createFromFormat('H:i:s', $workingHour->jam_masuk);
        
        // Cek keterlambatan
        $telatDalamMenit = $now->diffInMinutes($jamMasuk, false);
        
        // Jika terlambat lebih dari 60 menit (1 jam), status = tidak_masuk
        if ($now->format('H:i:s') > $workingHour->jam_masuk && $telatDalamMenit < -60) {
            $status = 'tidak_masuk';
        } else {
            $status = $now->format('H:i:s') <= $workingHour->jam_masuk ? 'tepat_waktu' : 'terlambat';
        }
        
        // Jika belum ada absensi, buat baru
        if (!$todayAttendance) {
            $attendance = \App\Models\Attendance::create([
                'user_id' => $user->id,
                'working_hour_id' => $workingHourId,
                'date' => now()->toDateString(),
                'check_in' => now()->toTimeString(),
                'status' => $status,
                'notes' => $notes
            ]);
        } else {
            // Update absensi yang ada
            $todayAttendance->update([
                'working_hour_id' => $workingHourId,
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
        $notes = $request->input('notes');
        
        // Cek apakah sudah ada absensi hari ini
        $todayAttendance = $user->todayAttendance();
        if (!$todayAttendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }
        
        if ($todayAttendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }
        
        // Cek apakah sudah waktunya pulang
        $workingHour = $todayAttendance->workingHour;
        $jamPulang = \Carbon\Carbon::createFromFormat('H:i:s', $workingHour->jam_pulang);
        $now = now();
        
        // Jika belum waktunya pulang (waktu sekarang kurang dari jam pulang)
        if ($now->format('H:i:s') < $workingHour->jam_pulang) {
            return back()->with('warning', 'Belum waktunya pulang. Jam pulang Anda adalah ' . $jamPulang->format('H:i') . '.');
        }
        
        // Update absensi
        $todayAttendance->update([
            'check_out' => now()->toTimeString(),
            'notes' => $notes ? $todayAttendance->notes . ' | Pulang: ' . $notes : $todayAttendance->notes
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
            ->with('workingHour')
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
            'working_hour_id' => 'required|exists:working_hours,id',
            'notes' => 'required|string|min:5',
        ]);
        
        // Cek apakah sudah ada absensi pada tanggal tersebut
        $existingAttendance = $user->attendances()
            ->whereDate('date', $request->date)
            ->first();
            
        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah memiliki catatan absensi pada tanggal tersebut.');
        }
        
        // Buat absensi dengan status ijin
        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'working_hour_id' => $request->working_hour_id,
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
