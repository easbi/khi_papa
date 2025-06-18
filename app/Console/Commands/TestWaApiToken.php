<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestWaApiToken extends Command
{
    protected $signature = 'test:wa-token';
    protected $description = 'Test WA API token from .env and send a test message';

    public function handle()
    {
        $token = env('API_WA_TOKEN');
        $this->info("Token from .env: $token");
        Log::info("Test WA API Token: $token");

        if (!$token) {
            $this->error("Token is empty! Please set API_WA_TOKEN in your .env file.");
            return 1;
        }

        $payload = [
            'token' => $token,
            'number' => '085265513571', // ganti nomor yang valid untuk test
            'message' => 'Test pesan dari Laravel untuk cek token WA API',
        ];

        $this->info('Mengirim request test ke API WA...');
        Log::info('Mengirim request test ke API WA...');

        $response = Http::asForm()->post('https://app.ruangwa.id/api/send_message', $payload);

        $this->info("Response status: " . $response->status());
        $this->info("Response body: " . $response->body());
        Log::info("Response WA API: " . $response->body());

        return 0;
    }
}
