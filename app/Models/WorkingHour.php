<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $table = 'working_hours';

    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_pulang',
    ];

    /**
     * Relasi dengan User (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_working_hours')
            ->withTimestamps();
    }
}
