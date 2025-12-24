<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingHour;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    /**
     * Display a listing of working hours.
     */
    public function index()
    {
        $workingHours = WorkingHour::latest()->paginate(10);
        
        return view('admin.working_hours.index', compact('workingHours'));
    }

    /**
     * Show the form for creating a new working hour.
     */
    public function create()
    {
        return view('admin.working_hours.create');
    }

    /**
     * Store a newly created working hour in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        $workingHour = WorkingHour::create([
            'nama' => $request->nama,
            'jam_masuk' => $request->jam_masuk . ':00',
            'jam_pulang' => $request->jam_pulang . ':00',
        ]);

        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Tambah Jam Kerja',
            'description' => 'Menambahkan jam kerja: ' . $workingHour->nama,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);

        return redirect()->route('admin.working-hours.index')
            ->with('success', 'Jam kerja berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified working hour.
     */
    public function edit(WorkingHour $workingHour)
    {
        return view('admin.working_hours.edit', compact('workingHour'));
    }

    /**
     * Update the specified working hour in storage.
     */
    public function update(Request $request, WorkingHour $workingHour)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        $oldName = $workingHour->nama;

        $workingHour->update([
            'nama' => $request->nama,
            'jam_masuk' => $request->jam_masuk . ':00',
            'jam_pulang' => $request->jam_pulang . ':00',
        ]);

        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Update Jam Kerja',
            'description' => 'Mengubah jam kerja: ' . $oldName . ' menjadi ' . $workingHour->nama,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);

        return redirect()->route('admin.working-hours.index')
            ->with('success', 'Jam kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified working hour from storage.
     */
    public function destroy(WorkingHour $workingHour)
    {
        $name = $workingHour->nama;
        
        $workingHour->delete();

        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Hapus Jam Kerja',
            'description' => 'Menghapus jam kerja: ' . $name,
            'ip' => request()->ip(),
            'method' => request()->method(),
            'performed_at' => now()
        ]);

        return redirect()->route('admin.working-hours.index')
            ->with('success', 'Jam kerja berhasil dihapus.');
    }
}
