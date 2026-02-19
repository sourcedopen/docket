<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Reminder $reminder) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->reminder->ticket;

        return (new MailMessage)
            ->subject("Reminder: {$this->reminder->title}")
            ->line("You have a reminder for ticket {$ticket->reference_number}: {$ticket->title}")
            ->line("Reminder: {$this->reminder->title}")
            ->when($this->reminder->notes, fn ($mail) => $mail->line("Notes: {$this->reminder->notes}"))
            ->action('View Ticket', route('tickets.show', $ticket))
            ->line('This reminder was created in Open Docket.');
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        $ticket = $this->reminder->ticket;

        return [
            'reminder_id' => $this->reminder->id,
            'ticket_id' => $ticket->id,
            'ticket_reference' => $ticket->reference_number,
            'ticket_title' => $ticket->title,
            'reminder_title' => $this->reminder->title,
            'remind_at' => $this->reminder->remind_at->toIso8601String(),
        ];
    }
}
