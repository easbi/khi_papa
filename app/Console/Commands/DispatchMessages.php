<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DailyReminder;
use App\Jobs\SendBirthdayReminderJob; 
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
        // Mengirim pengingat harian jika pengguna belum mengisi aktivitas hari ini
        $users = User::where('notification', '1')->get();
        foreach ($users as $user) {
            $TodayActivity = Activity::where('nip',$user->nip)
                                    ->where('tgl', Carbon::today())
                                    ->count();
            // dd($TodayActivity);
            if ($TodayActivity == 0) {
                    $details = [
                                'message' => '💬 Selamat Sore, ' . $user->fullname . ' Anda terpantau tidak mengisi kegiatan kerja hari ini di link https://sipalink.id/khi/public/ . Dah gitu aja. #isiajadulu 📲',
                                'no_hp' => $user->no_hp
                            ];
                    $queue = new DailyReminder($details);
                    dispatch($queue->delay(now()->addSeconds(10)));
            }

            // if ($TodayActivity != 0) {
            //         $details = [
            //                     'message' => '💬 Selamat Sore, ' . $user->fullname . 'Berikut Detail Kegiatan Kam',
            //                     'no_hp' => $user->no_hp
            //                 ];
            //         $queue = new DailyReminder($details);
            //         dispatch($queue->delay(now()->addSeconds(10)));
            // };
            #Tambahkan List Aktifitas Yang Mau di Copy Utk Presensi Disini
        }; 

        return Command::SUCCESS;

    }
}
