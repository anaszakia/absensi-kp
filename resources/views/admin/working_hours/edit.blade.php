@extends('layouts.app')
@section('title', 'Edit Jam Kerja')

@section('content')
<div class="space-y-6">
    {{-- HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Jam Kerja</h1>
                <p class="text-sm text-gray-500 mt-1">Ubah pengaturan jam kerja untuk absensi</p>
            </div>
            <div>
                <a href="{{ route('admin.working-hours.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.working-hours.update', $workingHour->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6">
                {{-- Nama --}}
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jam Kerja</label>
                    <input type="text" name="nama" id="nama" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                        value="{{ $workingHour->nama }}" placeholder="Contoh: Shift Pagi, Jam Normal, dll" required>
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Jam Masuk --}}
                    <div>
                        <label for="jam_masuk" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="jam_masuk" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            value="{{ substr($workingHour->jam_masuk, 0, 5) }}" required>
                        @error('jam_masuk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jam Pulang --}}
                    <div>
                        <label for="jam_pulang" class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="jam_pulang" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            value="{{ substr($workingHour->jam_pulang, 0, 5) }}" required>
                        @error('jam_pulang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.working-hours.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
