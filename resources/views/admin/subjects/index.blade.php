@extends('layouts.app')

@section('title', 'Manajemen Mata Pelajaran')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Manajemen Mata Pelajaran
        </h2>

        <!-- Search and Create Button -->
        <div class="flex justify-between mb-6">
            <div class="w-1/2">
                <form action="{{ route('admin.subjects.index') }}" method="GET" class="flex">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Cari mata pelajaran...">
                    </div>
                    <button type="submit" class="ml-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        Cari
                    </button>
                </form>
            </div>
            <div>
                <a href="{{ route('admin.subjects.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i>Tambah Mata Pelajaran
                </a>
            </div>
        </div>

        <!-- Data Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-100 border-b">
                            <th class="px-4 py-3">Nama Mata Pelajaran</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Total Jadwal</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($subjects as $subject)
                            <tr class="text-gray-700 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $subject->name }}</div>
                                    @if($subject->description)
                                        <div class="text-xs text-gray-600">{{ Str::limit($subject->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $subject->code ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $subject->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($subject->is_active)
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
                                    <span class="font-medium">{{ $subject->schedules->count() }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.subjects.show', $subject->id) }}" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($subject->schedules->count() === 0)
                                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button disabled class="px-2 py-1 text-xs bg-gray-400 text-white rounded cursor-not-allowed" title="Tidak dapat dihapus (memiliki jadwal)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2">Tidak ada mata pelajaran yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-white border-t">
                {{ $subjects->links() }}
            </div>
        </div>
    </div>
@endsection