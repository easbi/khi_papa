<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DailyReminder;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;

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
        $users = User::where('notification', '1')->get();
        foreach ($users as $user) {
            $TodayActivity = Activity::where('nip',$user->nip)
                                    -> where('tgl', Carbon::today())
                                    ->count();
            // dd($TodayActivity);
            if ($TodayActivity == 0) {
                    $details = [
                        'message' => 'Selamat Sore, ' . $user->fullname . ' Aduduh, Kamu belum mengisi Catatan Kerja di KHI Hari ini ! Segera isi KHI di https://padangpanjangkotabps.id/khi/public/ dengan akun username '.$user->username.' dan password yang sudah diberikan terdahulu. Jika Lupa akun atau password cukup balas pesan ini. Terimakasih dan Sehat Selalu. #sipalingingetin',
                        'no_hp' => $user->no_hp
                    ];

                    $delay = \DB::table('jobs')->count()*10;
                    $queue = new DailyReminder($details);

                    // send all notification whatsapp in the queue.
                    dispatch($queue->delay($delay));
                }
        };
    }
}
