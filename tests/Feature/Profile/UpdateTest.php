<?php

namespace Feature\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_update_success()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $newVal = rand(1000, 9999);
        $response = $this->post(route('profile.update'), [
            'email' => 'test' . $newVal . '@gmail.com',
            'name' => (string)$newVal,
            '_token' => csrf_token()
        ]);

        $response->assertRedirect(route('profile.index'))
            ->assertSessionHas('message', __('system.alerts.success'));
//        for ->assertSee($newVal) todo clear cache
    }

    public function test_update_return_error()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $newVal = rand(1000, 9999);
        $response = $this->post(route('profile.update'), [
            'email' => 'test' . $newVal . '@gmail.com',
            'name' => 9,
            '_token' => csrf_token()
        ]);

        $response->assertSessionHas('errors');
    }

//    todo for update photo
}