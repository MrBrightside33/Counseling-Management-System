@extends('layouts.guest')

@section('title', 'Admin Sign In')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
        <p class="mt-1 text-sm text-gray-600">Sign in to your admin account</p>
    </div>

    <div class="mb-6 flex rounded-lg border border-gray-200 bg-white p-1 text-sm font-medium">
        <span class="flex-1 rounded-md bg-[#140DED] px-4 py-2.5 text-center text-white shadow-sm">Sign in</span>
        <a href="{{ route('register') }}" class="flex-1 rounded-md px-4 py-2.5 text-center text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900">Register</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-[#140DED] focus:outline-none focus:ring-2 focus:ring-[#140DED]/20"
                placeholder="johncasagan@gmail.com"
            >
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-[#140DED] focus:outline-none focus:ring-2 focus:ring-[#140DED]/20"
                placeholder="••••••••"
            >
        </div>

        <div class="flex items-center gap-2">
            <input
                type="checkbox"
                name="remember"
                id="remember"
                value="1"
                checked
                class="h-4 w-4 rounded border-gray-300 text-[#140DED] focus:ring-[#140DED]/30"
            >
            <label for="remember" class="text-sm text-gray-600">Keep me signed in</label>
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-[#140DED] px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#1009c4] focus:outline-none focus:ring-2 focus:ring-[#140DED]/40 focus:ring-offset-2 cursor-pointer"
        >
            Sign in
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-600">
        New admin?
        <a href="{{ route('register') }}" class="font-medium text-[#140DED] hover:underline">Create an account</a>
    </p>
@endsection
