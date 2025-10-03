@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Detail Jadwal
        </h2>

        <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">
                    {{ $schedule->subject->name }}
                    @if($schedule->subject->code)
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $schedule->subject->code }}</span>
                    @endif
                </h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $schedule->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Pengajar</h4>
                    <div class="flex items-center">
                        @if($schedule->user->photo)
                            <div class="h-10 w-10 mr-3 rounded-full overflow-hidden">
                                <img src="{{ asset('storage/'.$schedule->user->photo) }}" alt="{{ $schedule->user->name }}" class="h-full w-full object-cover">
                            </div>
                        @else
                            <div class="h-10 w-10 mr-3 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold">{{ $schedule->user->name }}</p>
                            <p class="text-xs text-gray-600">{{ $schedule->user->nip ?? 'NIP: -' }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Jadwal</h4>
                    <p class="font-semibold">{{ $schedule->day }}, {{ $schedule->time_range }}</p>
                    <p class="text-sm text-gray-600">
                        @if($schedule->classroom)
                            Ruang: {{ $schedule->classroom }}
                        @else
                            Ruang: -
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="px-3 py-2 mr-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="inline mr-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </button>
                </form>
                <a href="{{ route('admin.schedules.index') }}" class="px-3 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
@endsection