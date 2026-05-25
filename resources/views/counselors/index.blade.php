@extends('layouts.app')

@section('title', 'Counselors')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Counselors</h1>
                <p class="mt-2 text-gray-600">Manage counselor profiles and session history</p>
            </div>
            <button
                type="button"
                data-counselor-modal-open
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#1009c4] cursor-pointer"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Counselor
            </button>
        </div>

        @php
            $completedSessions = $counselors->sum(fn ($c) => $c->appointments->where('status', 'completed')->count());
            $specializationCount = $counselors->pluck('specialization')->unique()->count();
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold">{{ $counselors->count() }}</div>
                <div class="text-sm text-gray-600">Total Counselors</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-green-600">{{ $completedSessions }}</div>
                <div class="text-sm text-gray-600">Completed Sessions</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-blue-600">{{ $specializationCount }}</div>
                <div class="text-sm text-gray-600">Specializations</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            @forelse ($counselors as $counselor)
                @php
                    $sessions = $counselor->appointments;
                    $stats = [
                        'total' => $sessions->count(),
                        'completed' => $sessions->where('status', 'completed')->count(),
                        'upcoming' => $sessions->where('status', 'scheduled')->count(),
                    ];
                @endphp
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <div class="flex items-start gap-4">
                        @include('partials.counselor-avatar', ['counselor' => $counselor, 'size' => 'lg'])
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $counselor->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $counselor->specialization }}</p>
                            <p class="mt-1 truncate text-xs text-gray-500">{{ $counselor->email }}</p>
                            <p class="text-xs text-gray-500">{{ $counselor->availability ?? 'Availability not set' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 border-t border-gray-100 pt-4 text-center text-sm">
                        <div>
                            <div class="font-bold">{{ $stats['total'] }}</div>
                            <div class="text-gray-500">Sessions</div>
                        </div>
                        <div>
                            <div class="font-bold text-green-600">{{ $stats['completed'] }}</div>
                            <div class="text-gray-500">Done</div>
                        </div>
                        <div>
                            <div class="font-bold text-blue-600">{{ $stats['upcoming'] }}</div>
                            <div class="text-gray-500">Upcoming</div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-end gap-1.5">
                        <x-icon-button
                            icon="history"
                            label="View session history for {{ $counselor->name }}"
                            variant="brand"
                            type="button"
                            data-counselor-sessions-open
                            data-counselor-id="{{ $counselor->id }}"
                            data-counselor-name="{{ $counselor->name }}"
                        />
                        <x-icon-button
                            icon="edit"
                            label="Edit {{ $counselor->name }}"
                            type="button"
                            data-counselor-edit
                            data-id="{{ $counselor->id }}"
                            data-update-url="{{ route('counselors.update', $counselor) }}"
                            data-name="{{ $counselor->name }}"
                            data-email="{{ $counselor->email }}"
                            data-phone="{{ $counselor->phone ?? '' }}"
                            data-specialization="{{ $counselor->specialization }}"
                            data-availability="{{ $counselor->availability ?? '' }}"
                            data-avatar-url="{{ $counselor->avatarUrl() ?? '' }}"
                        />
                        <form
                            method="POST"
                            action="{{ route('counselors.destroy', $counselor) }}"
                            class="inline"
                            onsubmit="return confirm(@js('Remove '.$counselor->name.' (resigned)? This cannot be undone.'.($counselor->appointments_count > 0 ? ' '.$counselor->appointments_count.' related appointment(s) will also be deleted.' : '')));"
                        >
                            @csrf
                            @method('DELETE')
                            <x-icon-button
                                icon="delete"
                                label="Delete {{ $counselor->name }}"
                                variant="danger"
                                type="submit"
                            />
                            </form>
                    </div>
                </div>

                <template id="counselor-sessions-{{ $counselor->id }}">
                    @include('partials.counselor-session-list', [
                        'appointments' => $sessions,
                        'counselor' => $counselor,
                    ])
                </template>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
                    No counselors on record.
                    <button type="button" data-counselor-modal-open class="font-medium text-[#140DED] hover:underline">Add a counselor</button>
                </div>
            @endforelse
        </div>
    </div>

    @include('partials.counselor-sessions-modal', [
        'open' => $openSessionsModal,
        'activeCounselorId' => $activeCounselorId,
    ])

    @include('partials.counselor-modal', [
        'open' => $openCounselorModal,
        'counselor' => $editingCounselor ?? null,
    ])
@endsection
