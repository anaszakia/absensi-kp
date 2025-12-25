@extends('layouts.app')

@section('title', 'Manajemen Pengajuan Ijin')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Manajemen Pengajuan Ijin
        </h2>

        <!-- Filter Card -->
        <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
            <h4 class="mb-4 font-semibold text-gray-800">Filter Data</h4>
            <form action="{{ route('admin.leave-requests.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-filter mr-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-100 border-b">
                            <th class="px-4 py-3">Pegawai</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Alasan</th>
                            <th class="px-4 py-3">Bukti</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($leaveRequests as $leave)
                            <tr class="text-gray-700 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($leave->user->photo)
                                            <div class="h-8 w-8 mr-3 rounded-full overflow-hidden">
                                                <img src="{{ asset('storage/'.$leave->user->photo) }}" alt="User Photo" class="h-full w-full object-cover">
                                            </div>
                                        @else
                                            <div class="h-8 w-8 mr-3 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold">{{ $leave->user->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $leave->user->nip ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $leave->date->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-600">Diajukan: {{ $leave->created_at->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <p class="text-sm truncate">{{ Str::limit($leave->reason, 50) }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($leave->attachment)
                                        <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200">
                                            <i class="fas fa-file mr-1"></i>Lihat
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($leave->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Menunggu
                                        </span>
                                    @elseif ($leave->status == 'approved')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($leave->status == 'pending')
                                        <div class="flex space-x-2">
                                            <button onclick="showApproveModal('{{ $leave->id }}')" class="px-2 py-1 text-xs font-medium rounded bg-green-600 text-white hover:bg-green-700">
                                                <i class="fas fa-check mr-1"></i>Setujui
                                            </button>
                                            <button onclick="showRejectModal('{{ $leave->id }}')" class="px-2 py-1 text-xs font-medium rounded bg-red-600 text-white hover:bg-red-700">
                                                <i class="fas fa-times mr-1"></i>Tolak
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">
                                            @if($leave->approved_by)
                                                <p>Oleh: {{ $leave->approver->name }}</p>
                                            @endif
                                            <p>{{ $leave->approved_at ? $leave->approved_at->format('d/m/Y H:i') : '-' }}</p>
                                            @if($leave->admin_remarks)
                                                <p>Catatan: {{ Str::limit($leave->admin_remarks, 30) }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2">Tidak ada pengajuan ijin yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white border-t">
                {{ $leaveRequests->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    <div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity" onclick="closeModals()"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
                <form id="approveForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <div class="px-6 py-4">
                        <div class="text-lg font-medium text-gray-900 mb-2">Setujui Pengajuan Ijin</div>
                        <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menyetujui pengajuan ijin ini?</p>
                        
                        <div>
                            <label for="admin_remarks" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea id="admin_remarks" name="admin_remarks" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button type="button" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300" onclick="closeModals()">Batal</button>
                        <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity" onclick="closeModals()"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
                <form id="rejectForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <div class="px-6 py-4">
                        <div class="text-lg font-medium text-gray-900 mb-2">Tolak Pengajuan Ijin</div>
                        <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menolak pengajuan ijin ini?</p>
                        
                        <div>
                            <label for="admin_remarks_reject" class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan</label>
                            <textarea id="admin_remarks_reject" name="admin_remarks" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button type="button" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300" onclick="closeModals()">Batal</button>
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function showApproveModal(id) {
        document.getElementById('approveForm').action = "{{ route('admin.leave-requests.action', ':id') }}".replace(':id', id);
        document.getElementById('approveModal').classList.remove('hidden');
    }
    
    function showRejectModal(id) {
        document.getElementById('rejectForm').action = "{{ route('admin.leave-requests.action', ':id') }}".replace(':id', id);
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    
    function closeModals() {
        document.getElementById('approveModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection