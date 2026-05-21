<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Sign In') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-gray-900 antialiased">
    <div class="flex min-h-screen">
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-[#140DED] p-12 text-white lg:flex">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-white/10"></div>

            <div class="relative z-10 flex items-center gap-4">
                <img
                    src="{{ asset('images/cpc logo.png') }}"
                    alt="Cordova Public College"
                    class="h-14 w-14 rounded-xl bg-white/20 p-2 object-contain"
                >
                <div>
                    <h1 class="text-lg font-bold leading-tight">Cordova Public College</h1>
                    <p class="text-sm text-white/80">Counseling Management System</p>
                </div>
            </div>

            <div class="relative z-10 max-w-md space-y-4">
                <h2 class="text-3xl font-bold leading-tight">Admin Portal</h2>
                <p class="text-white/85 leading-relaxed">
                    Manage students, counselors, and appointments in one secure place. Sign in to access your dashboard.
                </p>
            </div>

            <p class="relative z-10 text-xs text-white/60">&copy; {{ date('Y') }} Cordova Public College</p>
        </div>

        <div class="flex w-full flex-col justify-center bg-gray-50 px-6 py-12 sm:px-12 lg:w-1/2 lg:px-16">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <img
                        src="{{ asset('images/cpc logo.png') }}"
                        alt="CPC"
                        class="h-12 w-12 rounded-lg bg-[#140DED]/10 p-1.5 object-contain"
                    >
                    <div>
                        <p class="text-sm font-bold text-gray-900">Cordova Public College</p>
                        <p class="text-xs text-gray-500">Counseling Management</p>
                    </div>
                </div>

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
