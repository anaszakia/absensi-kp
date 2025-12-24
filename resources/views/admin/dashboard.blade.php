@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white relative">
            <!-- Logo di pojok kanan -->
            <div class="absolute top-4 right-6">
                <img src="{{ asset('images/logo_azzuhdi.png') }}" alt="Logo Azzuhdi" class="h-14">
            </div>
            <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-blue-100">Kelola sistem dengan mudah melalui dashboard admin</p>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jumlah User</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500">Total seluruh user</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Absen Hari Ini -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Absen Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($absenHariIni) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500">Yang sudah absen</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Ijin Hari Ini -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ijin Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($ijinHariIni) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500">Yang sedang ijin</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-times text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Terlambat Hari Ini -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Terlambat Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($terlambatHariIni) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500">Yang datang terlambat</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">User Terbaru</h3>
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua</a>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentUsers as $user)
                    <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                {{ $user->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-300 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada user terbaru</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.audit.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua</a>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            @if($activity->action === 'Login') bg-green-100 @elseif($activity->action === 'Logout') bg-red-100 @else bg-blue-100 @endif">
                            @if($activity->action === 'Login')
                                <i class="fas fa-sign-in-alt text-green-600 text-xs"></i>
                            @elseif($activity->action === 'Logout')
                                <i class="fas fa-sign-out-alt text-red-600 text-xs"></i>
                            @else
                                <i class="fas fa-cog text-blue-600 text-xs"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $activity->user ? $activity->user->name : 'Unknown User' }}
                            </p>
                            <p class="text-sm text-gray-600">{{ $activity->action }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-300 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
