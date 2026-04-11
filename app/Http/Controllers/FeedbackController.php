<?php

namespace App\Http\Controllers;

use App\DTO\Errors\CommonError;
use App\DTO\Errors\ValidationError;
use App\Http\Requests\Feedback\StoreRequest;
use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    public function store(StoreRequest $request): Response
    {
        $service = new FeedbackService();
        /**
         * @var ?Feedback $feedback
         * @var ?ValidationError $error
         */
        [$feedback, $error] = $service->store($request->validated());

        if ($error) {
            return responseJson(false, $error);
        }

        if ($feedback && $feedback->wasRecentlyCreated) {
            // todo with lang
            return responseJson('Заявка успешно отправлена', status: Response::HTTP_CREATED);
        } else {
            return responseJson(false, new CommonError('Ваша заявка уже отправлена и ожидает обработки'));
        }
    }
}
