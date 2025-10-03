<?php

namespace App\Http\Controllers;

use App\Models\UserSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserScheduleController extends Controller
{
    /**
     * Display the authenticated user's schedules
     * Grouped by days
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $day = $request->query('day');
        
        // Mendukung filter hari dalam bahasa Indonesia dan Inggris
        $validDays = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
            'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
        ];
        
        // Order days correctly - menggunakan bahasa Indonesia karena sepertinya data menggunakan bahasa Indonesia
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        // Set hari default ke Senin jika tidak ada filter
        if (!$day) {
            $day = 'Senin';
        }
        
        // Ambil semua jadwal untuk menghitung jumlah per hari
        $allSchedules = $user->schedules()->with('subject')->get();
        
        // Hitung jumlah jadwal per hari
        $scheduleCounts = [];
        foreach ($days as $dayName) {
            $scheduleCounts[$dayName] = $allSchedules->where('day', $dayName)->count();
        }
        
        // Filter berdasarkan hari yang dipilih
        $query = $user->schedules()->with('subject');
        if (in_array($day, $validDays)) {
            $query->where('day', $day);
        }
        
        $schedules = $query->orderBy('start_time')->get();
        
        return view('user.schedules.index', [
            'schedules' => $schedules,
            'days' => $days,
            'selectedDay' => $day,
            'scheduleCounts' => $scheduleCounts
        ]);
    }
    
    /**
     * Display the authenticated user's schedules in calendar view
     */
    public function calendar()
    {
        $user = Auth::user();
        $schedules = $user->schedules()->with('subject')->get();
        
        // Definisi hari dalam bahasa Indonesia
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        // Kelompokkan jadwal berdasarkan hari
        $schedulesByDay = collect();
        foreach ($days as $day) {
            $schedulesByDay[$day] = $schedules->filter(function($schedule) use ($day) {
                return $schedule->day === $day;
            });
        }
        
        return view('user.schedules.calendar', [
            'days' => $days,
            'schedulesByDay' => $schedulesByDay
        ]);
    }
}