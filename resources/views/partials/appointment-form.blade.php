@php
    $isEdit = isset($appointment);
    $formAction = $isEdit ? route('appointments.update', $appointment) : route('appointments.store');
    $selectedTime = old('time', $appointment->time ?? '');
@endphp

<form
    data-appointment-form
    data-store-url="{{ route('appointments.store') }}"
    method="POST"
    action="{{ $formAction }}"
    class="space-y-5"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="return_to" value="{{ $returnTo }}">
    <input type="hidden" name="appointment_record_id" id="appointment_record_id" value="{{ old('appointment_record_id', $appointment->id ?? '') }}">

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="appointment_student_id" class="mb-1 block text-sm font-medium text-gray-700">Student *</label>
            <select
                id="appointment_student_id"
                name="student_id"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                <option value="">Select student</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected(old('student_id', $appointment->student_id ?? '') == $student->id)>
                        {{ $student->name }} ({{ $student->student_id }})
                    </option>
                @endforeach
            </select>
            @error('student_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if ($students->isEmpty())
                <p class="mt-1 text-xs text-amber-600">
                    <a href="{{ route('students.index', ['add' => 1]) }}" class="font-medium underline">Add a student</a> first.
                </p>
            @endif
        </div>

        <div>
            <label for="appointment_counselor_id" class="mb-1 block text-sm font-medium text-gray-700">Counselor *</label>
            <select
                id="appointment_counselor_id"
                name="counselor_id"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                <option value="">Select counselor</option>
                @foreach ($counselors as $counselor)
                    <option value="{{ $counselor->id }}" @selected(old('counselor_id', $appointment->counselor_id ?? '') == $counselor->id)>
                        {{ $counselor->name }}
                    </option>
                @endforeach
            </select>
            @error('counselor_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="appointment_date" class="mb-1 block text-sm font-medium text-gray-700">Date *</label>
            <input
                id="appointment_date"
                name="date"
                type="date"
                required
                value="{{ old('date', isset($appointment) ? $appointment->date->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="appointment_time" class="mb-1 block text-sm font-medium text-gray-700">Time *</label>
            <select
                id="appointment_time"
                name="time"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                <option value="">Select time</option>
                @if ($selectedTime && ! in_array($selectedTime, $timeSlots, true))
                    <option value="{{ $selectedTime }}" selected>{{ $selectedTime }}</option>
                @endif
                @foreach ($timeSlots as $slot)
                    <option value="{{ $slot }}" @selected($selectedTime === $slot)>{{ $slot }}</option>
                @endforeach
            </select>
            @error('time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="appointment_type" class="mb-1 block text-sm font-medium text-gray-700">Type of counseling *</label>
            <select
                id="appointment_type"
                name="type"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                <option value="">Select type</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(old('type', $appointment->type ?? '') === $type)>{{ $type }}</option>
                @endforeach
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @unless ($isEdit)
                <p class="mt-1 text-xs text-gray-500">New appointments are automatically set to scheduled.</p>
            @endunless
        </div>

        <div data-appointment-status-field class="{{ $isEdit ? '' : 'hidden' }}">
            <label for="appointment_status" class="mb-1 block text-sm font-medium text-gray-700">Status *</label>
            <select
                id="appointment_status"
                name="status"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $appointment->status ?? 'scheduled') === $status)>
                        {{ ucfirst(str_replace('-', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="appointment_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes (optional)</label>
        <textarea
            id="appointment_notes"
            name="notes"
            rows="3"
            class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            placeholder="Session details or reminders..."
        >{{ old('notes', $appointment->notes ?? '') }}</textarea>
        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center">
        <button
            type="submit"
            data-appointment-submit
            class="inline-flex items-center justify-center rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1009c4]"
            @disabled($students->isEmpty() || $counselors->isEmpty())
        >
            {{ $isEdit ? 'Update Appointment' : $submitLabel }}
        </button>
        @if ($returnTo === 'appointments')
            <button
                type="button"
                data-appointment-modal-close
                class="text-sm font-medium text-gray-600 hover:text-gray-900"
            >
                Cancel
            </button>
        @endif
    </div>
</form>
