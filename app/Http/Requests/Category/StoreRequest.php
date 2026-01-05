<?php

namespace App\Http\Requests\Category;

use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

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
            'parent_id' => 'int|nullable',
            'title' => 'required|string',
            'img' => ['nullable', 'mimes:jpg,png,jpeg,gif', File::image()->max(FileService::MAX_FILE_SIZE)],
            'sort' => 'int|nullable'
        ];
    }
}
