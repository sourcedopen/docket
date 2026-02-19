<?php

namespace App\Enums;

enum CommentType: string
{
    case Update = 'update';
    case Note = 'note';
    case ResponseReceived = 'response_received';
    case Escalation = 'escalation';
    case Resolution = 'resolution';

    public function label(): string
    {
        return match ($this) {
            self::Update => 'Update',
            self::Note => 'Note',
            self::ResponseReceived => 'Response Received',
            self::Escalation => 'Escalation',
            self::Resolution => 'Resolution',
        };
    }
}
