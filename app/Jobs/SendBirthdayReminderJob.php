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

class SendBirthdayReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $details;
    public $timeout = 20; //0,33 minutes to time out for n clients

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = env('API_WA_TOKEN');
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
            CURLOPT_POSTFIELDS => 'token=' . $token . '&number=' . $this->details['no_hp'] . '&message=' . urlencode($this->details['message']),
        ));
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            Log::error('Curl error: ' . curl_error($curl));
        } else {
            Log::info('Response from API: ' . $response);
        }

        curl_close($curl);
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Handle the failure logic, log it for further review
        Log::error('SendBirthdayReminderJob failed for ' . $this->details['no_hp'] . ': ' . $exception->getMessage());
    }
}