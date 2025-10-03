@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Rekap Absensi
        </h2>

        <!-- Filter Card -->
        <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
            <h4 class="mb-4 font-semibold text-gray-800">Filter Data</h4>
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pegawai</label>
                    <select id="user_id" name="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Semua Pegawai</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->nip ? '(' . $user->nip . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ $status == $statusOption ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $statusOption)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-filter mr-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 mr-3 bg-green-100 rounded-full text-green-500">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Kehadiran</p>
                        <p class="text-xl font-bold">{{ $totalPresent }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 mr-3 bg-yellow-100 rounded-full text-yellow-500">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Keterlambatan</p>
                        <p class="text-xl font-bold">{{ $totalLate }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 mr-3 bg-blue-100 rounded-full text-blue-500">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Izin</p>
                        <p class="text-xl font-bold">{{ $totalLeave }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between mb-4">
            <div class="flex space-x-2">
                <form action="{{ route('admin.attendance.export') }}" method="POST">
                    @csrf
                    <!-- Hidden inputs to pass filter values -->
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </button>
                </form>
            </div>
            <div>
                <p class="text-sm text-gray-600">
                    Menampilkan {{ $attendances->count() }} dari {{ $attendances->total() }} data
                </p>
            </div>
        </div>

        <!-- Data Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-100 border-b">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">NIP</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jam Masuk</th>
                            <th class="px-4 py-3">Jam Pulang</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Jam Kerja</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($attendances as $attendance)
                            <tr class="text-gray-700 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $attendance->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $attendance->user->nip ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($attendance->user->photo)
                                            <div class="h-8 w-8 mr-3 rounded-full overflow-hidden">
                                                <img src="{{ asset('storage/'.$attendance->user->photo) }}" alt="User Photo" class="h-full w-full object-cover">
                                            </div>
                                        @else
                                            <div class="h-8 w-8 mr-3 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                        @endif
                                        {{ $attendance->user->name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($attendance->check_in)
                                        {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($attendance->check_out)
                                        {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($attendance->status == 'tepat_waktu')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Tepat Waktu
                                        </span>
                                    @elseif ($attendance->status == 'terlambat')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Terlambat
                                        </span>
                                    @elseif ($attendance->status == 'ijin')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Ijin
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Tidak Masuk
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm">{{ $attendance->workingHour->nama }}</span>
                                    <p class="text-xs text-gray-500">{{ $attendance->workingHour->jam_masuk }} - {{ $attendance->workingHour->jam_pulang }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2">Tidak ada data absensi yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white border-t">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
@endsection