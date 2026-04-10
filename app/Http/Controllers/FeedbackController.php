<?php

namespace App\Http\Controllers;

use App\DTO\Errors\CommonError;
use App\Http\Requests\Feedback\StoreRequest;
use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    public function store(StoreRequest $request): Response
    {
        $service = new FeedbackService();
        /** @var ?Feedback $feedback */
        [$feedback, $errors] = $service->store($request->validated());

        if ($errors) {
            return responseJson(false, $errors);
        }

        if ($feedback && $feedback->wasRecentlyCreated) {
            // todo with lang
            return responseJson(result: 'Заявка успешно отправлена', status: Response::HTTP_CREATED);
        } else {
            return responseJson(false, [new CommonError('Ваша заявка уже отправлена и ожидает обработки')]);
        }
    }
}
