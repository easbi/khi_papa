<?php

namespace Tests\Feature;

use App\Models\Notification;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class NotificationImageTest extends TestCase
{
    public function test_store_notification_with_image(): void
    {
        Notification::query()->truncate();
        $image = UploadedFile::fake()->image('announcement.png', 600, 400);

        $response = $this->post(route('notif.store'), [
            'judul' => 'Test notification',
            'deskripsi' => 'Notification with image',
            'tipe' => 'info',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'image' => $image,
        ]);

        $response->assertRedirect(route('notif.index'));

        $notification = Notification::latest()->first();

        $this->assertNotNull($notification);
        $this->assertNotNull($notification->image_path);
        $this->assertFileExists(public_path($notification->image_path));
    }
}
