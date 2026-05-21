@extends('layouts.guest')

@section('title', 'Admin Register')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Create admin account</h2>
        <p class="mt-1 text-sm text-gray-600">Register to access the counseling management system</p>
    </div>

    <div class="mb-6 flex rounded-lg border border-gray-200 bg-white p-1 text-sm font-medium">
        <a href="{{ route('login') }}" class="flex-1 rounded-md px-4 py-2.5 text-center text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900">Sign in</a>
        <span class="flex-1 rounded-md bg-[#140DED] px-4 py-2.5 text-center text-white shadow-sm">Register</span>
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

    <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Full name</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-[#140DED] focus:outline-none focus:ring-2 focus:ring-[#140DED]/20"
                placeholder="John Casagan"
            >
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
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
                autocomplete="new-password"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-[#140DED] focus:outline-none focus:ring-2 focus:ring-[#140DED]/20"
                placeholder="At least 8 characters"
            >
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Confirm password</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-[#140DED] focus:outline-none focus:ring-2 focus:ring-[#140DED]/20"
                placeholder="Repeat password"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-[#140DED] px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#1009c4] focus:outline-none focus:ring-2 focus:ring-[#140DED]/40 focus:ring-offset-2 cursor-pointer"
        >
            Create account
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-600 cursor-pointer">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-[#140DED] hover:underline">Sign in</a>
    </p>
@endsection
