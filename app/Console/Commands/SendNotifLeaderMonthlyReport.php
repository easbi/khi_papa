<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMonthlyNotiftoLeaderJob;
use App\Helpers\DateHelper;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use DB;

class SendNotifLeaderMonthlyReport extends Command
{
    protected $signature = 'send-notif-leader-monthly-report';
    protected $description = 'Send monthly KHI activity report to supervisor';

    public function handle()
    {
        $lastMonth = Carbon::now()->subMonth();
        $monthName = $lastMonth->translatedFormat('F Y');
        $startDate = $lastMonth->startOfMonth()->format('Y-m-d');
        $endDate = $lastMonth->endOfMonth()->format('Y-m-d');
        $workingDays = $this->getWorkingDays($lastMonth);

        // Ambil nomor HP pimpinan dari tabel users id = 1 atau 2
        $supervisor = User::find(2);
        $supervisorPhone = $supervisor ? $supervisor->no_hp : env('SUPERVISOR_PHONE', '6285265513571');

        // Hitung statistik KHI
        $stats = $this->getKHIStats($startDate, $endDate, $workingDays);

        $message = "📊 *Laporan Monitoring KBI Bulanan*\n\n"
            . "📅 Periode: {$monthName}\n"
            . "📆 Hari Kerja: {$stats['actual_working_days']} hari\n"
            . "👥 Total Pegawai: {$stats['total_employees']} orang\n\n"
            . "*📈 STATISTIK PENGISIAN KHI:*\n"
            . "✅ Rajin (≥80%): {$stats['rajin']} orang\n"
            . "❌ Kurang (<80%): {$stats['kurang']} orang\n\n";

        if (!empty($stats['kurang_rajin'])) {
            $message .= "*🚨 PEGAWAI YANG PERLU PERHATIAN:*\n";
            foreach ($stats['kurang_rajin'] as $pegawai) {
                $message .= "• {$pegawai['nama']} - {$pegawai['persentase']}%\n";
            }
            $message .= "\n";
        }

        $message .= "📋 Rata- rata Persentase Keaktifan Pengisian KHI : {$stats['rata_rata']}%\n\n"
            . "📊 Monitoring lengkap dapat di akses di : https://sipalink.id/khi/public/act/monitoring\n\n"
            . "_ Notifikasi Laporan ini secara otomatis sistem KHI di awal bulan untuk pimpinan_";

        $details = [
            'message' => $message,
            'no_hp' => $supervisorPhone,
        ];

        dispatch(new SendMonthlyNotiftoLeaderJob($details));

        $this->info('Monthly KHI report queued successfully');
        return Command::SUCCESS;
    }

    private function getKHIStats($startDate, $endDate, $workingDays)
    {
        $bulan = Carbon::parse($startDate)->month;
        $tahun = Carbon::parse($startDate)->year;

        // Ambil semua pegawai kecuali id tertentu
        $employees = User::whereNotIn('id', [2, 12, 26, 28, 29, 30])->get();
        $totalEmployees = $employees->count();

        $rajin = 0;
        $kurang = 0;
        $kurangRajin = [];
        $totalPersentase = 0;

        // Pakai hari kerja yang sudah dihitung
        $actualWorkingDays = $workingDays;

        foreach ($employees as $employee) {
            // Hitung jumlah hari unik yang diisi pegawai
            $inputDays = DB::table('daily_activity')
                ->where('nip', $employee->nip)
                ->whereMonth('tgl', $bulan)
                ->whereYear('tgl', $tahun)
                ->distinct()
                ->count(DB::raw('DATE(tgl)'));

            $persentase = $actualWorkingDays > 0 ? round(($inputDays / $actualWorkingDays) * 100, 1) : 0;
            $totalPersentase += $persentase;

            if ($persentase >= 80) {
                $rajin++;
            } else {
                $kurang++;
                $kurangRajin[] = [
                    'nama' => $employee->fullname ?? $employee->name ?? $employee->nip,
                    'nip' => $employee->nip,
                    'persentase' => $persentase,
                    'hari_isi' => $inputDays,
                    'hari_kerja' => $actualWorkingDays
                ];
            }
        }

        // Urutkan yang kurang rajin berdasarkan persentase terendah
        usort($kurangRajin, function($a, $b) {
            return $a['persentase'] <=> $b['persentase'];
        });

        $rataRata = $totalEmployees > 0 ? round($totalPersentase / $totalEmployees, 1) : 0;

        return [
            'total_employees' => $totalEmployees,
            'rajin' => $rajin,
            'kurang' => $kurang,
            'kurang_rajin' => $kurangRajin,
            'rata_rata' => $rataRata,
            'actual_working_days' => $actualWorkingDays
        ];
    }

    private function getWorkingDays($month)
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $workingDays = 0;

        while ($start->lte($end)) {
            // Skip Sabtu (6) dan Minggu (0)
            if (!in_array($start->dayOfWeek, [0, 6])) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }
}
