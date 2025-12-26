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
        
        // Ambil jam kerja umum
        $workHours = \App\Models\WorkingHour::where('nama', 'Jam Kerja Umum')->first();
        
        // Ambil absensi hari ini
        $todayAttendance = $user->attendances()
            ->whereDate('date', now()->toDateString())
            ->first();
        
        // Ambil riwayat absensi 7 hari terakhir
        $recentAttendances = $user->attendances()
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
        
        // Cek apakah sudah waktunya pulang
        $canCheckOut = false;
        if ($workHours) {
            $jamPulang = \Carbon\Carbon::createFromFormat('H:i:s', $workHours->jam_pulang);
            $now = now();
            $canCheckOut = $now->format('H:i:s') >= $jamPulang->format('H:i:s');
        }
        
        return view('user.dashboard', compact('user', 'workHours', 'todayAttendance', 'recentAttendances', 'monthlyStats', 'canCheckOut'));
    }
    
    /**
     * Proses check-in (absen masuk)
     */
    public function checkIn(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string',
            ], [
                'image.required' => 'Foto absensi wajib diisi. Silakan ambil foto terlebih dahulu.',
                'image.string' => 'Format foto tidak valid.',
            ]);

            \Log::info('Check-in attempt', [
                'user_id' => auth()->id(),
                'has_image' => !empty($request->image),
                'image_size' => $request->image ? strlen($request->image) : 0
            ]);

            $user = auth()->user();
            $notes = $request->input('notes');
        
        // Ambil jam kerja umum
        $workHours = \App\Models\WorkingHour::where('nama', 'Jam Kerja Umum')->first();
        
        if (!$workHours) {
            return back()->with('error', 'Jam kerja umum belum dikonfigurasi.');
        }
        
        // Cek apakah sudah ada absensi hari ini
        $existingAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();
            
        if ($existingAttendance && $existingAttendance->check_in) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }
        
        $now = now();
        $jamMasuk = \Carbon\Carbon::createFromFormat('H:i:s', $workHours->jam_masuk);
        $tolerance = config('attendance.work_hours.late_tolerance', 15);
        
        // Cek keterlambatan
        if ($now->format('H:i:s') > $jamMasuk->copy()->addMinutes($tolerance)->format('H:i:s')) {
            $status = 'terlambat';
        } else {
            $status = 'tepat_waktu';
        }

        // Handle foto dari kamera (base64)
        $imagePath = null;
        if ($request->image) {
            $image = $request->image;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'attendance_' . $user->id . '_' . now()->format('YmdHis') . '.png';
            
            \Storage::disk('public')->put('attendances/' . $imageName, base64_decode($image));
            $imagePath = 'attendances/' . $imageName;
        }
        
        // Buat atau update absensi
        if (!$existingAttendance) {
            $attendance = \App\Models\Attendance::create([
                'user_id' => $user->id,
                'date' => now()->toDateString(),
                'check_in' => now()->toTimeString(),
                'status' => $status,
                'notes' => $notes,
                'image' => $imagePath
            ]);
        } else {
            $existingAttendance->update([
                'check_in' => now()->toTimeString(),
                'status' => $status,
                'notes' => $notes,
                'image' => $imagePath
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
        
        \Log::info('Check-in successful', [
            'user_id' => $user->id,
            'status' => $status,
            'image_saved' => $imagePath
        ]);
        
        return back()->with('success', 'Absen masuk berhasil dicatat dengan status ' . ($status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat') . '.');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Check-in validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Check-in failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal melakukan absen masuk: ' . $e->getMessage());
        }
    }
    
    /**
     * Proses check-out (absen pulang)
     */
    public function checkOut(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string',
            ], [
                'image.required' => 'Foto absensi wajib diisi. Silakan ambil foto terlebih dahulu.',
                'image.string' => 'Format foto tidak valid.',
            ]);

            \Log::info('Check-out attempt', [
                'user_id' => auth()->id(),
                'has_image' => !empty($request->image),
                'image_size' => $request->image ? strlen($request->image) : 0
            ]);

            $user = auth()->user();
            $notes = $request->input('notes');
        
        // Cek apakah sudah ada absensi hari ini
        $attendance = \App\Models\Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();
            
        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }
        
        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // Handle foto dari kamera (base64)
        $imagePath = $attendance->image; // Keep existing image
        if ($request->image) {
            // Save checkout image with different naming
            $image = $request->image;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'attendance_out_' . $user->id . '_' . now()->format('YmdHis') . '.png';
            
            \Storage::disk('public')->put('attendances/' . $imageName, base64_decode($image));
            // Store both images separated by pipe
            $imagePath = $attendance->image . '|' . 'attendances/' . $imageName;
        }
        
        // Update absensi
        $attendance->update([
            'check_out' => now()->toTimeString(),
            'notes' => $notes ? $attendance->notes . ' | Pulang: ' . $notes : $attendance->notes,
            'image' => $imagePath
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
        
        \Log::info('Check-out successful', [
            'user_id' => $user->id,
            'image_saved' => $imagePath
        ]);
        
        return back()->with('success', 'Absen pulang berhasil dicatat.');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Check-out validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Check-out failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal melakukan absen pulang: ' . $e->getMessage());
        }
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
            'notes' => 'required|string|min:5',
        ]);
        
        // Cek apakah sudah ada absensi pada tanggal tersebut
        $existingAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->whereDate('date', $request->date)
            ->first();
            
        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah memiliki catatan absensi pada tanggal tersebut.');
        }
        
        // Buat absensi dengan status ijin
        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
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
