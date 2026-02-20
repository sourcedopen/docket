<?php

namespace App\Models;

use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use SourcedOpen\Tags\Traits\HasTags;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contact extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory, HasTags, InteractsWithMedia, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'organization',
        'email',
        'phone',
        'address',
        'type',
        'notes',
    ];

    public function casts(): array
    {
        return [
            'type' => ContactType::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'filed_with_contact_id');
    }
}
