@extends('layouts.app')

@section('title', 'Jadwal Saya')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Jadwal Saya
        </h2>
        
        <!-- Tab Navigation -->
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                @foreach($days as $day)
                    <li class="mr-2">
                        <a href="{{ route('user.schedules.index', ['day' => $day]) }}" class="inline-block p-4 {{ $selectedDay == $day ? 'text-blue-600 border-b-2 border-blue-600' : 'border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            {{ $day }}
                            @if(isset($scheduleCounts[$day]) && $scheduleCounts[$day] > 0)
                                <span class="ml-1 bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $scheduleCounts[$day] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
                <li class="ml-auto">
                    <a href="{{ route('user.schedules.calendar') }}" class="inline-block p-4 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-calendar-alt mr-1"></i> Lihat Kalender Mingguan
                    </a>
                </li>
            </ul>
        </div>

        @if($schedules->count() > 0)
            <!-- Schedule Cards -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
                @foreach($schedules as $schedule)
                    <div class="p-4 bg-white rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-lg text-gray-700">{{ $schedule->subject->name }}</h3>
                            @if($schedule->subject->code)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $schedule->subject->code }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <div class="flex items-center text-sm mb-1">
                                <i class="fas fa-clock mr-2 text-gray-500"></i>
                                <span>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</span>
                            </div>
                            @if($schedule->classroom)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                                    <span>{{ $schedule->classroom }}</span>
                                </div>
                            @endif
                        </div>
                        @if($schedule->subject->description)
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($schedule->subject->description, 100) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <div class="inline-flex rounded-full bg-yellow-100 p-4">
                    <div class="rounded-full bg-yellow-200 p-2">
                        <svg class="h-6 w-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-gray-800">Tidak ada jadwal untuk hari {{ $selectedDay }}</h3>
                <p class="mt-2 text-gray-600">Anda tidak memiliki jadwal mengajar pada hari ini.</p>
            </div>
        @endif
    </div>
@endsection