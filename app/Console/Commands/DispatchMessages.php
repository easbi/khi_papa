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
                        'message' => 'Hello ' . $user->fullname . ' Kamu belum mengisi Catatan Kerja Hari ini !',
                        'no_hp' => $user->no_hp
                    ];

                    // echo "There are activities for today.";

                    // dd($details);

                    $delay = \DB::table('jobs')->count()*10;
                    $queue = new DailyReminder($details);

                    // send all notification whatsapp in the queue.
                    dispatch($queue->delay($delay));
                }
        };
    }
}
