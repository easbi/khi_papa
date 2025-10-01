<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyReminder implements ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;
    public $timeout = 20;
    public $connection = 'sync'; // langsung dieksekusi

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function uniqueId(): string
    {
        return $this->details['no_hp'];
    }

    public function handle(): void
    {
        $noHp     = $this->details['no_hp'] ?? '-';
        $fullname = $this->details['fullname'] ?? '-';
        $message  = $this->details['message'] ?? '';

        Log::info("📨 DailyReminder START untuk {$noHp} - {$fullname}");

        $token = env('API_WA_TOKEN');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'token'   => $token,
                'number'  => $noHp,
                'message' => $message,
            ]),
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::error("❌ DailyReminder ERROR untuk {$noHp}: {$error}");
        } else {
            Log::info("✅ DailyReminder SELESAI untuk {$noHp} - Response: {$response}");
        }
    }
}
