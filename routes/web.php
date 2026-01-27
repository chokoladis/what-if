<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicesAuthController;

//$responseJsonMiddleware = \App\Http\Middleware\ResponseJsonMiddleware::class;

Route::get('/api/google_auth.php', [ServicesAuthController::class, 'googleAuth'])->name('google_auth');
Route::get('/api/yandex_auth.php', [ServicesAuthController::class, 'yandexAuth'])->name('yandex_auth');


Route::middleware(['locale'])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    \Illuminate\Support\Facades\Auth::routes();

    Route::group(['namespace' => 'App\Http\Controllers'], function () {

        Route::get('/questions', 'QuestionController@index')->name('questions.index');
        Route::get('/categories', 'CategoryController@index')->name('categories.index');
        Route::get('/categories/detail/{category}/', 'CategoryController@detail')->name('categories.detail');
        Route::get('/questions/detail/{question}/', 'QuestionController@detail')->name('questions.detail');
        Route::post('/comments/load-subcomments', 'CommentController@loadSubcomments')->name('comments.load.subcomments');

        Route::post('/setting/lang', 'SettingController@setLang')->name('setting.set.lang');
        Route::post('/setting/theme', 'SettingController@setTheme')->name('setting.set.theme');
        Route::post('/setting/type-output', 'SettingController@setTypeOutput')->name('setting.set.typeOutput');

        Route::middleware(['auth'])->group(function () {

            Route::prefix('questions')->name('questions.')->group(function () {
                Route::get('/add', 'QuestionController@add')->name('add');
                Route::post('/', 'QuestionController@store')->middleware(['captcha'])->name('store');
                Route::post('/right_comment', 'QuestionController@setRightComment')->name('setRightComment');

                Route::post('/{question}/vote', 'QuestionVoteController@set')->name('vote');
            });

            Route::prefix('comments')->name('comments.')->group(function () {
                Route::post('/', 'CommentController@store')->name('store');

                Route::post('/vote', 'CommentVotesController@setStatus')->name('status.set');
            });

            Route::group(['prefix' => 'profile', 'controller' => 'UserController'], function () {
                Route::name('profile.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/edit', 'edit')->name('edit');
                    Route::post('/', 'update')->name('update');
                    Route::post('/tags', 'setTags')->name('tags.set');
                    Route::post('/photo', 'setPhoto')->name('setPhoto');
                });
            });
        });

        Route::get('/search', 'SearchController@index')->name('search.index');
        Route::post('/feedback', 'FeedbackController@store')->name('feedback.store');

        Route::post('/ajax/setThemeMode', 'UserController@setThemeMode')->name('setThemeMode');
    });
});