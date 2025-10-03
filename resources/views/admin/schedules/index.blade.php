@extends('layouts.app')

@section('title', 'Manajemen Jadwal')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Manajemen Jadwal
        </h2>

        <!-- Filter and Search -->
        <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
            <h4 class="mb-4 font-semibold text-gray-800">Filter & Pencarian</h4>
            <form action="{{ route('admin.schedules.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pengajar</label>
                    <select id="user_id" name="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Semua Pengajar</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->nip ? '(' . $user->nip . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Cari mata pelajaran atau pengajar...">
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-filter mr-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="flex justify-end mb-4">
            <a href="{{ route('admin.schedules.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Jadwal Baru
            </a>
        </div>

        <!-- Data Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-100 border-b">
                            <th class="px-4 py-3">Pengajar</th>
                            <th class="px-4 py-3">Mata Pelajaran</th>
                            <th class="px-4 py-3">Hari</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Ruang</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($schedules as $schedule)
                            <tr class="text-gray-700 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($schedule->user->photo)
                                            <div class="h-8 w-8 mr-3 rounded-full overflow-hidden">
                                                <img src="{{ asset('storage/'.$schedule->user->photo) }}" alt="{{ $schedule->user->name }}" class="h-full w-full object-cover">
                                            </div>
                                        @else
                                            <div class="h-8 w-8 mr-3 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold">{{ $schedule->user->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $schedule->user->nip ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $schedule->subject->name }}</div>
                                    @if($schedule->subject->code)
                                        <div class="text-xs text-gray-600">Kode: {{ $schedule->subject->code }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $schedule->day }}</td>
                                <td class="px-4 py-3">{{ $schedule->time_range }}</td>
                                <td class="px-4 py-3">{{ $schedule->classroom ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($schedule->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <p class="mt-2">Belum ada jadwal yang ditemukan</p>
                                        <a href="{{ route('admin.schedules.create') }}" class="mt-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                            <i class="fas fa-plus mr-1"></i>Tambah Jadwal
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white border-t">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
@endsection