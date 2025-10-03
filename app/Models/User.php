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
     * Relasi dengan WorkingHour (many-to-many)
     */
    public function workingHours()
    {
        return $this->belongsToMany(WorkingHour::class, 'user_working_hours')
            ->withTimestamps();
    }
    
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
     * Relasi dengan LeaveRequest (one-to-many)
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
