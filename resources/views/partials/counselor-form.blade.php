@php
    $isEdit = isset($counselor);
    $formAction = $isEdit ? route('counselors.update', $counselor) : route('counselors.store');
@endphp

<form
    data-counselor-form
    data-store-url="{{ route('counselors.store') }}"
    method="POST"
    action="{{ $formAction }}"
    enctype="multipart/form-data"
    class="space-y-5"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="return_to" value="counselors">
    <input type="hidden" name="counselor_record_id" id="counselor_record_id" value="{{ old('counselor_record_id', $counselor->id ?? '') }}">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
        <div class="relative shrink-0" data-counselor-avatar-preview-wrap>
            @if ($isEdit && ($counselor->avatar ?? null))
                <img
                    data-counselor-avatar-preview
                    src="{{ $counselor->avatarUrl() }}"
                    alt="{{ $counselor->name }}"
                    class="h-24 w-24 rounded-full object-cover ring-2 ring-gray-100"
                >
                <div
                    data-counselor-avatar-preview-fallback
                    class="hidden h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-[#140DED] to-purple-600 text-2xl font-bold text-white ring-2 ring-gray-100"
                >
                    {{ $counselor->initials() }}
                </div>
            @else
                <img
                    data-counselor-avatar-preview
                    src=""
                    alt=""
                    class="hidden h-24 w-24 rounded-full object-cover ring-2 ring-gray-100"
                >
                <div
                    data-counselor-avatar-preview-fallback
                    class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-[#140DED] to-purple-600 text-2xl font-bold text-white ring-2 ring-gray-100"
                >
                    {{ $isEdit ? $counselor->initials() : '?' }}
                </div>
            @endif
        </div>
        <div class="min-w-0 flex-1 space-y-3">
            <div>
                <label for="counselor_avatar" class="mb-1 block text-sm font-medium text-gray-700">Profile photo</label>
                <input
                    id="counselor_avatar"
                    name="avatar"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                    data-counselor-avatar-input
                    class="block w-full cursor-pointer text-sm text-gray-600 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[#140DED]/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#140DED] hover:file:bg-[#140DED]/20"
                >
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF or WebP. Max 2MB.</p>
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div
                data-counselor-remove-avatar-field
                class="{{ ($isEdit && ($counselor->avatar ?? null)) ? '' : 'hidden' }}"
            >
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input
                        type="checkbox"
                        name="remove_avatar"
                        value="1"
                        class="h-4 w-4 rounded border-gray-300 text-[#140DED] focus:ring-[#140DED]/30"
                    >
                    Remove current photo
                </label>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="counselor_name" class="mb-1 block text-sm font-medium text-gray-700">Full name *</label>
            <input
                id="counselor_name"
                name="name"
                type="text"
                required
                value="{{ old('name', $counselor->name ?? '') }}"
                placeholder="Enter counselor name"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="counselor_email" class="mb-1 block text-sm font-medium text-gray-700">Email address *</label>
            <input
                id="counselor_email"
                name="email"
                type="email"
                required
                value="{{ old('email', $counselor->email ?? '') }}"
                placeholder="counselor@edu.ph"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="counselor_phone" class="mb-1 block text-sm font-medium text-gray-700">Phone number</label>
            <input
                id="counselor_phone"
                name="phone"
                type="tel"
                value="{{ old('phone', $counselor->phone ?? '') }}"
                placeholder="+63 XXX XXX XXXX"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="counselor_specialization" class="mb-1 block text-sm font-medium text-gray-700">Specialization *</label>
            <select
                id="counselor_specialization"
                name="specialization"
                required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
            >
                <option value="">Select specialization</option>
                @foreach ($specializations as $specialization)
                    <option value="{{ $specialization }}" @selected(old('specialization', $counselor->specialization ?? '') === $specialization)>
                        {{ $specialization }}
                    </option>
                @endforeach
            </select>
            @error('specialization')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="counselor_availability" class="mb-1 block text-sm font-medium text-gray-700">Availability schedule</label>
        <input
            id="counselor_availability"
            name="availability"
            type="text"
            value="{{ old('availability', $counselor->availability ?? '') }}"
            placeholder="e.g., Mon–Fri, 9AM–5PM"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
        >
        <p class="mt-1 text-xs text-gray-500">Specify available days and time ranges</p>
        @error('availability')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center">
        <button
            type="submit"
            data-counselor-submit
            class="inline-flex items-center justify-center rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1009c4]"
        >
            {{ $isEdit ? 'Update Counselor' : 'Add Counselor' }}
        </button>
        <button
            type="button"
            data-counselor-modal-close
            class="text-sm font-medium text-gray-600 hover:text-gray-900"
        >
            Cancel
        </button>
    </div>
</form>
