@extends('layouts.app')

@section('title', 'Kalender Jadwal')

@section('content')
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center my-6">
            <h2 class="text-2xl font-semibold text-gray-700">
                Kalender Jadwal Mingguan
            </h2>
            <a href="{{ route('user.schedules.index') }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-list mr-1"></i>Lihat dalam Daftar
            </a>
        </div>
        
        <div class="w-full overflow-hidden rounded-lg shadow-md mb-6">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                            <th class="px-4 py-3 w-32">Jam</th>
                            @foreach($days as $day)
                                <th class="px-4 py-3">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $startHour = 7;
                            $endHour = 17;
                            $intervals = ['00', '30'];
                        @endphp

                        @for($hour = $startHour; $hour <= $endHour; $hour++)
                            @foreach($intervals as $minute)
                                @php
                                    $timeSlot = sprintf('%02d:%s', $hour, $minute);
                                    $nextTimeSlot = $minute === '00' ? sprintf('%02d:30', $hour) : sprintf('%02d:00', $hour + 1);
                                @endphp
                                <tr class="text-gray-700 border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-xs font-medium text-gray-600">
                                        {{ $timeSlot }}
                                    </td>

                                    @foreach($days as $day)
                                        <td class="px-4 py-3">
                                            @php
                                                $schedulesForTimeSlot = $schedulesByDay[$day]->filter(function($schedule) use ($timeSlot, $nextTimeSlot) {
                                                    $start = substr($schedule->start_time, 0, 5);
                                                    $end = substr($schedule->end_time, 0, 5);
                                                    return ($start <= $timeSlot && $end > $timeSlot) || 
                                                           ($start >= $timeSlot && $start < $nextTimeSlot);
                                                });
                                            @endphp

                                            @if($schedulesForTimeSlot->count() > 0)
                                                @foreach($schedulesForTimeSlot as $schedule)
                                                    @php
                                                        $start = substr($schedule->start_time, 0, 5);
                                                        $isStart = $start == $timeSlot;
                                                    @endphp
                                                    <div class="px-2 py-1 {{ $isStart ? 'mt-0' : '-mt-1' }} text-xs rounded bg-blue-100 border-l-4 border-blue-500">
                                                        @if($isStart)
                                                            <div class="font-medium">{{ $schedule->subject->name }}</div>
                                                            <div class="flex justify-between">
                                                                <span>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</span>
                                                                @if($schedule->classroom)
                                                                    <span class="bg-gray-200 px-1 rounded">{{ $schedule->classroom }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection