<?php

namespace App\Models;

use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'working_hour_id',
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
     * Get the working hour that associated with the attendance.
     */
    public function workingHour()
    {
        return $this->belongsTo(WorkingHour::class);
    }
}
