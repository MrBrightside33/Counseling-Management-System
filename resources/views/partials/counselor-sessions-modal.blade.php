@props([
    'open' => false,
    'activeCounselorId' => null,
])

@php
    $activeCounselor = $activeCounselorId ? \App\Models\Counselor::find($activeCounselorId) : null;
@endphp

<div
    data-counselor-sessions-modal
    data-active-counselor-id="{{ $activeCounselorId }}"
    data-active-counselor-name="{{ $activeCounselor?->name }}"
    class="fixed inset-0 z-50 {{ $open ? '' : 'hidden' }}"
    role="dialog"
    aria-modal="true"
    aria-labelledby="counselor-sessions-modal-title"
>
    <div data-counselor-sessions-backdrop class="absolute inset-0 bg-black/50"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl">
            <div class="sticky top-0 z-10 flex items-start justify-between border-b border-gray-100 bg-white px-6 py-4">
                <div>
                    <h2 id="counselor-sessions-modal-title" data-counselor-sessions-title class="text-lg font-semibold text-gray-900">
                        Session History
                    </h2>
                    <p data-counselor-sessions-subtitle class="mt-1 text-sm text-gray-600">
                        View sessions and update status
                    </p>
                </div>
                <button
                    type="button"
                    data-counselor-sessions-close
                    class="rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Close"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6" data-counselor-sessions-body>
                <p class="text-sm text-gray-500">Select a counselor to view session history.</p>
            </div>
        </div>
    </div>
</div>
