<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserSchedule;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'user_schedule_id', // Ganti working_hour_id dengan user_schedule_id
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user schedule that associated with the attendance.
     */
    public function userSchedule()
    {
        return $this->belongsTo(UserSchedule::class);
    }
}
