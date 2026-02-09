<?php

namespace App\Http\Requests\Question;

use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'category' => ['nullable', 'string'], //todo id categories
            'title' => ['required', 'string', 'min:3'],
            'tags' => ['nullable', 'array'],
            'img' => ['nullable', 'image', 'mimes:' . implode(',', FileService::ALLOW_IMG_EXT), 'max:' . FileService::MAX_FILE_SIZE_KB]
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Заголовок обязателен для заполнения',
            'title.min' => 'Заголовок должен иметь не менее 3 символов',
            'img.mimes' => 'Файл должен иметь разрешенное расширение: ' . implode(',', FileService::ALLOW_IMG_EXT),
            'img.size' => 'Размер изображение не должно быть больше ' . FileService::MAX_FILE_SIZE_MB . ' MB',
        ];
    }
}
