@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Detail Mata Pelajaran
        </h2>

        <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">
                    {{ $subject->name }} 
                    @if($subject->code)
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $subject->code }}</span>
                    @endif
                </h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $subject->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
            
            @if($subject->description)
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700">Deskripsi</h4>
                    <p class="mt-1 text-gray-600">{{ $subject->description }}</p>
                </div>
            @endif
            
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="px-3 py-2 mr-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="px-3 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Jadwal Mengajar</h3>
            
            <div class="w-full overflow-hidden rounded-lg shadow-md">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-100 border-b">
                                <th class="px-4 py-3">Pengajar</th>
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
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <p class="mt-2">Belum ada jadwal untuk mata pelajaran ini</p>
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
            </div>
        </div>
    </div>
@endsection