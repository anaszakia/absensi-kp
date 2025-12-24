<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jam Kerja Umum
    |--------------------------------------------------------------------------
    |
    | Konfigurasi jam masuk dan jam keluar yang berlaku untuk semua pegawai
    |
    */
    
    'work_hours' => [
        'check_in_time' => '08:00:00',  // Jam masuk
        'check_out_time' => '16:00:00', // Jam pulang
        'late_tolerance' => 15,          // Toleransi keterlambatan (menit)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Status Absensi
    |--------------------------------------------------------------------------
    |
    | Daftar status absensi yang tersedia
    |
    */
    
    'status' => [
        'on_time' => 'tepat_waktu',
        'late' => 'terlambat',
        'absent' => 'tidak_masuk',
        'leave' => 'ijin',
    ],
];
