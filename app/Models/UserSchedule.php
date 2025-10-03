<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'day',
        'start_time',
        'end_time',
        'classroom',
        'is_active'
    ];
    
    /**
     * Get the user that owns the schedule
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the subject for this schedule
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    /**
     * Get day name in Indonesian
     */
    public function getDayNameAttribute()
    {
        return $this->day;
    }
    
    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        return \Carbon\Carbon::parse($this->start_time)->format('H:i') . ' - ' . 
               \Carbon\Carbon::parse($this->end_time)->format('H:i');
    }
}
