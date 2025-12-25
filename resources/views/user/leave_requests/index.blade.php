@extends('layouts.app')

@section('title', 'Daftar Pengajuan Ijin')

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-wrap items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Pengajuan Ijin</h3>
                    <div class="flex items-center space-x-2 mt-2 sm:mt-0">
                        <a href="{{ route('user.leave-requests.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Ajukan Ijin Baru
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @php
            $pending = $leaveRequests->where('status', 'pending')->count();
            $approved = $leaveRequests->where('status', 'approved')->count();
            $rejected = $leaveRequests->where('status', 'rejected')->count();
        @endphp
        
        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 mr-3 bg-yellow-100 rounded-full text-yellow-500">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                    <p class="text-xl font-bold">{{ $pending }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 mr-3 bg-green-100 rounded-full text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-xl font-bold">{{ $approved }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 mr-3 bg-red-100 rounded-full text-red-500">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ditolak</p>
                    <p class="text-xl font-bold">{{ $rejected }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Bukti</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Catatan Admin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($leaveRequests as $leave)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ $leave->date->format('d/m/Y') }}
                                <p class="text-xs text-gray-500">{{ $leave->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-xs">
                                    <p class="text-sm text-gray-900 truncate">{{ Str::limit($leave->reason, 50) }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($leave->attachment)
                                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-md hover:bg-blue-200">
                                        <i class="fas fa-file-download mr-1"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($leave->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($leave->status == 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                                
                                @if($leave->status != 'pending')
                                    <p class="text-xs text-gray-500 mt-1">{{ $leave->approved_at ? $leave->approved_at->format('d/m/Y H:i') : '-' }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($leave->admin_remarks)
                                    <p class="text-sm text-gray-900">{{ Str::limit($leave->admin_remarks, 50) }}</p>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm font-medium">Belum ada pengajuan ijin</p>
                                    <a href="{{ route('user.leave-requests.create') }}" class="mt-3 inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                        <i class="fas fa-plus-circle mr-1"></i> Ajukan Ijin
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $leaveRequests->links() }}
        </div>
    </div>
@endsection