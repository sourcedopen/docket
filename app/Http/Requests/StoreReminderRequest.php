<?php

namespace App\Http\Requests;

use App\Enums\ReminderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'remind_at' => ['required', 'date'],
            'type' => ['required', Rule::enum(ReminderType::class)],
            'notes' => ['nullable', 'string'],
            'is_recurring' => ['boolean'],
            'recurrence_rule' => ['nullable', 'string', Rule::in(['every_7_days', 'every_30_days', 'every_weekday'])],
            'recurrence_ends_at' => ['nullable', 'date', 'after:remind_at'],
        ];
    }
}
