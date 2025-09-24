<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DailyReminder;
use App\Helpers\DateHelper;
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
     */
    public function handle()
    {
        $bulan = date('m');
        $tahun = date('Y');
        $today = Carbon::today();

        // Ambil daftar user
        $users = DB::table('users')
            ->leftJoin('daily_activity', function ($join) use ($bulan, $tahun) {
                $join->on('users.nip', '=', 'daily_activity.nip')
                    ->whereMonth('daily_activity.tgl', '=', $bulan)
                    ->whereYear('daily_activity.tgl', '=', $tahun);
            })
            ->where('notification', '1')
            ->whereNotIn('users.nip', ['199111052014102001', '197612291999011001']) // pengecualian tertentu
            ->select(
                'users.nip',
                'users.no_hp',
                'users.fullname',
                DB::raw('COALESCE(COUNT(daily_activity.id), 0) as jumlah_pengisian')
            )
            ->groupBy('users.nip', 'users.fullname', 'users.no_hp')
            ->orderBy('jumlah_pengisian', 'desc')
            ->get()
            ->map(function ($user) use ($bulan, $tahun, $today) {
                $startOfMonth = Carbon::create($tahun, $bulan)->startOfMonth();
                $endOfMonth = Carbon::create($tahun, $bulan)->endOfMonth();

                // hari kerja (Senin–Jumat) tanpa libur nasional
                $workingDaysWithoutHolidays = DateHelper::getWorkingDaysWithoutHolidaysUntilToday($bulan, $tahun);

                // ambil hari yg sudah diisi
                $filledDays = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('DATE(tgl) as date'))
                    ->pluck('date')
                    ->toArray();

                // hitung missed days
                $missedDays = array_diff($workingDaysWithoutHolidays, $filledDays);
                $user->missed_days = count($missedDays);

                // format daftar missed days
                $user->missed_days_list = array_map(
                    fn($date) => Carbon::parse($date)->isoFormat('dddd, DD MMMM YYYY'),
                    $missedDays
                );

                // hitung total aktivitas bulan ini
                $totalActivities = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->count();

                // max aktivitas untuk skala
                $maxActivities = DB::table('daily_activity')
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('COUNT(id) as activity_count'))
                    ->groupBy('nip')
                    ->orderByDesc('activity_count')
                    ->limit(1)
                    ->value('activity_count') ?? 1;

                $maxWorkDays = count($workingDaysWithoutHolidays);

                // skor
                $filledDaysScore = ($user->missed_days < $maxWorkDays)
                    ? (($maxWorkDays - $user->missed_days) / $maxWorkDays) * 50
                    : 0;
                $activityScore = ($totalActivities / $maxActivities) * 50;

                $user->score = $filledDaysScore + $activityScore;
                $user->maxWorkDays = $maxWorkDays;

                return $user;
            })
            ->sortByDesc('score')
            ->values();

        // pool narasi + quotes
        $stories = [
            [
                'narrative' => "Bayangkan bila setiap aktivitas harian itu adalah potongan puzzle 🧩. Saat terangkai penuh, gambarnya indah. Sayangnya, ada :missed puzzle yang masih kosong di KHI Anda.",
                'quote' => '"Kolaborasi sejati terjadi ketika setiap individu melengkapi puzzle besar organisasi dengan kontribusi unik mereka." - Stephen Covey'
            ],
            [
                'narrative' => "Setiap hari kerja adalah satu lembar kisah 📖. Ada :missed lembar yang kosong di catatan KHI Anda.",
                'quote' => '"Pertumbuhan pribadi dimulai dari kebiasaan sederhana: menulis satu halaman progres setiap hari." - James Clear'
            ],
            [
                'narrative' => "Aktivitas harian adalah jejak langkah 👣. Saat ini ada :missed langkah yang hilang dari perjalanan Anda.",
                'quote' => '"Kesuksesan adalah hasil akumulasi dari ribuan langkah kecil yang konsisten." - Darren Hardy'
            ],
            [
                'narrative' => "Catatan aktivitas ibarat bintang 🌟. Ada :missed bintang yang belum bersinar di catatan KHI Anda.",
                'quote' => '"Pikiran kolektif yang terintegrasi akan menghasilkan cahaya yang lebih terang dari sekedar penjumlahan individu." - Peter Senge'
            ],
            [
                'narrative' => "Aktivitas kerja itu seperti nada 🎵. Ada :missed nada yang belum dimainkan di KHI Anda.",
                'quote' => '"Tim terbaik seperti orkestra: setiap instrumen memainkan bagiannya untuk menciptakan harmoni yang sempurna." - Pat Lencioni'
            ],
            [
                'narrative' => "Bayangkan aktivitas Anda sebagai benih di taman 🌱. Saat ini ada :missed benih yang belum ditanam di KHI.",
                'quote' => '"Konsistensi dalam tindakan kecil hari ini akan menghasilkan transformasi besar di masa depan." - Robin Sharma'
            ],
            [
                'narrative' => "Setiap catatan aktivitas harian anda adalah warna pelangi 🌈. Ada :missed warna yang masih hilang di catatan KHI Anda.",
                'quote' => '"Keragaman dalam tim bukan tentang perbedaan, tetapi tentang melengkapi spektrum kemampuan organisasi." - Verna Myers'
            ],
            [
                'narrative' => "Aktivitas harian itu seperti batu bata 🧱. Ada :missed bata yang belum diletakkan di bangunan kinerja Anda.",
                'quote' => '"Agilitas sejati lahir dari disiplin membangun fondasi yang kuat melalui tindakan konsisten setiap hari." - Eric Ries'
            ],
            [
                'narrative' => "Aktivitas kerja itu ibarat anak tangga ⬆️. Ada :missed tangga yang masih kosong di catatan KHI Anda.",
                'quote' => '"Setiap level pencapaian baru dimulai dari keberanian mengambil satu langkah kecil hari ini." - Tony Robbins'
            ],
            [
                'narrative' => "Bayangkan aktivitas Anda sebagai lukisan 🎨. Ada :missed sapuan kuas yang belum terisi di kanvas KHI.",
                'quote' => '"Masterpiece terbesar diciptakan melalui akumulasi detail-detail kecil yang dikerjakan dengan penuh perhatian." - Michelangelo'
            ],
            [
                'narrative' => "Setiap aktivitas itu seperti keping mozaik 🟦. Ada :missed keping yang belum ditempatkan di KHI Anda.",
                'quote' => '"Keunggulan organisasi terletak pada kemampuan mengintegrasikan setiap detail kontribusi menjadi gambaran besar yang utuh." - Jim Collins'
            ],
            [
                'narrative' => "Aktivitas Anda adalah perjalanan dengan kapal ⛵. Ada :missed pelabuhan yang terlewat di logbook KHI Anda.",
                'quote' => '"Dokumentasi perjalanan bukan sekedar catatan masa lalu, tetapi kompas untuk navigasi masa depan." - John Maxwell'
            ],
            [
                'narrative' => "Bayangkan aktivitas sebagai jembatan 🌉. Ada :missed papan kayu yang masih kosong di KHI Anda.",
                'quote' => '"Infrastruktur organisasi yang kokoh dibangun dari komitmen individual yang saling menguatkan." - Simon Sinek'
            ],
            [
                'narrative' => "Aktivitas harian ibarat irama drum 🥁. Ada :missed ketukan yang belum dimainkan di KHI.",
                'quote' => '"Ritme konsistensi dalam bekerja menciptakan momentum yang menggerakkan seluruh organisasi." - Marshall Goldsmith'
            ],
            [
                'narrative' => "Setiap catatan adalah bintang dalam konstelasi ✨. Ada :missed titik cahaya yang hilang dari KHI Anda.",
                'quote' => '"Kolaborasi yang efektif menciptakan sinergitas di mana hasil keseluruhan melebihi penjumlahan bagian-bagiannya." - Stephen Covey'
            ],
            [
                'narrative' => "Aktivitas itu ibarat catatan harian perjalanan 🚶. Ada :missed langkah yang tidak tercatat di KHI.",
                'quote' => '"Refleksi harian atas tindakan kecil adalah fondasi untuk perubahan transformatif jangka panjang." - Charles Duhigg'
            ],
            [
                'narrative' => "Setiap aktivitas adalah tetes air 💧. Ada :missed tetes yang hilang di aliran KHI Anda.",
                'quote' => '"Kontribusi individual yang tampak kecil, ketika dikumpulkan, mampu menggerakkan gelombang perubahan besar." - Margaret Mead'
            ],
            [
                'narrative' => "Aktivitas Anda adalah bagian dari lagu tim 🎶. Ada :missed nada yang belum ikut dimainkan.",
                'quote' => '"Simfoni organisasi terbaik lahir ketika setiap anggota tim memahami dan memainkan perannya dengan sempurna." - Patrick Lencioni'
            ],
            [
                'narrative' => "Bayangkan aktivitas sebagai cahaya lilin 🕯️. Ada :missed lilin yang belum dinyalakan di KHI.",
                'quote' => '"Satu tindakan inspiratif memiliki kekuatan untuk menyalakan semangat dan motivasi di seluruh tim." - John Kotter'
            ],
            [
                'narrative' => "Aktivitas adalah jejak tinta di kertas 📜. Ada :missed baris yang kosong di catatan KHI Anda.",
                'quote' => '"Setiap dokumentasi kerja adalah investasi untuk institutional memory dan pembelajaran organisasi." - Peter Drucker'
            ],
            [
                'narrative' => "Aktivitas ibarat benang dalam kain 🧵. Ada :missed benang yang belum terjalin di KHI.",
                'quote' => '"Kekuatan organisasi terletak pada kemampuan menenun setiap kontribusi individual menjadi fabric yang solid." - Edgar Schein'
            ],
            [
                'narrative' => "Bayangkan aktivitas Anda sebagai mata rantai ⛓️. Ada :missed rantai yang belum tersambung.",
                'quote' => '"Sistem yang kuat dibangun dari ketergantungan positif di mana setiap elemen saling memperkuat." - W. Edwards Deming'
            ],
            [
                'narrative' => "Aktivitas harian seperti batu permata 💎. Ada :missed permata yang belum ditempatkan di KHI.",
                'quote' => '"Nilai sejati organisasi tercipta ketika setiap talenta individual ditempatkan pada posisi yang tepat." - Marcus Buckingham'
            ],
            [
                'narrative' => "Setiap aktivitas adalah papan catur ♟️. Ada :missed langkah yang belum dimainkan di KHI.",
                'quote' => '"Strategi besar terwujud melalui eksekusi taktis yang konsisten dalam tindakan sehari-hari." - Michael Porter'
            ],
            [
                'narrative' => "Aktivitas itu ibarat cahaya pagi 🌅. Ada :missed sinar yang belum hadir di KHI Anda.",
                'quote' => '"Konsistensi dalam tindakan kecil setiap hari menciptakan momentum yang membawa pencerahan besar." - Hal Elrod'
            ],
            [
                'narrative' => "Bayangkan aktivitas sebagai tetesan cat 🌈. Ada :missed tetesan warna yang hilang dari kanvas KHI.",
                'quote' => '"Diversitas dalam kontribusi menciptakan spektrum kemampuan yang memperkaya hasil akhir organisasi." - Scott Page'
            ],
            [
                'narrative' => "Aktivitas kerja seperti biji kopi ☕. Ada :missed biji yang hilang di catatan KHI.",
                'quote' => '"Kualitas hasil akhir sangat ditentukan oleh perhatian terhadap setiap elemen kecil dalam proses." - Kaizen Philosophy'
            ],
            [
                'narrative' => "Setiap aktivitas adalah titik tinta ✒️. Ada :missed titik yang belum digoreskan di KHI Anda.",
                'quote' => '"Narasi besar organisasi ditulis melalui akumulasi cerita-cerita kecil dari setiap individu." - Brené Brown'
            ],
            [
                'narrative' => "Aktivitas itu ibarat kelopak bunga 🌸. Ada :missed kelopak yang belum mekar di KHI Anda.",
                'quote' => '"Budaya organisasi yang sehat tumbuh ketika setiap individu berkontribusi sesuai dengan potensi terbaiknya." - Edgar Schein'
            ],
            [
                'narrative' => "Bayangkan aktivitas Anda sebagai matahari terbit 🌄. Ada :missed cahaya yang belum muncul di KHI.",
                'quote' => '"Kebiasaan konsisten dalam bekerja adalah sumber energi yang memberikan harapan dan optimisme bagi seluruh tim." - Stephen Covey'
            ],
        ];


        // pilih narasi berdasar hari (rotate)
        $index = $today->day % count($stories);
        $story = $stories[$index];

        foreach ($users as $user) {
            // Hanya untuk test ke 1 nomor (ganti sesuai no HP Anda)
            // if ($user->no_hp !== 'xxxxxxxxxxxx') {
            //     continue; // lewati semua kecuali nomor ini
            // }
            $TodayActivity = Activity::where('nip', $user->nip)
                ->where('tgl', $today)
                ->count();

            if ($TodayActivity == 0) {
                $narrative = str_replace(':missed', $user->missed_days, $story['narrative']);

                $message = "💬 *Notifikasi Aplikasi KHI*\n"
                    . "Selamat Siang, {$user->fullname}. \n"
                    . $narrative . "\n\n"
                    . "_{$story['quote']}_";

                $details = [
                    'message' => $message,
                    'no_hp' => $user->no_hp,
                ];

                \Log::info("DispatchMessages jalan untuk user {$user->fullname}, no_hp: {$user->no_hp}");

                $queue = new DailyReminder($details);
                dispatch($queue->delay(now()->addSeconds(10)));
            }
        }

        return Command::SUCCESS;
    }
}
