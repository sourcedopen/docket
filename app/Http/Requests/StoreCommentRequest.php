<?php

namespace App\Http\Requests;

use App\Enums\CommentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
            'type' => ['required', Rule::enum(CommentType::class)],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480'],
        ];
    }
}
