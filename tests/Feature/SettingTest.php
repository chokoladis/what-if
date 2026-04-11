<?php

namespace Tests\Feature;

use App\Services\SettingService;
use Illuminate\Http\Response;
use Tests\TestCase;

class SettingTest extends TestCase
{
//        Route::post('/setting/theme', 'SettingController@setTheme')->name('setting.set.theme');
    public function test_set_type_output_success()
    {
        $this->post(route('setting.set.typeOutput'), [
            'type' => 'compact'
        ])->assertStatus(Response::HTTP_NO_CONTENT);

        $this->post(route('setting.set.typeOutput'), [
            'type' => 'simple'
        ])->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_set_type_output_return_value_not_set()
    {
        $this->post(route('setting.set.typeOutput'))
            ->assertJsonPath('errors.0.code','value_not_set');
    }

    public function test_set_type_output_return_not_support()
    {
        $this->post(route('setting.set.typeOutput'), ['type' => 'super'])
            ->assertJsonPath('errors.0.message','This lang not supporting');
    }

    public function test_set_lang_success()
    {
        $lang = SettingService::LANG[array_rand(SettingService::LANG)];
        $response = $this->post(route('setting.set.lang', [
            'lang' => $lang,
            '_token' => csrf_token()
        ]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_set_lang_return_error()
    {
        $response = $this->post(route('setting.set.lang', [
            'lang' => 'rand',
            '_token' => csrf_token()
        ]));

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}