<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicesAuthController;

//$responseJsonMiddleware = \App\Http\Middleware\ResponseJsonMiddleware::class;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->middleware(['locale'])->name('home');
Route::get('/api/google_auth.php', [ServicesAuthController::class, 'googleAuth'])->name('google_auth');
Route::get('/api/yandex_auth.php', [ServicesAuthController::class, 'yandexAuth'])->name('yandex_auth');
Route::get('/api/telegram_auth.php', [ServicesAuthController::class, 'telegramAuth'])->name('telegram_auth');

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => 'App\Http\Middleware\LocaleMiddleware'], function () {

    Route::get('/questions', 'QuestionController@index')->name('questions.index');
    Route::get('/categories', 'CategoryController@index')->name('categories.index');
    Route::get('/categories/detail/{category}/', 'CategoryController@detail')->name('categories.detail');
    Route::get('/questions/detail/{question}/', 'QuestionController@detail')->name('questions.detail');
    Route::post('/comments/load-subcomments', 'CommentController@loadSubcomments')->name('comments.load.subcomments');
    Route::post('/setting/lang', 'SettingController@setLang')->name('setting.set.lang');
    Route::post('/setting/theme', 'SettingController@setTheme')->name('setting.set.theme');

    Route::middleware(['auth'])->group(function () {

        Route::group(['prefix' => 'questions', 'controller' => 'QuestionController'], function () {
            Route::name('questions.')->group(function () {
                Route::get('/add', 'add')->name('add');
                Route::post('/', 'store')->middleware(['captcha'])->name('store');
                Route::post('/right_comment', 'setRightComment')->name('setRightComment');
            });
        });

        Route::group(['prefix' => 'comments'], function () {
            Route::name('comments.')->group(function () {
                Route::group(['controller' => 'CommentController'], function () {
                    Route::post('/', 'store')->name('store');
                });
                Route::group(['controller' => 'CommentUserStatusController'], function () {
                    Route::post('/status', 'setStatus')->name('status.set');
                });
            });
        });

        Route::group(['prefix' => 'profile', 'controller' => 'UserController'], function () {
            Route::name('profile.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'update')->name('update');
                Route::post('/photo', 'setPhoto')->name('setPhoto');
            });
        });


        Route::post('/ajax/questionStatus', 'QuestionUserStatusController@set')->name('ajax.questionStatus');
        //        admin

        Route::middleware(['admin'])->group(function () {

            Route::group(['prefix' => 'categories', 'controller' => 'CategoryController'], function () {
                Route::name('categories.')->group(function () {
                    Route::get('/add', 'add')->name('add');
                    Route::post('/', 'store')->name('store');
                });
            });
        });
    });
    Route::post('/feedback', 'FeedbackController@store')->name('feedback.store');

    Route::post('/ajax/setThemeMode', 'UserController@setThemeMode')->name('setThemeMode');
})->middleware(['locale']);

// Route::get('/home', [HomeController::class, 'index'])->name('home');