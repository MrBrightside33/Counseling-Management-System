@props([
    'open' => false,
    'returnTo' => 'appointments',
    'submitLabel' => 'Add Appointment',
    'appointment' => null,
])

@php
    $isEdit = $appointment !== null;
@endphp

<div
    data-appointment-modal
    class="fixed inset-0 z-50 {{ $open ? '' : 'hidden' }}"
    role="dialog"
    aria-modal="true"
    aria-labelledby="appointment-modal-title"
>
    <div data-appointment-modal-backdrop class="absolute inset-0 bg-black/50"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl">
            <div class="sticky top-0 z-10 flex items-start justify-between border-b border-gray-100 bg-white px-6 py-4">
                <div>
                    <h2
                        id="appointment-modal-title"
                        data-appointment-modal-title
                        class="text-lg font-semibold text-gray-900"
                    >{{ $isEdit ? 'Edit Appointment' : 'Schedule New Appointment' }}</h2>
                    <p data-appointment-modal-subtitle class="mt-1 text-sm text-gray-600">
                        {{ $isEdit ? 'Update appointment details and status' : 'Assign a student, counselor, date, and counseling type' }}
                    </p>
                </div>
                <button
                    type="button"
                    data-appointment-modal-close
                    class="rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Close"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                @include('partials.appointment-form', [
                    'returnTo' => $returnTo,
                    'submitLabel' => $submitLabel,
                    'appointment' => $appointment,
                ])
            </div>
        </div>
    </div>
</div>
