<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\UserSchedule;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class UserScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $search = $request->input('search');
        
        $users = User::where('role', 'user')
            ->orderBy('name')
            ->get();
            
        $schedules = UserSchedule::with(['user', 'subject'])
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->orderBy('day')
            ->orderBy('start_time')
            ->paginate(15)
            ->appends($request->query());
            
        return view('admin.schedules.index', compact('schedules', 'users', 'userId', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        return view('admin.schedules.create', compact('users', 'subjects', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
        ]);
        
        // Format start_time and end_time to include seconds
        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';
        
        // Check for schedule conflicts
        $conflictingSchedule = UserSchedule::where('user_id', $validated['user_id'])
            ->where('day', $validated['day'])
            ->where(function ($query) use ($validated) {
                // Check if new schedule overlaps with existing schedules
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->first();
            
        if ($conflictingSchedule) {
            $subject = Subject::find($conflictingSchedule->subject_id);
            return back()->withInput()
                ->with('error', 'Jadwal bentrok dengan jadwal ' . $subject->name . 
                        ' (' . $conflictingSchedule->start_time . ' - ' . 
                        $conflictingSchedule->end_time . ')');
        }
        
        $validated['is_active'] = true;
        
        $schedule = UserSchedule::create($validated);
        
        $user = User::find($validated['user_id']);
        $subject = Subject::find($validated['subject_id']);
        
        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'store',
            'description' => 'Menambahkan jadwal ' . $subject->name . ' untuk ' . $user->name,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = UserSchedule::with(['user', 'subject'])->findOrFail($id);
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = UserSchedule::findOrFail($id);
        $users = User::where('role', 'user')->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        // Format times without seconds for the form
        $startTime = substr($schedule->start_time, 0, 5);
        $endTime = substr($schedule->end_time, 0, 5);
        
        return view('admin.schedules.edit', compact('schedule', 'users', 'subjects', 'days', 'startTime', 'endTime'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = UserSchedule::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        
        // Format start_time and end_time to include seconds
        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';
        $validated['is_active'] = $request->has('is_active');
        
        // Check for schedule conflicts (excluding this schedule)
        $conflictingSchedule = UserSchedule::where('user_id', $validated['user_id'])
            ->where('day', $validated['day'])
            ->where('id', '!=', $id)
            ->where(function ($query) use ($validated) {
                // Check if new schedule overlaps with existing schedules
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->first();
            
        if ($conflictingSchedule) {
            $subject = Subject::find($conflictingSchedule->subject_id);
            return back()->withInput()
                ->with('error', 'Jadwal bentrok dengan jadwal ' . $subject->name . 
                        ' (' . $conflictingSchedule->start_time . ' - ' . 
                        $conflictingSchedule->end_time . ')');
        }
        
        $schedule->update($validated);
        
        $user = User::find($validated['user_id']);
        $subject = Subject::find($validated['subject_id']);
        
        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Mengubah jadwal ' . $subject->name . ' untuk ' . $user->name,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = UserSchedule::with(['user', 'subject'])->findOrFail($id);
        $description = 'Menghapus jadwal ' . $schedule->subject->name . ' untuk ' . $schedule->user->name;
        
        $schedule->delete();
        
        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'destroy',
            'description' => $description,
            'ip' => request()->ip(),
            'method' => request()->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }
}
