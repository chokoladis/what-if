<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = collect($validator->errors()->toArray())
            ->flatMap(function (array $messages, string $field) {
                return array_map(fn(string $message) => [
                    'code' => 'validation_error',
                    'message' => $message,
                    'field' => $field,
                ], $messages);
            })
            ->values()
            ->all();

        throw new HttpResponseException(
            response()->json([
                'result' => false,
                'errors' => $errors,
            ], 422)
        );
    }
}