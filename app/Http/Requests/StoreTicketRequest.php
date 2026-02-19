<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'status' => ['required', Rule::enum(TicketStatus::class)],
            'priority' => ['required', Rule::enum(TicketPriority::class)],
            'description' => ['nullable', 'string'],
            'filed_with_contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'external_reference' => ['nullable', 'string', 'max:255'],
            'filed_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*' => ['nullable'],
        ];
    }
}
