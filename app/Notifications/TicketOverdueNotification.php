<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Ticket $ticket) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysOverdue = now()->diffInDays($this->ticket->due_date);

        return (new MailMessage)
            ->subject("Overdue: {$this->ticket->reference_number} — {$this->ticket->title}")
            ->line("Ticket {$this->ticket->reference_number} is overdue by {$daysOverdue} day(s).")
            ->line("Title: {$this->ticket->title}")
            ->line("Due date: {$this->ticket->due_date->format('d M Y')}")
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('Please take action on this ticket in Open Docket.');
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_reference' => $this->ticket->reference_number,
            'ticket_title' => $this->ticket->title,
            'due_date' => $this->ticket->due_date->toDateString(),
            'days_overdue' => now()->diffInDays($this->ticket->due_date),
        ];
    }
}
