<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        // Ambil filter dari request
        $userId = $this->request->input('user_id');
        $startDate = $this->request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $this->request->input('end_date', now()->format('Y-m-d'));
        $status = $this->request->input('status');

        // Query dasar
        $query = Attendance::with(['user', 'userSchedule.subject'])
            ->whereBetween('date', [$startDate, $endDate])
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest('date');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIP',
            'Nama',
            'Mata Pelajaran',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Jadwal Mulai',
            'Jadwal Selesai',
            'Keterangan'
        ];
    }

    public function map($attendance): array
    {
        $subjectName = $attendance->userSchedule->subject->name ?? 'Tidak ada jadwal';
        $startTime = $attendance->userSchedule ? \Carbon\Carbon::parse($attendance->userSchedule->start_time)->format('H:i') : '-';
        $endTime = $attendance->userSchedule ? \Carbon\Carbon::parse($attendance->userSchedule->end_time)->format('H:i') : '-';
        
        return [
            $attendance->date->format('d/m/Y'),
            $attendance->user->nip ?? '-',
            $attendance->user->name,
            $subjectName,
            $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-',
            $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-',
            $this->formatStatus($attendance->status),
            $startTime,
            $endTime,
            $attendance->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }

    private function formatStatus($status)
    {
        switch ($status) {
            case 'tepat_waktu':
                return 'Tepat Waktu';
            case 'terlambat':
                return 'Terlambat';
            case 'ijin':
                return 'Ijin';
            case 'tidak_masuk':
                return 'Tidak Masuk';
            default:
                return ucfirst($status);
        }
    }
}