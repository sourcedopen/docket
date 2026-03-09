<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Console\Command;

class SchemaCleanseCommand extends Command
{
    protected $signature = 'schema:cleanse';

    protected $description = 'Remove the deprecated fee_paid field from ticket type schemas and ticket custom fields';

    public function handle(): int
    {
        $deprecatedKey = 'fee_paid';

        $schemaCount = $this->cleanseTicketTypeSchemas($deprecatedKey);
        $ticketCount = $this->cleanseTicketCustomFields($deprecatedKey);

        $this->info("Cleaned {$schemaCount} ticket type schema(s) and {$ticketCount} ticket custom field(s).");

        return self::SUCCESS;
    }

    private function cleanseTicketTypeSchemas(string $key): int
    {
        $count = 0;

        TicketType::query()
            ->whereNotNull('schema_definition')
            ->each(function (TicketType $ticketType) use ($key, &$count) {
                $schema = $ticketType->schema_definition;
                $fields = $schema['fields'] ?? [];

                $filtered = array_values(array_filter(
                    $fields,
                    fn (array $field) => ($field['key'] ?? null) !== $key,
                ));

                if (count($filtered) === count($fields)) {
                    return;
                }

                $schema['fields'] = $filtered;
                $ticketType->update(['schema_definition' => $schema]);
                $count++;
            });

        return $count;
    }

    private function cleanseTicketCustomFields(string $key): int
    {
        $count = 0;

        Ticket::query()
            ->whereNotNull('custom_fields')
            ->each(function (Ticket $ticket) use ($key, &$count) {
                $customFields = $ticket->custom_fields;

                if (! array_key_exists($key, $customFields)) {
                    return;
                }

                unset($customFields[$key]);
                $ticket->update(['custom_fields' => $customFields]);
                $count++;
            });

        return $count;
    }
}
