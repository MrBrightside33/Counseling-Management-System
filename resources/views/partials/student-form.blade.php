@php
    $isEdit = isset($student);
    $formAction = $isEdit ? route('students.update', $student) : route('students.store');
@endphp

<form
    data-student-form
    data-store-url="{{ route('students.store') }}"
    method="POST"
    action="{{ $formAction }}"
    class="space-y-5"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="return_to" value="students">
    <input type="hidden" name="student_record_id" id="student_record_id" value="{{ old('student_record_id', $student->id ?? '') }}">

    <div class="space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">Personal information</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="student_name" class="mb-1 block text-sm font-medium text-gray-700">Full name *</label>
                <input
                    id="student_name"
                    name="name"
                    type="text"
                    required
                    value="{{ old('name', $student->name ?? '') }}"
                    data-student-name-input
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                    placeholder="e.g., John Casagan"
                    title="Letters only (name)"
                >
                <p class="mt-1 text-xs text-gray-500">Letters, spaces, hyphens, and apostrophes only.</p>
                <p data-student-name-hint class="mt-1 hidden text-sm text-amber-600" role="alert"></p>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="student_studentId" class="mb-1 block text-sm font-medium text-gray-700">Student ID *</label>
                <input
                    id="student_studentId"
                    name="studentId"
                    type="text"
                    required
                    inputmode="numeric"
                    pattern="\d{8}"
                    maxlength="8"
                    minlength="8"
                    value="{{ old('studentId', $student->student_id ?? '') }}"
                    data-student-id-input
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                    placeholder="8 digits, e.g. 20230882"
                    title="Student ID must be exactly 8 digits"
                >
                <p class="mt-1 text-xs text-gray-500">Numbers only. Must be exactly 8 digits.</p>
                <p data-student-id-hint class="mt-1 hidden text-xs text-amber-600" role="alert"></p>
                @error('studentId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div>
            <label for="student_email" class="mb-1 block text-sm font-medium text-gray-700">Email address *</label>
            <input
                id="student_email"
                name="email"
                type="email"
                required
                value="{{ old('email', $student->email ?? '') }}"
                data-student-email-input
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                placeholder="name@gmail.com"
                title="Gmail address only"
            >
            <p class="mt-1 text-xs text-gray-500">Gmail address only (must end with @gmail.com).</p>
            <p data-student-email-hint class="mt-1 hidden text-xs text-amber-600" role="alert"></p>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-4 border-t border-gray-100 pt-4">
        <h3 class="text-sm font-semibold text-gray-900">Academic information</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="student_program" class="mb-1 block text-sm font-medium text-gray-700">Program *</label>
                <select
                    id="student_program"
                    name="program"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                >
                    <option value="">Select program</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program }}" @selected(old('program', $student->program ?? '') === $program)>{{ $program }}</option>
                    @endforeach
                </select>
                @error('program')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="student_yearLevel" class="mb-1 block text-sm font-medium text-gray-700">Year level *</label>
                <select
                    id="student_yearLevel"
                    name="yearLevel"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                >
                    <option value="">Select year level</option>
                    @foreach ($yearLevels as $year)
                        <option value="{{ $year }}" @selected(old('yearLevel', $student->year_level ?? '') === $year)>{{ $year }}</option>
                    @endforeach
                </select>
                @error('yearLevel')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div data-student-status-field class="{{ $isEdit ? '' : 'hidden' }}">
            <label for="student_status" class="mb-1 block text-sm font-medium text-gray-700">Status *</label>
            <select
                id="student_status"
                name="status"
                class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $student->status ?? 'active') === $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center">
        <button
            type="submit"
            data-student-submit
            class="inline-flex items-center justify-center rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1009c4]"
        >
            {{ $isEdit ? 'Update Student' : 'Save Student' }}
        </button>
        <button
            type="button"
            data-student-modal-close
            class="text-sm font-medium text-gray-600 hover:text-gray-900"
        >
            Cancel
        </button>
    </div>
</form>
