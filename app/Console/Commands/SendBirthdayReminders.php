<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendBirthdayReminderJob;
use App\Models\User;
use Carbon\Carbon;

class SendBirthdayReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-birthday-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to send birthday reminders to users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::get();
        foreach ($users as $user) {
            // Ekstrak tahun, bulan, dan tanggal dari NIP
            $tahunUlangTahun = substr($user->nip, 0, 4);
            $bulanUlangTahun = substr($user->nip, 4, 2);
            $tanggalUlangTahun = substr($user->nip, 6, 2);

            try {
                // Membuat objek tanggal lahir menggunakan Carbon
                $birth_date = Carbon::createFromDate($tahunUlangTahun, $bulanUlangTahun, $tanggalUlangTahun);

                // Menghitung usia
                $usia = $birth_date->diffInYears(Carbon::now());

                // Cek apakah hari ini adalah ulang tahun pengguna
                if ($birth_date->format('m-d') == Carbon::today()->format('m-d')) {
                    $details = [
                        'message' => 'Selamat Ulang Tahun, *' . $user->fullname . '*! Kami berharap di usia mu yang ke-' . $usia . ' tahun hari ini sangat spesial untukmu. Jangan lupa untuk tetap semangat dan sehat selalu!',
                        'no_hp' => $user->no_hp
                    ];

                    $birthdayQueue = new SendBirthdayReminderJob($details);

                    // Dispatch job untuk pengingat ulang tahun
                    dispatch($birthdayQueue);
                }
            } catch (\Exception $e) {
                \Log::error('Error parsing birth date for user ' . $user->id . ': ' . $e->getMessage());
                continue; // Lewati pengguna jika parsing tanggal gagal
            }
        }

        return Command::SUCCESS;
    }
}
