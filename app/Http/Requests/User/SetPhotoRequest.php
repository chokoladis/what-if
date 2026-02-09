<?php

namespace App\Http\Requests\User;

use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;

class SetPhotoRequest extends FormRequest
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
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:' . FileService::MAX_FILE_SIZE_KB],
        ];
    }

    public function messages(): array
    {
        return [
            //The photo field must be an image.
            //The photo field must be a file of type: jpeg, png, jpg, gif.
        ];
    }
}
