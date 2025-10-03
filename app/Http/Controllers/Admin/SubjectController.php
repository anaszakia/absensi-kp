<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $subjects = Subject::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());
            
        return view('admin.subjects.index', compact('subjects', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects',
            'description' => 'nullable|string|max:1000',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $subject = Subject::create($validated);
        
        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'store',
            'description' => 'Membuat mata pelajaran baru: ' . $subject->name,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subject = Subject::findOrFail($id);
        
        // Get all users assigned to this subject with their schedules
        $schedules = $subject->schedules()
            ->with('user')
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();
            
        return view('admin.subjects.show', compact('subject', 'schedules'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subject = Subject::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $subject->update($validated);
        
        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Mengubah mata pelajaran: ' . $subject->name,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);
        $subjectName = $subject->name;
        
        // Check if subject has associated schedules
        if ($subject->schedules()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus mata pelajaran yang memiliki jadwal terkait');
        }
        
        $subject->delete();
        
        // Log activity
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'destroy',
            'description' => 'Menghapus mata pelajaran: ' . $subjectName,
            'ip' => request()->ip(),
            'method' => request()->method(),
            'performed_at' => now()
        ]);
        
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus');
    }
}
