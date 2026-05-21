@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-2 text-gray-600">Update your admin account information</p>
        </div>

        <div class="mt-6 max-w-2xl rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold">Account details</h2>
            </div>
            <form
                method="POST"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
                data-profile-form
                class="space-y-6 p-6"
            >
                @csrf
                @method('PUT')

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="relative shrink-0" data-avatar-preview-wrap>
                        @if ($user->avatar)
                            <img
                                data-avatar-preview
                                src="{{ $user->avatarUrl() }}"
                                alt="{{ $user->name }}"
                                class="h-24 w-24 rounded-full object-cover ring-2 ring-gray-100"
                            >
                            <div
                                data-avatar-preview-fallback
                                class="hidden h-24 w-24 items-center justify-center rounded-full bg-[#140DED] text-2xl font-medium text-white ring-2 ring-gray-100"
                            >
                                {{ $user->initials() }}
                            </div>
                        @else
                            <img
                                data-avatar-preview
                                src=""
                                alt=""
                                class="hidden h-24 w-24 rounded-full object-cover ring-2 ring-gray-100"
                            >
                            <div
                                data-avatar-preview-fallback
                                class="flex h-24 w-24 items-center justify-center rounded-full bg-[#140DED] text-2xl font-medium text-white ring-2 ring-gray-100"
                            >
                                {{ $user->initials() }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <label for="avatar" class="mb-1 block text-sm font-medium text-gray-700">Profile photo</label>
                            <input
                                id="avatar"
                                name="avatar"
                                type="file"
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                data-avatar-input
                                class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-[#140DED]/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#140DED] hover:file:bg-[#140DED]/20"
                            >
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF or WebP. Max 2MB.</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($user->avatar)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input
                                    type="checkbox"
                                    name="remove_avatar"
                                    value="1"
                                    class="h-4 w-4 rounded border-gray-300 text-[#140DED] focus:ring-[#140DED]/30"
                                >
                                Remove current photo
                            </label>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Full name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name', $user->name) }}"
                        data-profile-name-input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                        placeholder="e.g., Admin Name"
                        title="Letters only (name)"
                    >
                    <p class="mt-1 text-xs text-gray-500">Letters, spaces, hyphens, and apostrophes only.</p>
                    <p data-profile-name-hint class="mt-1 hidden text-sm text-amber-600" role="alert"></p>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email', $user->email) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-900">Change password</h3>
                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep your current password</p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="current_password" class="mb-1 block text-sm font-medium text-gray-700">Current password</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                autocomplete="current-password"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                            >
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">New password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Confirm new password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-gray-100 pt-6">
                    <button
                        type="submit"
                        class="rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#1009c4]"
                    >
                        Save changes
                    </button>
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @if (session('password_changed'))
        <div data-profile-password-changed class="hidden" aria-hidden="true"></div>
    @endif
@endsection
