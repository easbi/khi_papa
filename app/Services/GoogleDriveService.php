<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected function basePayload(array $extra = []): array
    {
        return array_merge([
            'token' => config('services.gdrive.token'),
        ], $extra);
    }

    protected function post(array $payload): ?array
    {
        try {
            $response = Http::timeout(120)
                ->asJson()
                ->post(config('services.gdrive.url'), $this->basePayload($payload));

            if (! $response->successful()) {
                Log::error('GDrive HTTP error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();
            if (! ($json['success'] ?? false)) {
                Log::error('GDrive API failed', ['res' => $json]);
                return null;
            }

            return $json;
        } catch (\Throwable $e) {
            Log::error('GDrive exception', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function sanitizeFolderName(string $name): string
    {
        $clean = preg_replace('#[/\\\\:\*\?"<>\|]#', '-', $name);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));
        if (mb_strlen($clean) > 100) {
            $clean = mb_substr($clean, 0, 100);
        }
        return $clean ?: 'Unknown';
    }

    public function upload(
        string $filePath,
        string $fileName,
        string $mimeType,
        string $folderName,
        string $tahun
    ): ?array {
        $json = $this->post([
            'action'      => 'upload',
            'kegiatan_id' => $this->sanitizeFolderName($folderName),
            'tahun'       => $tahun,
            'file_name'   => $fileName,
            'mime_type'   => $mimeType,
            'file_base64' => base64_encode(file_get_contents($filePath)),
        ]);

        if (! $json || empty($json['file']['id'])) {
            return null;
        }

        // Paksa URL yang rapi
        $json['file']['url'] = 'https://drive.google.com/file/d/'
            . $json['file']['id'] . '/view';

        return $json['file'];
    }

    public function delete(string $fileId): bool
    {
        $json = $this->post([
            'action'  => 'delete',
            'file_id' => $fileId,
        ]);

        return $json !== null;
    }

    public function info(string $fileId): ?array
    {
        try {
            $response = Http::timeout(30)->get(config('services.gdrive.url'), [
                'action'  => 'info',
                'token'   => config('services.gdrive.token'),
                'file_id' => $fileId,
            ]);

            if (! $response->successful()) return null;
            $json = $response->json();
            return ($json['success'] ?? false) ? ($json['file'] ?? null) : null;
        } catch (\Throwable $e) {
            Log::error('GDrive info exception', ['msg' => $e->getMessage()]);
            return null;
        }
    }
}
