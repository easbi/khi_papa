<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DailyReminder;
use App\Jobs\SendBirthdayReminderJob; 
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use DB;

class DispatchMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to send WA to clients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bulan = date('m');
        $tahun = date('Y');
        $users = DB::table('users')
            ->leftJoin('daily_activity', function ($join) use ($bulan, $tahun) {
                $join->on('users.nip', '=', 'daily_activity.nip')
                    ->whereMonth('daily_activity.tgl', '=', $bulan)
                    ->whereYear('daily_activity.tgl', '=', $tahun);
            })
            ->where('notification', '1')
            ->whereNotIn('users.nip', ['199111052014102001', '197612291999011001']) // Mengecualikan pegawai tertentu
            ->select(
                'users.nip',
                'users.no_hp',
                'users.fullname',
                DB::raw('COALESCE(COUNT(daily_activity.id), 0) as jumlah_pengisian')
            )
            ->groupBy('users.nip', 'users.fullname', 'users.no_hp')
            ->orderBy('jumlah_pengisian', 'desc')
            ->get()
            ->map(function ($user) use ($bulan, $tahun) {
                $today = Carbon::today();
                $datenow = $today->format('Y-m-d');

                // Hitung semua hari kerja dalam bulan ini (Senin - Jumat)
                $allWorkingDays = [];
                $startOfMonth = Carbon::create($tahun, $bulan)->startOfMonth();
                $endOfMonth = Carbon::create($tahun, $bulan)->endOfMonth();
                while ($startOfMonth <= $endOfMonth) {
                    if ($startOfMonth->isWeekday() && $startOfMonth <= $today) {
                        $allWorkingDays[] = $startOfMonth->format('Y-m-d');
                    }
                    $startOfMonth->addDay();
                }

                // Ambil hari libur nasional yang jatuh di hari kerja
                $hariLibur = array_filter(
                    json_decode(file_get_contents("https://api-harilibur.netlify.app/api?month=$bulan&year=$tahun"), true),
                    fn($holiday) => (new \DateTime($holiday['holiday_date']))->format('N') <= 5 && $holiday['holiday_date'] <= $datenow
                );

                $holidayDates = array_map(fn($holiday) => $holiday['holiday_date'], $hariLibur);
                $workingDaysWithoutHolidays = array_diff($allWorkingDays, $holidayDates);

                // Ambil daftar hari yang sudah diisi user
                $filledDays = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('DATE(tgl) as date'))
                    ->pluck('date')
                    ->toArray();

                // Hitung hari yang tidak diisi
                $missedDays = array_diff($workingDaysWithoutHolidays, $filledDays);
                $user->missed_days = count($missedDays);

                // Format tanggal yang tidak diisi dalam bahasa Indonesia
                $user->missed_days_list = array_map(fn($date) => Carbon::parse($date)->isoFormat('dddd, DD MMMM YYYY'), $missedDays);

                // Hitung total aktivitas dalam bulan ini
                $totalActivities = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->count();

                // Ambil jumlah maksimal aktivitas untuk skala nilai
                $maxActivities = DB::table('daily_activity')
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('COUNT(id) as activity_count'))
                    ->groupBy('nip')
                    ->orderByDesc('activity_count')
                    ->limit(1)
                    ->value('activity_count') ?? 1; // Hindari pembagian nol

                // Skala skor pengisian dan aktivitas
                $maxWorkDays = count($workingDaysWithoutHolidays);
                $filledDaysScore = ($user->missed_days < $maxWorkDays) ? (($maxWorkDays - $user->missed_days) / $maxWorkDays) * 50 : 0;
                $activityScore = ($totalActivities / $maxActivities) * 50;

                // Skor akhir
                $user->score = $filledDaysScore + $activityScore;
                $user->maxWorkDays = $maxWorkDays;

                return $user;
            })
            ->sortByDesc('score') // Urutkan berdasarkan skor tertinggi
            ->values();

        foreach ($users as $user) {
            $TodayActivity = Activity::where('nip', $user->nip)
                ->where('tgl', Carbon::today())
                ->count();

            if ($TodayActivity == 0) {
                $details = [
                    'message' => "💬 *Notifikasi Aplikasi KHI* .\nSelamat Sore, {$user->fullname}. Anda terpantau tidak mengisi kegiatan kerja hari ini di link https://sipalink.id/khi/public/. " .
                                 "Saat ini Anda tercatat tidak mengisi selama {$user->missed_days} hari kerja tanpa mengisi kegiatan di aplikasi. " .
                                 "Yuk, #isiajadulu 📲",
                    'no_hp' => $user->no_hp
                ];
                $queue = new DailyReminder($details);
                dispatch($queue->delay(now()->addSeconds(10)));
            }
        }

        return Command::SUCCESS;

    }
}
