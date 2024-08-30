<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;
use DB;

class DailyReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $details;
    public $timeout = 20; //0,33 minutes to time out for n clients

    /**
     * Create a new job instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = "6y9HFiTodpFEUJnN64rK5bKHtGhhNbnvBNGEF5Uabobe6LxnAN";
        // $token = "6y9HFiTodpFEUJnN64rK5bKHtGhhNbnvBNGEF5Uabo";
        // $phone= "081312315895"; //untuk group pakai groupid contoh: 62812xxxxxx-xxxxx
        // $message = "Test Daily Notifikasi At 9.00 WIB";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'token='.$token.'&number='.$this->details['no_hp'].'&message='.$this->details['message'],
        //   CURLOPT_POSTFIELDS => 'token=Gj2npNWz7fTTka91sWKSxdK7wArGKAgik4Ge7SALGmt7pMKTik&number=6281312315895&message=Test WA BOT',
        ));
        $response = curl_exec($curl);
        // $affected = DB::table('transaksi_pembayaran')->where('id', $this->details['id'])->where('send_notif', 0)->update(['send_notif' => 1]);
        curl_close($curl);
        echo $response;
    }
}
