@extends('layouts.app')

@section('title', 'Ajukan Ijin')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-wrap items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Form Pengajuan Ijin</h3>
                    <div class="flex items-center space-x-2 mt-2 sm:mt-0">
                        <a href="{{ route('user.leave-requests.index') }}" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Daftar Pengajuan
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Form Pengajuan Ijin -->
            <div class="p-6">
                <form action="{{ route('user.leave-requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ijin *</label>
                            <input type="date" id="date" name="date" 
                                value="{{ old('date') ?: now()->format('Y-m-d') }}" 
                                min="{{ now()->format('Y-m-d') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('date') border-red-500 @enderror" required>
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="working_hour_id" class="block text-sm font-medium text-gray-700 mb-1">Jam Kerja *</label>
                            @if($workingHours->count() == 1)
                                <!-- Jika hanya ada satu jam kerja, otomatis pilih itu -->
                                <input type="hidden" name="working_hour_id" value="{{ $workingHours->first()->id }}">
                                <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2.5 flex items-center">
                                    <span>{{ $workingHours->first()->nama }} ({{ $workingHours->first()->jam_masuk }} - {{ $workingHours->first()->jam_pulang }})</span>
                                </div>
                            @elseif($workingHours->count() > 1)
                                <!-- Jika ada lebih dari satu jam kerja, pilih default dan tampilkan dropdown -->
                                <select id="working_hour_id" name="working_hour_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('working_hour_id') border-red-500 @enderror" required>
                                    @foreach($workingHours as $workingHour)
                                        <option value="{{ $workingHour->id }}" 
                                            {{ (old('working_hour_id') == $workingHour->id || ($loop->first && !old('working_hour_id'))) ? 'selected' : '' }}>
                                            {{ $workingHour->nama }} ({{ $workingHour->jam_masuk }} - {{ $workingHour->jam_pulang }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <!-- Jika tidak ada jam kerja -->
                                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 text-sm rounded-lg px-3 py-2.5">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Anda belum memiliki jam kerja. Silakan hubungi admin.
                                </div>
                            @endif
                            @error('working_hour_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Ijin *</label>
                            <textarea id="reason" name="reason" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('reason') border-red-500 @enderror" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">
                                Surat/Bukti Ijin
                                <span class="text-xs text-gray-500 font-normal">(opsional, format: PDF, JPG, PNG, maks. 2MB)</span>
                            </label>
                            <input type="file" id="attachment" name="attachment" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('attachment') border-red-500 @enderror">
                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <div class="flex items-center space-x-3">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i>Ajukan Ijin
                            </button>
                            <a href="{{ route('user.leave-requests.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection