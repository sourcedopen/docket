<?php

use App\Enums\CommentType;
use App\Enums\ContactType;
use App\Enums\ReminderType;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

describe('TicketStatus', function () {
    it('has the expected cases', function () {
        expect(TicketStatus::cases())->toHaveCount(8);
    });

    it('has correct string values', function (TicketStatus $status, string $value) {
        expect($status->value)->toBe($value);
    })->with([
        [TicketStatus::Draft, 'draft'],
        [TicketStatus::Submitted, 'submitted'],
        [TicketStatus::Acknowledged, 'acknowledged'],
        [TicketStatus::InProgress, 'in_progress'],
        [TicketStatus::Escalated, 'escalated'],
        [TicketStatus::Resolved, 'resolved'],
        [TicketStatus::Closed, 'closed'],
        [TicketStatus::Reopened, 'reopened'],
    ]);

    it('returns a human-readable label', function () {
        expect(TicketStatus::InProgress->label())->toBe('In Progress')
            ->and(TicketStatus::Draft->label())->toBe('Draft');
    });

    it('can be created from a string value', function () {
        expect(TicketStatus::from('in_progress'))->toBe(TicketStatus::InProgress);
    });
});

describe('TicketPriority', function () {
    it('has the expected cases', function () {
        expect(TicketPriority::cases())->toHaveCount(4);
    });

    it('has correct string values', function (TicketPriority $priority, string $value) {
        expect($priority->value)->toBe($value);
    })->with([
        [TicketPriority::Low, 'low'],
        [TicketPriority::Medium, 'medium'],
        [TicketPriority::High, 'high'],
        [TicketPriority::Critical, 'critical'],
    ]);

    it('returns a human-readable label', function () {
        expect(TicketPriority::Critical->label())->toBe('Critical');
    });
});

describe('CommentType', function () {
    it('has the expected cases', function () {
        expect(CommentType::cases())->toHaveCount(5);
    });

    it('has correct string values', function (CommentType $type, string $value) {
        expect($type->value)->toBe($value);
    })->with([
        [CommentType::Update, 'update'],
        [CommentType::Note, 'note'],
        [CommentType::ResponseReceived, 'response_received'],
        [CommentType::Escalation, 'escalation'],
        [CommentType::Resolution, 'resolution'],
    ]);

    it('returns a human-readable label', function () {
        expect(CommentType::ResponseReceived->label())->toBe('Response Received');
    });
});

describe('ContactType', function () {
    it('has the expected cases', function () {
        expect(ContactType::cases())->toHaveCount(4);
    });

    it('has correct string values', function (ContactType $type, string $value) {
        expect($type->value)->toBe($value);
    })->with([
        [ContactType::Authority, 'authority'],
        [ContactType::Company, 'company'],
        [ContactType::Department, 'department'],
        [ContactType::Individual, 'individual'],
    ]);
});

describe('ReminderType', function () {
    it('has the expected cases', function () {
        expect(ReminderType::cases())->toHaveCount(3);
    });

    it('has correct string values', function (ReminderType $type, string $value) {
        expect($type->value)->toBe($value);
    })->with([
        [ReminderType::DeadlineApproaching, 'deadline_approaching'],
        [ReminderType::FollowUp, 'follow_up'],
        [ReminderType::Custom, 'custom'],
    ]);

    it('returns a human-readable label', function () {
        expect(ReminderType::DeadlineApproaching->label())->toBe('Deadline Approaching');
    });
});
