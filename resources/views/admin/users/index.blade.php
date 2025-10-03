@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div x-data="{ 
    openCreate: false,
    editModals: {},
    detailModals: {},
    toggleModal(type, userId = null) {
        if (type === 'create') {
            this.openCreate = !this.openCreate;
            document.body.style.overflow = this.openCreate ? 'hidden' : '';
        } else {
            this[type + 'Modals'][userId] = !this[type + 'Modals'][userId];
            document.body.style.overflow = this[type + 'Modals'][userId] ? 'hidden' : '';
        }
    }
}" class="space-y-6">

    {{-- HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->role === 'admin' ? 'Manajemen User' : 'Daftar User' }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->role === 'admin' ? 'Kelola akun pengguna sistem' : 'Informasi pengguna sistem' }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                {{-- Search Form --}}
                <form method="GET" action="{{ auth()->user()->role === 'admin' ? route('admin.users.index') : route('user.users.index') }}" class="flex gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email..."
                               class="border-gray-300 rounded-lg px-4 py-2.5 text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2.5 rounded-lg transition-colors font-medium">Cari</button>
                </form>
                {{-- Add Button --}}
                @if(auth()->user()->role === 'admin')
                <button @click="toggleModal('create')" class="bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah User
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->position }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $color = $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            {{-- Detail Button --}}
                            <button @click="toggleModal('detail', {{ $user->id }})" title="Detail"
                                    class="inline-flex items-center p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            @if(auth()->user()->role === 'admin')
                            {{-- Edit Button --}}
                            <button @click="toggleModal('edit', {{ $user->id }})" title="Edit"
                                    class="inline-flex items-center p-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            {{-- Delete Button --}}
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" title="Hapus" onclick="confirmDelete(this.closest('form'), '{{ $user->name }}')"
                                        class="inline-flex items-center p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    {{-- DETAIL MODAL --}}
                    <div x-show="detailModals[{{ $user->id }}]" x-cloak 
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                        <div @click.away="toggleModal('detail', {{ $user->id }})" 
                             class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900">Detail User</h2>
                                <button @click="toggleModal('detail', {{ $user->id }})" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="px-6 py-6 overflow-y-auto modal-scroll flex-1">
                                <div class="text-center mb-6">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-20 h-20 object-cover rounded-full mx-auto">
                                    @else
                                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    @if($user->position)
                                        <p class="text-sm text-blue-600 mt-1">{{ $user->position }}</p>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">NIP</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->nip ?? '-' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">No. Telepon</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->phone ?? '-' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->gender === 'L' ? 'Laki-laki' : ($user->gender === 'P' ? 'Perempuan' : '-') }}</dd>
                                    </div>
                                    @if($user->address)
                                    <div class="bg-gray-50 rounded-lg p-4 col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->address }}</dd>
                                    </div>
                                    @endif
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Terdaftar</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Update Terakhir</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                                <button @click="toggleModal('detail', {{ $user->id }})" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- EDIT MODAL --}}
                    @if(auth()->user()->role === 'admin')
                    <div x-show="editModals[{{ $user->id }}]" x-cloak 
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                        <div @click.away="toggleModal('edit', {{ $user->id }})" 
                             class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900">Edit User</h2>
                                <button @click="toggleModal('edit', {{ $user->id }})" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="px-6 py-4 overflow-y-auto modal-scroll flex-1">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                                            <input type="text" name="name" value="{{ $user->name }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                                            <input type="text" name="nip" value="{{ $user->nip }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan NIP">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                            <input type="email" name="email" value="{{ $user->email }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                            <input type="text" name="phone" value="{{ $user->phone }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nomor telepon">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                            <select name="gender" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="L" @selected($user->gender==='L')>Laki-laki</option>
                                                <option value="P" @selected($user->gender==='P')>Perempuan</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                            <select name="position" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Jabatan</option>
                                                <option value="Kepala Sekolah" @selected($user->position==='Kepala Sekolah')>Kepala Sekolah</option>
                                                <option value="Wakil Kepala Sekolah" @selected($user->position==='Wakil Kepala Sekolah')>Wakil Kepala Sekolah</option>
                                                <option value="Guru" @selected($user->position==='Guru')>Guru</option>
                                                <option value="Staff" @selected($user->position==='Staff')>Staff</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                            <select name="role" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                                <option value="admin" @selected($user->role==='admin')>Admin</option>
                                                <option value="user" @selected($user->role==='user')>User</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-xs text-gray-500">(kosongkan jika tidak diubah)</span></label>
                                            <input type="password" name="password" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Password baru">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                            <textarea name="address" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan alamat" rows="2">{{ $user->address }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                                            @if($user->photo)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-20 h-20 object-cover rounded-lg">
                                                </div>
                                            @endif
                                            <input type="file" name="photo" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Max: 2MB</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end space-x-3 pt-4">
                                        <button type="button" @click="toggleModal('edit', {{ $user->id }})" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data user</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if(isset($users) && method_exists($users, 'links'))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">{{ $users->links() }}</div>
    @endif

    {{-- CREATE MODAL --}}
    @if(auth()->user()->role === 'admin')
    <div x-show="openCreate" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.away="toggleModal('create')" class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Tambah User</h2>
            </div>
            <div class="px-6 py-4 overflow-y-auto modal-scroll flex-1">
                <form method="POST" action="{{ route('admin.users.store') }}" id="createForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan NIP">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan email" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nomor telepon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select name="gender" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" @selected(old('gender')==='L')>Laki-laki</option>
                                <option value="P" @selected(old('gender')==='P')>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                            <select name="position" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Jabatan</option>
                                <option value="Kepala Sekolah" @selected(old('position')==='Kepala Sekolah')>Kepala Sekolah</option>
                                <option value="Wakil Kepala Sekolah" @selected(old('position')==='Wakil Kepala Sekolah')>Wakil Kepala Sekolah</option>
                                <option value="Guru" @selected(old('position')==='Guru')>Guru</option>
                                <option value="Staff" @selected(old('position')==='Staff')>Staff</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select name="role" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" @selected(old('role')==='admin')>Admin</option>
                                <option value="user" @selected(old('role')==='user')>User</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="address" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan alamat" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan konfirmasi password" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                            <input type="file" name="photo" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Max: 2MB</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button @click="toggleModal('create')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">Batal</button>
                <button type="submit" form="createForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
[x-cloak] { display: none !important; }
.modal-scroll::-webkit-scrollbar { width: 6px; }
.modal-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
.modal-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.modal-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection