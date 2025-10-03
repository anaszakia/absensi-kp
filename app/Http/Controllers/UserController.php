<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        //search and pagination
        $keyword = $request->query('search');

        $users = User::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($q2) use ($keyword) {
                    $q2->where('name',  'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('nip', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('position', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(5)
            ->appends(['search' => $keyword]);
            
        return view('admin.users.index', compact('users', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'nip'       => 'nullable|string|max:50',
            'email'     => 'required|email|unique:users,email|max:255',
            'phone'     => 'nullable|string|max:15',
            'address'   => 'nullable|string',
            'gender'    => 'nullable|in:L,P',
            'position'  => 'nullable|string|max:100',
            'role'      => 'required|in:admin,user',
            'password'  => 'required|min:8|confirmed',
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Handle file upload jika ada foto
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Pastikan direktori photos ada
                $photoPath = storage_path('app/public/photos');
                if (!file_exists($photoPath)) {
                    mkdir($photoPath, 0755, true);
                }
                
                // Simpan file secara manual daripada menggunakan storeAs
                $file->move($photoPath, $filename);
                $validated['photo'] = 'photos/' . $filename;
                
            } catch (\Exception $e) {
                // Log error
                \Log::error('Error uploading file: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
            }
        }

        User::create($validated);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        // Jika diperlukan untuk menampilkan detail user dalam halaman terpisah
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'      => 'required|string|max:100',
            'nip'       => 'nullable|string|max:50',
            'email'     => 'required|email|unique:users,email,' . $user->id . '|max:255',
            'phone'     => 'nullable|string|max:15',
            'address'   => 'nullable|string',
            'gender'    => 'nullable|in:L,P',
            'position'  => 'nullable|string|max:100',
            'role'      => 'required|in:admin,user',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $validated = $request->validate($rules, [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',
            'photo.mimes' => 'Foto harus berupa file gambar dengan format: jpeg, png, jpg.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Handle file upload jika ada foto baru
        if ($request->hasFile('photo')) {
            try {
                // Hapus foto lama jika ada
                if ($user->photo && file_exists(storage_path('app/public/' . $user->photo))) {
                    unlink(storage_path('app/public/' . $user->photo));
                }
                
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Pastikan direktori photos ada
                $photoPath = storage_path('app/public/photos');
                if (!file_exists($photoPath)) {
                    mkdir($photoPath, 0755, true);
                }
                
                // Simpan file secara manual daripada menggunakan storeAs
                $file->move($photoPath, $filename);
                $validated['photo'] = 'photos/' . $filename;
                
            } catch (\Exception $e) {
                // Log error
                \Log::error('Error uploading file: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
            }
        }

        $user->update($validated);

        return back()->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Opsional: cegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun Anda sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }
}
