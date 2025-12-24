<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah sistem absensi dari per jadwal menjadi sistem umum
     */
    public function up(): void
    {
        // 1. Buat kembali tabel working_hours jika belum ada
        if (!Schema::hasTable('working_hours')) {
            Schema::create('working_hours', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->time('jam_masuk');
                $table->time('jam_pulang');
                $table->timestamps();
            });
        }
        
        // 2. Buat jam kerja umum default di tabel working_hours
        DB::table('working_hours')->insert([
            'nama' => 'Jam Kerja Umum',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '16:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 3. Ubah kolom user_schedule_id menjadi nullable di attendances jika ada
        if (Schema::hasColumn('attendances', 'user_schedule_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropForeign(['user_schedule_id']);
                $table->foreignId('user_schedule_id')->nullable()->change();
            });
        }
        
        // 4. Ubah kolom user_schedule_id menjadi nullable di leave_requests jika ada
        if (Schema::hasColumn('leave_requests', 'user_schedule_id')) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->dropForeign(['user_schedule_id']);
                $table->foreignId('user_schedule_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus jam kerja umum
        DB::table('working_hours')->where('nama', 'Jam Kerja Umum')->delete();
        
        // Kembalikan user_schedule_id menjadi required jika perlu
        if (Schema::hasColumn('attendances', 'user_schedule_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreignId('user_schedule_id')->nullable(false)->change();
                $table->foreign('user_schedule_id')->references('id')->on('user_schedules')->onDelete('cascade');
            });
        }
    }
};
