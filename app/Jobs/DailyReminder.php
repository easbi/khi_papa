<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyReminder implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;
    public $timeout = 20; // maksimal job jalan 20 detik

    /**
     * Create a new job instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Unique ID supaya 1 nomor HP tidak dobel job saat masih berjalan.
     */
    public function uniqueId()
    {
        return $this->details['no_hp'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("DailyReminder START untuk {$this->details['no_hp']} - {$this->details['fullname']}");

        $token = env('API_WA_TOKEN');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15, // timeout 15 detik
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query([
                'token'   => $token,
                'number'  => $this->details['no_hp'],
                'message' => $this->details['message'],
            ]),
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::error("DailyReminder ERROR untuk {$this->details['no_hp']} - {$this->details['fullname']} : {$error}");
        } else {
            Log::info("DailyReminder SELESAI untuk {$this->details['no_hp']} - Response: {$response}");
        }
    }
}
