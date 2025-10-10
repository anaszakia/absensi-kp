<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi tabel attendance
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan kolom user_schedule_id (sebelum menghapus working_hour_id)
            $table->foreignId('user_schedule_id')->nullable()->after('working_hour_id')
                  ->constrained()->onDelete('cascade');
        });
        
        // 2. Modifikasi tabel leave_requests
        Schema::table('leave_requests', function (Blueprint $table) {
            // Tambahkan kolom user_schedule_id (sebelum menghapus working_hour_id)
            $table->foreignId('user_schedule_id')->nullable()->after('working_hour_id')
                  ->constrained()->onDelete('cascade');
        });
        
        // 3. Migrasi data dari working_hour_id ke user_schedule_id
        // Ini harus dilakukan secara manual atau menggunakan script terpisah
        // karena tidak ada pemetaan langsung antara working_hour dengan user_schedule
        
        // 4. Hapus foreign key dan kolom working_hour_id dari tabel leave_requests
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['working_hour_id']);
            $table->dropColumn('working_hour_id');
        });
        
        // 5. Hapus kolom working_hour_id dari tabel attendance
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['working_hour_id']);
            $table->dropColumn('working_hour_id');
        });
        
        // 6. Hapus tabel user_working_hours
        Schema::dropIfExists('user_working_hours');
        
        // 7. Hapus tabel working_hours
        Schema::dropIfExists('working_hours');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Membuat kembali tabel working_hours
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->timestamps();
        });
        
        // Membuat kembali tabel user_working_hours
        Schema::create('user_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('working_hour_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
        // Tambahkan kembali kolom working_hour_id ke tabel attendance
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('working_hour_id')->nullable()->after('user_id');
        });
        
        // Hapus kolom user_schedule_id dari tabel attendance
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_schedule_id']);
            $table->dropColumn('user_schedule_id');
        });
        
        // Tambahkan kembali kolom working_hour_id ke tabel leave_requests
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('working_hour_id')->nullable()->after('user_id');
        });
        
        // Hapus kolom user_schedule_id dari tabel leave_requests
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['user_schedule_id']);
            $table->dropColumn('user_schedule_id');
        });
    }
};