@props([
    'open' => false,
    'counselor' => null,
])

@php
    $isEdit = $counselor !== null;
@endphp

<div
    data-counselor-modal
    class="fixed inset-0 z-50 {{ $open ? '' : 'hidden' }}"
    role="dialog"
    aria-modal="true"
    aria-labelledby="counselor-modal-title"
>
    <div data-counselor-modal-backdrop class="absolute inset-0 bg-black/50"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl">
            <div class="sticky top-0 z-10 flex items-start justify-between border-b border-gray-100 bg-white px-6 py-4">
                <div>
                    <h2
                        id="counselor-modal-title"
                        data-counselor-modal-title
                        class="text-lg font-semibold text-gray-900"
                    >{{ $isEdit ? 'Edit Counselor' : 'Add New Counselor' }}</h2>
                    <p data-counselor-modal-subtitle class="mt-1 text-sm text-gray-600">
                        {{ $isEdit ? 'Update counselor profile, including photo' : 'Register a new counselor profile' }}
                    </p>
                </div>
                <button
                    type="button"
                    data-counselor-modal-close
                    class="rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Close"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                @include('partials.counselor-form', ['counselor' => $counselor])
            </div>
        </div>
    </div>
</div>
