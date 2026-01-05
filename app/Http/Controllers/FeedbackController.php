<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\StoreRequest;
use App\Models\Feedback;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{

    public function store(StoreRequest $request)
    {

        $success = true;
        $response = null;

        try {
            $data = $request->validated();

            // if ($data['email'])

            if (isset($data['phone']) && $data['phone']) {
                $data['phone'] = getNumbers($data['phone']);

                if (strlen($data['phone']) !== 11) {
                    $success = false;
                    $error['phone'] = 'Ошибка заполнения телефона';
                }
            }

            if ($success) {
                $check = Feedback::firstOrCreate([
                    'email' => $data['email'],
                    'comment' => $data['comment']
                ], $data);

                if ($check->wasRecentlyCreated) {
                    $response = 'Заявка успешно отправлена';
                } else {
                    $success = false;
                    $error['other'] = 'Ваша заявка уже отправлена и ожидает обработки';
                }
            }

            dump($data);

            return responseJson($success, $response, $error, 201);
        } catch (\Throwable $th) {

            Log::error($th, $data);

            throw $th;
        }
    }
}
