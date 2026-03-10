<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\QueryBuilders\Sorts\NullsLastSort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use SourcedOpen\Tags\Models\Tag;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class TicketService
{
    public function __construct(private TicketStateMachine $stateMachine) {}

    public function getFilteredTickets(int $perPage = 20): LengthAwarePaginator
    {
        return QueryBuilder::for(Ticket::class)
            ->with(['ticketType', 'filedWithContact', 'tags'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('ticket_type_id'),
                AllowedFilter::callback('tag', fn (Builder $query, $value) => $query->whereHas('tags', fn (Builder $q) => $q->where('name', $value))),
                AllowedFilter::callback('search', function (Builder $query, $value): void {
                    $query->where(function (Builder $q) use ($value) {
                        $q->where('title', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%")
                            ->orWhere('reference_number', 'like', "%{$value}%")
                            ->orWhere('external_reference', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('filed_from', fn (Builder $query, $value) => $query->whereDate('filed_date', '>=', $value)),
                AllowedFilter::callback('filed_to', fn (Builder $query, $value) => $query->whereDate('filed_date', '<=', $value)),
                AllowedFilter::callback('overdue', function (Builder $query, $value): void {
                    if ($value) {
                        $completedValues = array_map(fn (TicketStatus $s) => $s->value, TicketStatus::completedStatuses());
                        $query->whereNotNull('due_date')
                            ->whereDate('due_date', '<', today())
                            ->whereNotIn('status', $completedValues);
                    }
                }),
                AllowedFilter::callback('open', function (Builder $query, $value): void {
                    if (request()->filled('filter.status')) {
                        return;
                    }

                    if ($value) {
                        $completedValues = array_map(fn (TicketStatus $s) => $s->value, TicketStatus::completedStatuses());
                        $query->whereNotIn('status', $completedValues);
                    }
                })->default(true),
            ])
            ->defaultSort(AllowedSort::custom('due_date', new NullsLastSort))
            ->allowedSorts([
                AllowedSort::custom('due_date', new NullsLastSort),
            ])
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $files
     */
    public function createTicket(array $data, array $tagNames = [], array $files = []): Ticket
    {
        return DB::transaction(function () use ($data, $tagNames, $files) {
            $ticket = Ticket::query()->create($data);
            $this->syncTags($ticket, $tagNames);
            $this->addMedia($ticket, $files, 'documents');

            if (! empty($data['cost_amount'])) {
                $ticket->costs()->create([
                    'amount' => $data['cost_amount'],
                    'description' => $data['cost_description'] ?? null,
                    'incurred_at' => $data['cost_incurred_at'],
                    'user_id' => $data['user_id'],
                ]);
            }

            return $ticket;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $files
     */
    public function updateTicket(Ticket $ticket, array $data, array $tagNames = [], array $files = []): Ticket
    {
        $newStatus = TicketStatus::from($data['status']);

        if (! $this->stateMachine->canTransition($ticket->status, $newStatus)) {
            throw new \InvalidArgumentException("Cannot transition from {$ticket->status->label()} to {$newStatus->label()}.");
        }

        if ($newStatus === TicketStatus::Escalated && ! $ticket->childTickets()->exists()) {
            throw new \InvalidArgumentException('A ticket can only be escalated if it has at least one follow-up ticket.');
        }

        if ($newStatus === TicketStatus::Closed && $ticket->status !== TicketStatus::Closed) {
            $data['closed_date'] = now()->toDateString();
        }

        DB::transaction(function () use ($data, $tagNames, $files, $ticket) {
            $ticket->update($data);
            $this->syncTags($ticket, $tagNames);
            $this->addMedia($ticket, $files, 'documents');
        });

        return $ticket;
    }

    /**
     * @param  list<TicketStatus>  $allowedTransitions
     * @return list<TicketStatus>
     */
    public function getAllowedStatuses(Ticket $ticket): array
    {
        return array_filter(
            $this->stateMachine->allowedTransitions($ticket->status),
            fn (TicketStatus $status) => $status !== TicketStatus::Escalated || $ticket->childTickets->isNotEmpty(),
        );
    }

    /** @param  array<int, string>  $tagNames */
    private function syncTags(Ticket $ticket, array $tagNames): void
    {
        $tagIds = collect($tagNames)
            ->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id)
            ->toArray();
        $ticket->syncTags($tagIds);
    }

    /** @param  array<int, UploadedFile>  $files */
    private function addMedia(Ticket $ticket, array $files, string $collection): void
    {
        foreach ($files as $file) {
            $ticket->addMedia($file)->toMediaCollection($collection);
        }
    }
}
