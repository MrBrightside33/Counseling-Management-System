@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">
                Welcome to Cordova Public College Counseling Management System
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-600">Active Students</p>
                <p class="mt-2 text-3xl font-bold text-black">{{ $activeStudents }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-600">Scheduled Appointments</p>
                <p class="mt-2 text-3xl font-bold text-red-600">{{ $scheduledAppointments }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-600">Active Counselors</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ $counselors->count() }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-600">Completed Sessions</p>
                <p class="mt-2 text-3xl font-bold text-purple-70">{{ $completedThisWeek }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold">Upcoming Appointments</h2>
                    <a href="{{ route('appointments.index') }}"
                        class="text-sm font-medium text-[#140DED] hover:underline">View all</a>
                </div>
                <div class="divide-y divide-gray-100 p-6">
                    @forelse ($recentAppointments as $appointment)
                                    <div class="flex gap-4 py-4 first:pt-0 last:pb-0">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#140DED]/10 text-[#140DED]">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-gray-900">{{ $appointment->student->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $appointment->counselor->name }}</p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $appointment->formatted_date }} at {{ $appointment->time }} · {{ $appointment->type }}
                                            </p>
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    'no-show' => 'bg-orange-100 text-orange-800',
                                                ];
                                                $statusClass = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                         <span
                                                class="mt-2 inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $statusClass }}">
                                                {{ str_replace('-', ' ', $appointment->status) }}
                                            </span>
                                        </div>
                                    </div>
                    @empty
                        <p class="text-sm text-gray-500">
                            No scheduled appointments yet.
                            <a href="{{ route('appointments.index', ['schedule' => 1]) }}"
                                class="font-medium text-[#140DED] hover:underline">Schedule one</a>
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold">Counselor overview</h2>
                </div>
                <div class="divide-y divide-gray-100 p-6">
                    @forelse ($counselors->take(4) as $counselor)
                        <div class="flex gap-4 py-4 first:pt-0 last:pb-0">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                ✓</div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $counselor->name }}</p>
                                <p class="text-sm text-gray-600">{{ $counselor->specialization }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $counselor->availability ?? 'Availability not set' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No counselors on record.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection