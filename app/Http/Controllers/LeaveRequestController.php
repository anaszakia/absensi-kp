<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\UserSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests for the user.
     */
    public function index()
    {
        $user = auth()->user();
        $leaveRequests = $user->leaveRequests()->with('userSchedule.subject')->latest()->paginate(10);
        
        return view('user.leave_requests.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $user = auth()->user();
        $userSchedules = $user->schedules()->with('subject')->where('is_active', true)->get();
        
        // Jika user tidak memiliki jadwal, redirect kembali dengan pesan error
        if ($userSchedules->isEmpty()) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Anda belum memiliki jadwal mata pelajaran. Silakan hubungi admin.');
        }
        
        // Group jadwal berdasarkan hari
        $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $schedulesByDay = [];
        
        foreach ($dayOrder as $day) {
            $schedulesByDay[$day] = $userSchedules->filter(function($schedule) use ($day) {
                return $schedule->day == $day;
            })->sortBy('start_time');
        }
        
        return view('user.leave_requests.create', compact('schedulesByDay'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'user_schedule_id' => 'required|exists:user_schedules,id',
            'reason' => 'required|string|min:5',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        // Check if there's already attendance or leave request for this date and schedule
        $existingAttendance = $user->attendances()
            ->where('user_schedule_id', $request->user_schedule_id)
            ->whereDate('date', $request->date)
            ->first();
            
        $existingLeaveRequest = $user->leaveRequests()
            ->where('user_schedule_id', $request->user_schedule_id)
            ->whereDate('date', $request->date)
            ->first();
            
        // Verify the selected schedule belongs to this user
        $userSchedule = UserSchedule::findOrFail($request->user_schedule_id);
        if ($userSchedule->user_id != $user->id) {
            return back()->with('error', 'Jadwal yang dipilih bukan milik Anda.');
        }
        
        // Verify the requested date matches the schedule day
        $requestDate = Carbon::parse($request->date);
        $dayName = $requestDate->locale('id')->isoFormat('dddd');
        // Convert English day names to Indonesian
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        if (isset($dayMapping[$dayName])) {
            $dayName = $dayMapping[$dayName];
        }
        
        if ($userSchedule->day != $dayName) {
            return back()->with('error', 'Jadwal yang dipilih tidak sesuai dengan hari pada tanggal tersebut. Hari jadwal: ' . $userSchedule->day . ', Hari tanggal: ' . $dayName);
        }
        
        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah memiliki catatan absensi untuk jadwal ini pada tanggal tersebut.');
        }
        
        if ($existingLeaveRequest) {
            return back()->with('error', 'Anda sudah mengajukan ijin untuk jadwal ini pada tanggal tersebut.');
        }
        
        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }
        
        // Create leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'user_schedule_id' => $request->user_schedule_id,
            'date' => $request->date,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
        ]);
        
        // Log activity
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Pengajuan Ijin',
            'description' => 'Mengajukan ijin untuk jadwal ' . $userSchedule->subject->name . ' pada tanggal ' . $request->date,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('user.leave-requests.index')
            ->with('success', 'Pengajuan ijin berhasil dibuat dan menunggu persetujuan admin.');
    }
    
    /**
     * Admin functions - list all leave requests
     */
    public function adminIndex(Request $request)
    {
        $status = $request->input('status', 'pending');
        $userId = $request->input('user_id');
        
        $leaveRequests = LeaveRequest::with(['user', 'workingHour'])
            ->when($status, function ($query, $status) {
                if ($status !== 'all') {
                    return $query->where('status', $status);
                }
            })
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());
            
        $users = User::where('role', 'user')->orderBy('name')->get();
        
        return view('admin.leave_requests.index', compact('leaveRequests', 'users', 'status', 'userId'));
    }
    
    /**
     * Admin functions - approve or reject leave request
     */
    public function adminAction(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remarks' => 'nullable|string',
        ]);
        
        $admin = auth()->user();
        
        $leaveRequest->update([
            'status' => $request->status,
            'admin_remarks' => $request->admin_remarks,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
        
        // If approved, create an attendance record with status 'ijin'
        if ($request->status == 'approved') {
            Attendance::create([
                'user_id' => $leaveRequest->user_id,
                'working_hour_id' => $leaveRequest->working_hour_id,
                'date' => $leaveRequest->date,
                'status' => 'ijin',
                'notes' => 'Ijin: ' . $leaveRequest->reason,
            ]);
        }
        
        // Log activity
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => $request->status == 'approved' ? 'Menyetujui Ijin' : 'Menolak Ijin',
            'description' => ($request->status == 'approved' ? 'Menyetujui' : 'Menolak') . ' pengajuan ijin dari ' . $leaveRequest->user->name . ' untuk tanggal ' . $leaveRequest->date->format('d/m/Y'),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return back()->with('success', 'Pengajuan ijin berhasil ' . ($request->status == 'approved' ? 'disetujui' : 'ditolak'));
    }
}
