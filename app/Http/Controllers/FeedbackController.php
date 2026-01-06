<?php

namespace App\Http\Controllers;

use App\DTO\Errors\CommonError;
use App\DTO\Errors\ValidationError;
use App\Http\Requests\Feedback\StoreRequest;
use App\Models\Feedback;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function store(StoreRequest $request)
    {
        $success = true;
        $errors = [];

        try {
            $data = $request->validated();

            // if ($data['email'])

            if (isset($data['phone']) && $data['phone']) {
                $data['phone'] = getNumbers($data['phone']);

                if (strlen($data['phone']) !== 11) {
                    $errors[] = new ValidationError(
                        'Ошибка заполнения телефона',
                        'phone',
                    );
                }
            }

            if (empty($errors)) {
                $check = Feedback::firstOrCreate([
                    'email' => $data['email'],
                    'comment' => $data['comment']
                ], $data);

                if ($check->wasRecentlyCreated) {
                    $response = 'Заявка успешно отправлена';
                } else {
                    $errors[] = new CommonError('Ваша заявка уже отправлена и ожидает обработки');
                }
            }

            if (!empty($errors)){
                return responseJson(false, $errors);
            }

            return responseJson($success, $response, 201);
        } catch (\Throwable $th) {

            Log::error($th, $data);

            throw $th;
        }
    }
}
