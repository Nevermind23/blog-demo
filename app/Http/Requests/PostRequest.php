<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $type = 'required';
        if (request()->route()->getName() == 'post.update') {
            $type = 'nullable';
        }

        return [
            'title' => [$type, 'string', 'max:255'],
            'description' => [$type, 'string'],
            'image' => [$type, 'image', 'max:' . config('test.max_file_size')]
        ];
    }
}
