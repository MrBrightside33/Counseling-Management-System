@php
    $user = auth()->user();
@endphp
<header class="sticky top-0 z-30 border-b border-gray-200 bg-white">
    <div class="flex items-center justify-between px-4 py-4">
        <button
            type="button"
            data-sidebar-open
            class="rounded-lg border border-gray-200 bg-white p-2 shadow-sm hover:bg-gray-50 lg:hidden"
            aria-label="Open menu"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="flex-1 lg:flex-none"></div>

        <div class="relative" data-profile-menu>
            <button
                type="button"
                data-profile-toggle
                class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-2 py-1.5 shadow-sm transition-colors hover:bg-gray-50 sm:px-3"
                aria-expanded="false"
                aria-haspopup="true"
                aria-label="Account menu"
            >
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
                @include('partials.avatar', ['user' => $user, 'size' => 'md'])
                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                data-profile-dropdown
                class="absolute right-0 z-50 mt-2 hidden w-56 origin-top-right rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                role="menu"
            >
                <div class="border-b border-gray-100 px-4 py-3 sm:hidden">
                    <p class="truncate text-sm font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                </div>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50"
                    role="menuitem"
                >
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Edit profile
                </a>

                <form method="POST" action="{{ route('logout') }}" role="none">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-red-600 transition-colors hover:bg-red-50"
                        role="menuitem"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
