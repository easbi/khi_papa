<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    public function test_login_accepts_username_field_for_existing_users(): void
    {
        User::where('email', 'easbi@example.com')->delete();

        $user = new User();
        $user->name = 'easbi';
        $user->email = 'easbi@example.com';
        $user->password = Hash::make('password123');
        $user->save();

        $response = $this->post(route('login'), [
            'username' => 'easbi',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}
