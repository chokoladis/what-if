<?php

namespace App\Http\Requests\Feedback;

use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/[\w\d]+@[\w\d]+\.[\w\d]+/i'],
            'phone' => ['string', 'nullable'],
            'subject' => ['required', 'string', 'max:40'],
            'comment' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'email.max' => 'Email превысил лимит символов',
            'email.email' => 'Email не является email',
            'email.regex' => 'Email не прошел валидацию',
        ];
    }
}
