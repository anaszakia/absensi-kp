<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserSchedule;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'user_schedule_id', // Ganti working_hour_id dengan user_schedule_id
        'date',
        'reason',
        'attachment',
        'status',
        'admin_remarks',
        'approved_by',
        'approved_at'
    ];
    
    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];
    
    /**
     * Get the user that owns the leave request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the admin who approved/rejected the request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the user schedule associated with the leave request.
     */
    public function userSchedule()
    {
        return $this->belongsTo(UserSchedule::class);
    }
}
