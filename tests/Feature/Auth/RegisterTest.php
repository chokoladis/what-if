<?php

namespace Feature\Auth;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_register_accept(): void
    {
        $this->withoutToken();

        $response = $this->post(route('register'), [
            'name' => 'test_1',
            'email' => 'test_1@mail.ru',
            'password' => 'test_1@mail.ru',
            'password_confirmation' => 'test_1',
        ]);

        $response->assertRedirect(route('welcome'));
    }

    public function test_register_redirect_back(): void
    {
        $this->withoutToken();

        $response = $this->post(route('register'), [
            'name' => 'test_err',
            'email' => 'test_err.ru',
            'password' => 'ru',
            'password_confirmation' => 'test_1',
        ]);

        $response->assertRedirectBack();
    }

    public function test_register_with_error_repeater_email(): void
    {
        $this->withoutToken();

        $response = $this->post(route('register'), [
            'name' => 'test_1',
            'email' => 'test_1@mail.ru',
            'password' => 'test_1@mail.ru',
            'password_confirmation' => 'test_1',
        ]);
        $response->assertRedirect(route('welcome'));

        $response = $this->post(route('register'), [
            'name' => 'test_1',
            'email' => 'test_1@mail.ru',
            'password' => 'test_1@mail.ru',
            'password_confirmation' => 'test_1',
        ]);
        $response->assertRedirectBack();
    }
}
