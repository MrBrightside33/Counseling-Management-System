@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
    @php
        $scheduled = $appointments->where('status', 'scheduled');
        $completed = $appointments->where('status', 'completed');
        $cancelled = $appointments->where('status', 'cancelled');
        $openAppointmentModal = request('schedule')
            || request('edit')
            || ($errors->any() && old('return_to') === 'appointments');
    @endphp
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
                <p class="mt-2 text-gray-600">Manage counseling appointments and schedules</p>
            </div>
            <button
                type="button"
                data-appointment-modal-open
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#1009c4] cursor-pointer"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Schedule New Appointment
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold">{{ $scheduled->count() }}</div>
                <div class="text-sm text-gray-600">Scheduled</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-green-600">{{ $completed->count() }}</div>
                <div class="text-sm text-gray-600">Completed</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-purple-800">{{ $appointments->count() }}</div>
                <div class="text-sm text-gray-600">Total</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-orange-600">{{ $cancelled->count() }}</div>
                <div class="text-sm text-gray-600">Cancelled</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold">All appointments</h2>
            </div>
            <div class="overflow-x-auto p-6">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-600">
                            <th class="pb-3 pr-4 font-medium">Date &amp; time</th>
                            <th class="pb-3 pr-4 font-medium">Student</th>
                            <th class="pb-3 pr-4 font-medium">Counselor</th>
                            <th class="pb-3 pr-4 font-medium">Type</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 pr-4">
                                    <div class="font-medium">{{ $appointment->formatted_date }}</div>
                                    <div class="text-gray-500">{{ $appointment->time }}</div>
                                </td>
                                <td class="py-3 pr-4 font-medium">{{ $appointment->student->name }}</td>
                                <td class="py-3 pr-4">{{ $appointment->counselor->name }}</td>
                                <td class="py-3 pr-4">
                                    <span class="rounded border border-gray-200 px-2 py-0.5 text-xs">{{ $appointment->type }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    @php
                                        $colors = [
                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'no-show' => 'bg-orange-100 text-orange-800',
                                        ];
                                        $c = $colors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $c }}">
                                        {{ str_replace('-', ' ', $appointment->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-icon-button
                                            icon="edit"
                                            label="Update appointment for {{ $appointment->student->name }}"
                                            type="button"
                                            data-appointment-edit
                                            data-id="{{ $appointment->id }}"
                                            data-update-url="{{ route('appointments.update', $appointment) }}"
                                            data-student-id="{{ $appointment->student_id }}"
                                            data-counselor-id="{{ $appointment->counselor_id }}"
                                            data-date="{{ $appointment->date->format('Y-m-d') }}"
                                            data-time="{{ $appointment->time }}"
                                            data-type="{{ $appointment->type }}"
                                            data-status="{{ $appointment->status }}"
                                            data-notes="{{ e($appointment->notes ?? '') }}"
                                        />
                                        <form
                                            method="POST"
                                            action="{{ route('appointments.destroy', $appointment) }}"
                                            class="inline"
                                            onsubmit="return confirm(@js('Delete this appointment for '.$appointment->student->name.' on '.$appointment->formatted_date.' at '.$appointment->time.'? This cannot be undone.'));"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <x-icon-button
                                                icon="delete"
                                                label="Delete appointment for {{ $appointment->student->name }}"
                                                variant="danger"
                                                type="submit"
                                            />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    No appointments yet.
                                    <button type="button" data-appointment-modal-open class="font-medium text-[#140DED] hover:underline">Schedule one</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('partials.appointment-modal', [
        'open' => $openAppointmentModal,
        'returnTo' => 'appointments',
        'submitLabel' => 'Add Appointment',
        'appointment' => $editingAppointment ?? null,
    ])
@endsection
