<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];
    
    /**
     * Get the schedules associated with the subject
     */
    public function schedules()
    {
        return $this->hasMany(UserSchedule::class);
    }
    
    /**
     * Get users associated with this subject through schedules
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_schedules');
    }
}
