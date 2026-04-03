<?php

namespace Tests\Feature;

use Tests\TestCase;

class SettingTest extends TestCase
{
//        Route::post('/setting/lang', 'SettingController@setLang')->name('setting.set.lang');
//        Route::post('/setting/theme', 'SettingController@setTheme')->name('setting.set.theme');
    public function test_set_type_output_success()
    {
        $this->post(route('setting.set.typeOutput'), [
            'type' => 'compact'
        ])->assertOk();

        $this->post(route('setting.set.typeOutput'), [
            'type' => 'simple'
        ])->assertOk();
    }

    public function test_set_type_output_error()
    {
        $this->post(route('setting.set.typeOutput'))->assertStatus(500);

        $this->post(route('setting.set.typeOutput'), [
            'type' => 'super'
        ])->assertBadRequest();
    }
}