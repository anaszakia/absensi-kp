<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'phone',
        'address',
        'gender',
        'position',
        'photo',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * DEPRECATED: Relasi dengan WorkingHour (many-to-many)
     * Tidak digunakan lagi karena sistem jam kerja telah dihapus
     * Absensi sekarang berdasarkan jadwal mata pelajaran
     */
    // public function workingHours()
    // {
    //     return $this->belongsToMany(WorkingHour::class, 'user_working_hours')
    //         ->withTimestamps();
    // }
    
    /**
     * Relasi dengan Attendance (one-to-many)
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    /**
     * Get today's attendance
     */
    public function todayAttendance()
    {
        return $this->attendances()
            ->whereDate('date', now()->toDateString())
            ->first();
    }
    
    /**
     * Check if user has checked in today
     */
    public function hasCheckedInToday()
    {
        $attendance = $this->todayAttendance();
        return $attendance && $attendance->check_in;
    }
    
    /**
     * Check if user has checked out today
     */
    public function hasCheckedOutToday()
    {
        $attendance = $this->todayAttendance();
        return $attendance && $attendance->check_out;
    }
    
    /**
     * Relasi dengan LeaveRequest (one-to-many)
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    
    /**
     * Relasi dengan UserSchedule (one-to-many)
     */
    public function schedules()
    {
        return $this->hasMany(UserSchedule::class);
    }
    
    /**
     * Subjects assigned to this user through schedules
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'user_schedules')
            ->withPivot(['day', 'start_time', 'end_time', 'classroom'])
            ->withTimestamps();
    }
    
    /**
     * Get schedules for a specific day
     */
    public function getSchedulesForDay($day)
    {
        return $this->schedules()
            ->where('day', $day)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }
}
