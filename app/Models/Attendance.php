<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserSchedule;
use App\Models\WorkingHour;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'user_schedule_id', // Nullable - untuk kompatibilitas mundur
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'image',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the user schedule for this attendance (optional)
     */
    public function userSchedule()
    {
        return $this->belongsTo(UserSchedule::class, 'user_schedule_id');
    }
    
    /**
     * Get work hours from database (Jam Kerja Umum)
     */
    public static function getGeneralWorkHours()
    {
        return WorkingHour::where('nama', 'Jam Kerja Umum')->first();
    }
    
    /**
     * Check if check-in is late
     */
    public function isLate()
    {
        if (!$this->check_in) {
            return false;
        }
        
        $workHours = self::getGeneralWorkHours();
        if (!$workHours) {
            return false;
        }
        
        $checkInTime = \Carbon\Carbon::parse($this->check_in);
        $expectedTime = \Carbon\Carbon::parse($workHours->jam_masuk);
        $tolerance = config('attendance.work_hours.late_tolerance', 15);
        
        return $checkInTime->greaterThan($expectedTime->addMinutes($tolerance));
    }
}
