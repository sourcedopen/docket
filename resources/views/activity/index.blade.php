<x-layouts.app title="Activity — {{ config('app.name') }}" page-title="Activity Log">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Event</th>
                            <th>Subject</th>
                            <th>By</th>
                            <th>Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="text-sm text-base-content/60 whitespace-nowrap">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                                <td>
                                    @php
                                        $eventColors = [
                                            'created' => 'badge-success',
                                            'updated' => 'badge-info',
                                            'deleted' => 'badge-error',
                                        ];
                                    @endphp
                                    <span class="badge badge-sm {{ $eventColors[$activity->event] ?? 'badge-ghost' }}">
                                        {{ $activity->event ?? $activity->description }}
                                    </span>
                                </td>
                                <td>
                                    @if ($activity->subject)
                                        @php
                                            $subjectType = class_basename($activity->subject_type);
                                            $subjectLabel = $activity->subject->reference_number
                                                ?? $activity->subject->title
                                                ?? $activity->subject->name
                                                ?? "#{$activity->subject_id}";
                                        @endphp
                                        <span class="badge badge-outline badge-sm mr-1">{{ $subjectType }}</span>
                                        @if ($activity->subject instanceof \App\Models\Ticket)
                                            <a href="{{ route('tickets.show', $activity->subject) }}"
                                                class="link link-hover text-sm">{{ $subjectLabel }}</a>
                                        @elseif ($activity->subject instanceof \App\Models\Contact)
                                            <a href="{{ route('contacts.show', $activity->subject) }}"
                                                class="link link-hover text-sm">{{ $subjectLabel }}</a>
                                        @else
                                            <span class="text-sm">{{ $subjectLabel }}</span>
                                        @endif
                                    @else
                                        <span class="text-base-content/40 text-sm">{{ class_basename($activity->subject_type) }}
                                            #{{ $activity->subject_id }}</span>
                                    @endif
                                </td>
                                <td class="text-sm">{{ $activity->causer?->name ?? 'System' }}</td>
                                <td class="text-sm max-w-xs">
                                    @php
                                        $attrs = $activity->properties['attributes'] ?? [];
                                        $old = $activity->properties['old'] ?? [];
                                    @endphp
                                    @if (!empty($attrs))
                                        <div class="space-y-0.5 text-xs">
                                            @foreach (array_slice($attrs, 0, 4, true) as $key => $value)
                                                @if (!in_array($key, ['updated_at', 'created_at']))
                                                    <div>
                                                        <span class="font-medium">{{ $key }}:</span>
                                                        @if (isset($old[$key]))
                                                            <span
                                                                class="line-through text-base-content/40">{{ Str::limit(is_array($old[$key]) ? json_encode($old[$key]) : (string) $old[$key], 30) }}</span>
                                                            →
                                                        @endif
                                                        <span>{{ Str::limit(is_array($value) ? json_encode($value) : (string) $value, 30) }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-base-content/50 py-8">No activity recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div class="p-4 border-t border-base-200">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>