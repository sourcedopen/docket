<?php

namespace App\Models;

use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reminder extends Model
{
    /** @use HasFactory<\Database\Factories\ReminderFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'title',
        'remind_at',
        'type',
        'notes',
        'is_sent',
        'sent_at',
        'is_recurring',
        'recurrence_rule',
        'recurrence_ends_at',
    ];

    public function casts(): array
    {
        return [
            'type' => ReminderType::class,
            'remind_at' => 'datetime',
            'sent_at' => 'datetime',
            'recurrence_ends_at' => 'datetime',
            'is_sent' => 'boolean',
            'is_recurring' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
