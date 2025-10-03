<?php
namespace App\Http\Controllers;

use App\Models\WorkingHour;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    // Tampilkan semua jam kerja
    public function index()
    {
        $workingHours = WorkingHour::withCount('users')->get();
        return view('admin.working_hours.index', compact('workingHours'));
    }
    
    // Menampilkan halaman manajemen user untuk jam kerja tertentu
    public function users($id)
    {
        $workingHour = WorkingHour::findOrFail($id);
        $assignedUsers = $workingHour->users()->get();
        $users = \App\Models\User::where('role', 'user')->get();
        
        return view('admin.working_hours.users', compact('workingHour', 'assignedUsers', 'users'));
    }
    
    // Menambahkan user ke jam kerja tertentu
    public function assignUsers(Request $request, $id)
    {
        $workingHour = WorkingHour::findOrFail($id);
        $userIds = $request->input('user_ids', []);
        
        // Sync user IDs
        $workingHour->users()->sync($userIds);
        
        return redirect()->route('admin.working-hours.users', $id)
                         ->with('success', 'Daftar user berhasil diperbarui');
    }

    // Tampilkan form tambah jam kerja
    public function create()
    {
        return view('admin.working_hours.create');
    }

    // Simpan jam kerja baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ]);
        WorkingHour::create($request->all());
        return redirect()->route('admin.working-hours.index')->with('success', 'Jam kerja berhasil ditambahkan.');
    }

    // Tampilkan form edit jam kerja
    public function edit($id)
    {
        $workingHour = WorkingHour::findOrFail($id);
        return view('admin.working_hours.edit', compact('workingHour'));
    }

    // Update jam kerja
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ]);
        $workingHour = WorkingHour::findOrFail($id);
        $workingHour->update($request->all());
        return redirect()->route('admin.working-hours.index')->with('success', 'Jam kerja berhasil diupdate.');
    }

    // Hapus jam kerja
    public function destroy($id)
    {
        $workingHour = WorkingHour::findOrFail($id);
        $workingHour->delete();
        return redirect()->route('admin.working-hours.index')->with('success', 'Jam kerja berhasil dihapus.');
    }
}
