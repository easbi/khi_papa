<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use App\Jobs\SendReminderActivityJob;
use App\Helpers\DateHelper;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use DB;


class SendActivityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-activity-reminders';

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
        $now = Carbon::now()->startOfMinute();

        $reminders = Activity::where('is_reminded', 1)
            ->where('reminder_at', $now)
            ->get();


        foreach ($reminders as $activity) {
            $user = User::where('nip', $activity->nip)->first();
            if (!$user || !$user->no_hp)
                continue;
            $plainKeterangan = strip_tags($activity->keterangan); // Hilangkan tag HTML
            $keterangan = $plainKeterangan ? Str::limit($plainKeterangan, 100, '...') : '-';

            $details = [
                'message' => "⏰ *Reminder Kegiatan Hari Ini (KHI)*\n"
                    . "📅 *Tanggal:* {$activity->tgl}\n"
                    . "📝 *Kegiatan:* {$activity->kegiatan}\n"
                    . "🗒️ *Keterangan:* {$keterangan}\n"
                    . "Jangan lupa untuk melengkapi dan menyelesaikan kegiatan hari ini ya, *{$user->fullname}*! 📲",
                'no_hp' => $user->no_hp,
            ];

            // jeda 10 detik sebelum dispatch job berikutnya
            sleep(10);

            dispatch(new SendReminderActivityJob($details));
        }


        return Command::SUCCESS;
    }

}
