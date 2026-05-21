@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
    @php
        $a = $analytics;
        $exportQuery = array_filter(['period' => $period !== 'all' ? $period : null]);
        $maxMonthCount = max(1, collect($a['by_month'])->max('count') ?? 1);
        $statusColors = [
            'scheduled' => 'bg-blue-500',
            'completed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            'no show' => 'bg-orange-500',
            'no-show' => 'bg-orange-500',
        ];
    @endphp
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reports &amp; Analytics</h1>
                <p class="mt-2 text-gray-600">System-wide overview of students, counselors, and counseling sessions</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-icon-button
                    icon="export"
                    label="Export analytics (DOCX)"
                    variant="primary"
                    :href="route('reports.export.summary', $exportQuery)"
                    class="!p-2.5"
                />
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Analytics period</h2>
                    <p class="mt-1 text-sm text-gray-600">Showing data for: <span class="font-medium text-gray-900">{{ $a['period_label'] }}</span></p>
                </div>
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-2">
                    @foreach (['all' => 'All time', 'month' => 'This month', 'year' => 'This year'] as $value => $label)
                        <button
                            type="submit"
                            name="period"
                            value="{{ $value }}"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $period === $value ? 'bg-[#140DED] text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-600">Total students</p>
                <p class="mt-1 text-3xl font-bold">{{ $a['students']['total'] }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $a['students']['active'] }} active · {{ $a['students']['inactive'] }} inactive</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-600">Counselors</p>
                <p class="mt-1 text-3xl font-bold text-purple-800">{{ $a['counselors']['total'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-600">Total sessions</p>
                <p class="mt-1 text-3xl font-bold text-[#140DED]">{{ $a['appointments']['total'] }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $a['appointments']['scheduled'] }} scheduled</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-600">Completion rate</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ $a['appointments']['completion_rate'] }}%</p>
                <p class="mt-1 text-xs text-gray-500">{{ $a['appointments']['completed'] }} completed</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold">Sessions by status</h2>
                </div>
                <div class="space-y-4 p-6">
                    @forelse ($a['by_status'] as $row)
                        @php
                            $barColor = $statusColors[strtolower($row['label'])] ?? 'bg-gray-400';
                        @endphp
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $row['label'] }}</span>
                                <span class="text-gray-600">{{ $row['count'] }} ({{ $row['percent'] }}%)</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $row['percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No session data for this period.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold">Sessions by counseling type</h2>
                </div>
                <div class="space-y-4 p-6">
                    @forelse ($a['by_type'] as $row)
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $row['label'] }}</span>
                                <span class="text-gray-600">{{ $row['count'] }} ({{ $row['percent'] }}%)</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-[#140DED]" style="width: {{ $row['percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No session data for this period.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold">Monthly session trend</h2>
                <p class="mt-1 text-sm text-gray-600">Last 6 months</p>
            </div>
            <div class="flex items-end justify-between gap-2 px-6 pb-6 pt-4" style="min-height: 12rem">
                @foreach ($a['by_month'] as $row)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <span class="text-xs font-medium text-gray-700">{{ $row['count'] }}</span>
                        <div
                            class="w-full max-w-[3rem] rounded-t-md bg-[#140DED]/80 transition-all"
                            style="height: {{ max(4, ($row['count'] / $maxMonthCount) * 120) }}px"
                            title="{{ $row['label'] }}: {{ $row['count'] }} sessions"
                        ></div>
                        <span class="text-center text-xs text-gray-500">{{ $row['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold">Sessions by program</h2>
                </div>
                <div class="overflow-x-auto p-6 pt-0">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-600">
                                <th class="pb-3 pr-4 font-medium">Program</th>
                                <th class="pb-3 font-medium text-right">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($a['by_program'] as $row)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 pr-4 font-medium">{{ $row['label'] }}</td>
                                    <td class="py-3 text-right">{{ $row['count'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-6 text-center text-gray-500">No data for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold">Counselor workload</h2>
                </div>
                <div class="overflow-x-auto p-6 pt-0">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-600">
                                <th class="pb-3 pr-4 font-medium">Counselor</th>
                                <th class="pb-3 pr-4 font-medium text-right">Sessions</th>
                                <th class="pb-3 font-medium text-right">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($a['by_counselor'] as $row)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 pr-4 font-medium">{{ $row['label'] }}</td>
                                    <td class="py-3 pr-4 text-right">{{ $row['count'] }}</td>
                                    <td class="py-3 text-right text-green-700">{{ $row['completed'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-500">No data for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-purple-200 bg-purple-50/40 p-5 shadow-sm">
                <p class="text-sm text-gray-600">Sessions with notes</p>
                <p class="mt-1 text-2xl font-bold text-purple-700">{{ $a['appointments']['with_notes'] }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $a['appointments']['notes_rate'] }}% of sessions documented</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="mb-3 text-sm font-medium text-gray-700">Recent sessions</p>
                <ul class="space-y-3">
                    @forelse ($a['recent'] as $session)
                        <li class="flex justify-between gap-2 text-sm">
                            <span class="min-w-0 truncate font-medium">{{ $session->student->name }}</span>
                            <span class="shrink-0 text-gray-500">{{ $session->formatted_date }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No sessions in this period.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div id="session-records" class="rounded-xl border border-gray-200 bg-white shadow-sm scroll-mt-6">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold">Session records</h2>
                <p class="mt-1 text-sm text-gray-600">Manage session notes and export per-student reports</p>
            </div>
            <div class="overflow-x-auto p-6">
                <p class="mb-4 text-sm text-gray-600">{{ $sessions->count() }} session record(s)</p>
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-600">
                            <th class="pb-3 pr-4 font-medium">Date &amp; time</th>
                            <th class="pb-3 pr-4 font-medium">Student</th>
                            <th class="pb-3 pr-4 font-medium">Counselor</th>
                            <th class="pb-3 pr-4 font-medium">Type</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 pr-4 font-medium">Session notes</th>
                            <th class="pb-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                            <tr class="border-b border-gray-100 align-top">
                                <td class="py-3 pr-4">
                                    <div class="font-medium">{{ $session->formatted_date }}</div>
                                    <div class="text-gray-500">{{ $session->time }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    <div class="font-medium">{{ $session->student->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->student->student_id }}</div>
                                </td>
                                <td class="py-3 pr-4">{{ $session->counselor->name }}</td>
                                <td class="py-3 pr-4">
                                    <span class="rounded border border-gray-200 px-2 py-0.5 text-xs">{{ $session->type }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    @php
                                        $colors = [
                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'no-show' => 'bg-orange-100 text-orange-800',
                                        ];
                                        $c = $colors[$session->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $c }}">
                                        {{ str_replace('-', ' ', $session->status) }}
                                    </span>
                                </td>
                                <td class="max-w-xs py-3 pr-4">
                                    <p class="line-clamp-3 text-gray-700">{{ $session->notes ?: '—' }}</p>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-icon-button
                                            :icon="$session->notes ? 'notes' : 'notes-add'"
                                            :label="$session->notes ? 'Edit notes for '.$session->student->name : 'Add notes for '.$session->student->name"
                                            type="button"
                                            data-session-notes-open
                                            data-id="{{ $session->id }}"
                                            data-student="{{ $session->student->name }}"
                                            data-date="{{ $session->formatted_date }}"
                                            data-time="{{ $session->time }}"
                                            data-notes="{{ e($session->notes ?? '') }}"
                                            data-update-url="{{ route('reports.session-notes', $session) }}"
                                        />
                                        @if (filled($session->notes))
                                            <form
                                                method="POST"
                                                action="{{ route('reports.session-notes', $session) }}"
                                                class="inline"
                                                onsubmit="return confirm(@js('Remove session notes for '.$session->student->name.' on '.$session->formatted_date.'?'))"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="notes" value="">
                                                @if ($period !== 'all')
                                                    <input type="hidden" name="period" value="{{ $period }}">
                                                @endif
                                                <x-icon-button
                                                    icon="delete"
                                                    label="Remove notes for {{ $session->student->name }}"
                                                    variant="danger"
                                                    type="submit"
                                                />
                                            </form>
                                        @endif
                                        <x-icon-button
                                            icon="export"
                                            label="Export {{ $session->student->name }} sessions (DOCX)"
                                            variant="brand"
                                            :href="route('reports.export.student', ['student' => $session->student_id])"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    No session records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div
        data-session-notes-modal
        class="fixed inset-0 z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="session-notes-modal-title"
    >
        <div data-session-notes-backdrop class="absolute inset-0 bg-black/50"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-xl border border-gray-200 bg-white shadow-xl">
                <div class="flex items-start justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h2 id="session-notes-modal-title" class="text-lg font-semibold text-gray-900">Session notes</h2>
                        <p data-session-notes-subtitle class="mt-1 text-sm text-gray-600"></p>
                    </div>
                    <button type="button" data-session-notes-close class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" aria-label="Close">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form data-session-notes-form method="POST" action="#" class="space-y-4 p-6">
                    @csrf
                    @method('PATCH')
                    @if ($period !== 'all')
                        <input type="hidden" name="period" value="{{ $period }}">
                    @endif
                    <div>
                        <label for="session_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            id="session_notes"
                            name="notes"
                            rows="6"
                            class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                            placeholder="Record counseling session observations, follow-ups, recommendations..."
                        ></textarea>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
                        <button type="submit" class="rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1009c4]">
                            Save notes
                        </button>
                        <x-icon-button
                            icon="delete"
                            label="Remove notes"
                            variant="danger"
                            type="button"
                            data-session-notes-remove
                            class="hidden"
                        />
                        <button type="button" data-session-notes-close class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
