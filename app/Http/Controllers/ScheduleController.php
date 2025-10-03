<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSchedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display user's schedule
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $selectedDay = $request->input('day', Carbon::now()->locale('id')->dayName);
        
        // Map English day names to Indonesian
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        // If the day is in English, convert to Indonesian
        if (isset($dayMapping[$selectedDay])) {
            $selectedDay = $dayMapping[$selectedDay];
        }
        
        $schedules = $user->schedules()
            ->where('day', $selectedDay)
            ->where('is_active', true)
            ->with('subject')
            ->orderBy('start_time')
            ->get();
            
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        // Get count of schedules for each day for display in the tab
        $scheduleCounts = [];
        foreach ($days as $day) {
            $scheduleCounts[$day] = $user->schedules()
                ->where('day', $day)
                ->where('is_active', true)
                ->count();
        }
        
        return view('user.schedules.index', compact('schedules', 'days', 'selectedDay', 'scheduleCounts'));
    }
    
    /**
     * Show weekly schedule in calendar view
     */
    public function calendar()
    {
        $user = auth()->user();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        // Get all schedules grouped by day
        $schedulesByDay = [];
        foreach ($days as $day) {
            $schedulesByDay[$day] = $user->schedules()
                ->where('day', $day)
                ->where('is_active', true)
                ->with('subject')
                ->orderBy('start_time')
                ->get();
        }
        
        return view('user.schedules.calendar', compact('schedulesByDay', 'days'));
    }
}
