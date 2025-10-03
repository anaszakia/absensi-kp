@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 md:p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mt-20 -mr-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full -mb-10 -ml-10"></div>
            <div class="relative z-10 flex items-center">
                <!-- User Avatar/Photo -->
                <div class="mr-4 md:mr-6 flex-shrink-0">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/'.auth()->user()->photo) }}" alt="User Photo" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 border-white shadow-md object-cover">
                    @else
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-white bg-opacity-25 border-2 border-white shadow-md flex items-center justify-center">
                            <i class="fas fa-user text-white text-3xl"></i>
                        </div>
                    @endif
                </div>
                <!-- User Info -->
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
                    <p class="text-blue-100 text-base md:text-lg">{{ now()->format('l, d F Y') }}</p>
                    <div class="mt-4">
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 inline-flex items-center">
                            <span class="text-sm font-semibold">Status absensi hari ini:</span>
                            @if($todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out)
                                <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-medium">Selesai</span>
                            @elseif($todayAttendance && $todayAttendance->check_in)
                                <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full font-medium">Absen Masuk</span>
                            @else
                                <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full font-medium">Belum Absen</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Time Info -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-md p-5 border border-blue-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                    <i class="fas fa-calendar-day text-white text-lg"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-blue-600 mb-1">Tanggal</p>
                    <p class="text-lg font-bold text-gray-900 truncate">{{ now()->format('d F Y') }}</p>
                </div>
            </div>
        </div>
                
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-md p-5 border border-green-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                    <i class="fas fa-clock text-white text-lg"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-green-600 mb-1">Waktu Sekarang</p>
                    <p class="text-lg font-bold text-gray-900" id="current-time">{{ now()->format('H:i:s') }}</p>
                </div>
            </div>
        </div>
                
        @if($workingHours->count() > 0)
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-md p-5 border border-purple-200 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                    <i class="fas fa-business-time text-white text-lg"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-purple-600 mb-1">Shift Anda</p>
                    <p class="text-lg font-bold text-gray-900 truncate">{{ $defaultWorkingHour->nama }}</p>
                    <p class="text-xs font-medium text-gray-600">{{ $defaultWorkingHour->jam_masuk }} - {{ $defaultWorkingHour->jam_pulang }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Attendance Status Card -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Kehadiran Hari Ini</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Check-in Status Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="h-1.5 bg-gradient-to-r from-green-400 to-blue-500"></div>
                <div class="p-5">
                    @if($todayAttendance && $todayAttendance->check_in)
                        @if($todayAttendance->status == 'tepat_waktu')
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                    <i class="fas fa-check text-white text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-semibold text-gray-900 mb-2">Absen Masuk</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-md">
                                            Tepat Waktu
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @elseif($todayAttendance->status == 'terlambat')
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                    <i class="fas fa-clock text-white text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-semibold text-gray-900 mb-2">Absen Masuk</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-md">
                                            Terlambat
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @elseif($todayAttendance->status == 'ijin')
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                    <i class="fas fa-calendar-check text-white text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-semibold text-gray-900 mb-2">Status Absensi</p>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-md inline-block mb-2">
                                        Ijin
                                    </span>
                                    <p class="text-xs text-gray-600 break-words">{{ $todayAttendance->notes }}</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                <i class="fas fa-user-clock text-white text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-semibold text-gray-900 mb-1">Belum Absen</p>
                                <p class="text-sm text-gray-600">Silakan lakukan absen dengan mengklik tombol di bawah</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Check-out Status Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="h-1.5 bg-gradient-to-r from-indigo-400 to-purple-500"></div>
                <div class="p-5">
                    @if($todayAttendance && $todayAttendance->check_out)
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                <i class="fas fa-sign-out-alt text-white text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-semibold text-gray-900 mb-2">Absen Pulang</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-md">
                                        Selesai
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @elseif($todayAttendance && $todayAttendance->check_in)
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                <i class="fas fa-hourglass-half text-white text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-semibold text-gray-900 mb-1">Absen Pulang</p>
                                <p class="text-sm text-gray-600">Belum absen pulang</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                                <i class="fas fa-minus-circle text-white text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-semibold text-gray-500 mb-1">Absen Pulang</p>
                                <p class="text-sm text-gray-400">Belum tersedia</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Action Buttons -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(!$todayAttendance || !$todayAttendance->check_in)
                <!-- Check In Form -->
                <div class="w-full">
                    <form action="{{ route('user.attendance.check-in') }}" method="POST" class="bg-white p-5 rounded-xl shadow-md border border-gray-200">
                        @csrf
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3 shadow-sm flex-shrink-0">
                                <i class="fas fa-sign-in-alt text-white"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Absen Masuk</h4>
                        </div>
                        
                        @if($workingHours->count() > 0)
                            <div class="mb-4 bg-green-50 p-3 rounded-lg border border-green-200">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Jam Kerja: <span class="font-medium">{{ $defaultWorkingHour->nama }}</span>
                                </p>
                                <p class="text-xs text-green-700 mt-1">
                                    {{ $defaultWorkingHour->jam_masuk }} - {{ $defaultWorkingHour->jam_pulang }}
                                </p>
                                <input type="hidden" name="working_hour_id" value="{{ $defaultWorkingHour->id }}">
                            </div>
                            <input type="hidden" name="notes" value="-">
                            <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-lg shadow hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all font-medium text-sm">
                                <i class="fas fa-sign-in-alt mr-2"></i>Absen Masuk Sekarang
                            </button>
                        @else
                            <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl mb-2"></i>
                                <p class="text-sm text-red-600 font-medium">Anda belum memiliki jadwal jam kerja. Silakan hubungi admin.</p>
                            </div>
                        @endif
                    </form>
                </div>
            @endif

            @if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                <!-- Check Out Form -->
                <div class="w-full">
                    <form action="{{ route('user.attendance.check-out') }}" method="POST" class="bg-white p-5 rounded-xl shadow-md border border-gray-200">
                        @csrf
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center mr-3 shadow-sm flex-shrink-0">
                                <i class="fas fa-sign-out-alt text-white"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Absen Pulang</h4>
                        </div>
                        
                        <div class="mb-4 bg-indigo-50 p-3 rounded-lg border border-indigo-200">
                            <p class="text-sm text-indigo-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jam Kerja: <span class="font-medium">{{ $todayAttendance->workingHour->nama }}</span>
                            </p>
                            <p class="text-xs text-indigo-700 mt-1">
                                {{ $todayAttendance->workingHour->jam_masuk }} - {{ $todayAttendance->workingHour->jam_pulang }}
                            </p>
                        </div>
                        <input type="hidden" name="notes" value="-">
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-500 to-indigo-600 text-white py-3 px-4 rounded-lg shadow hover:from-indigo-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-all font-medium text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>Absen Pulang Sekarang
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Attendance History -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Absensi 7 Hari Terakhir</h3>
            <a href="{{ route('user.attendance.history') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                Lihat Semua 
                <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pulang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentAttendances as $attendance)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                            <span class="text-xs font-bold text-blue-600">{{ \Carbon\Carbon::parse($attendance->date)->format('d') }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->date)->format('M Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $attendance->workingHour->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $attendance->workingHour->jam_masuk }} - {{ $attendance->workingHour->jam_pulang }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($attendance->check_in)
                                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($attendance->check_out)
                                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($attendance->status == 'tepat_waktu')
                                        <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1 text-xs"></i>Tepat Waktu
                                        </span>
                                    @elseif($attendance->status == 'terlambat')
                                        <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>Terlambat
                                        </span>
                                    @elseif($attendance->status == 'ijin')
                                        <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-info-circle mr-1 text-xs"></i>Ijin
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times mr-1 text-xs"></i>Tidak Masuk
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-sm">Belum ada data absensi</p>
                                        <p class="text-xs text-gray-400 mt-1">Data akan muncul setelah Anda melakukan absensi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Function to update current time every second
    function updateCurrentTime() {
        const currentTimeElement = document.getElementById('current-time');
        if (currentTimeElement) {
            setInterval(() => {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                currentTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
            }, 1000);
        }
    }

    // Ensure forms are properly submitted with POST method
    function setupForms() {
        // Find all forms in the page
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                // Check if there's a valid CSRF token
                const csrfToken = this.querySelector('input[name="_token"]');
                if (!csrfToken) {
                    event.preventDefault();
                    console.error('CSRF token not found in form');
                    alert('Error: Form validation failed. Please reload the page and try again.');
                }
            });
        });
    }

    // Call the functions when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateCurrentTime();
        setupForms();
    });
</script>
@endsection