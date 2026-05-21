@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Students</h1>
                <p class="mt-2 text-gray-600">Manage student records and information</p>
            </div>
            <button
                type="button"
                data-student-modal-open
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#140DED] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#1009c4] cursor-pointer"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Student
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-black">{{ $allStudents->count() }}</div>
                <div class="text-sm text-gray-600">Total Students</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-green-600">{{ $allStudents->where('status', 'active')->count() }}</div>
                <div class="text-sm text-gray-600">Active Students</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-2xl font-bold text-blue-600">{{ $allStudents->pluck('program')->unique()->count() }}</div>
                <div class="text-sm text-gray-600">Programs</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold">Student Records</h2>
                <form method="get" action="{{ route('students.index') }}" class="w-full sm:w-64">
                    <input
                        type="search"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Search students..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                    >
                </form>
            </div>
            <div class="overflow-x-auto p-6 pt-0">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-600">
                            <th class="pb-3 pr-4 font-medium">Student ID</th>
                            <th class="pb-3 pr-4 font-medium">Name</th>
                            <th class="pb-3 pr-4 font-medium">Program</th>
                            <th class="pb-3 pr-4 font-medium">Year</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 pr-4 font-medium">Last Visit</th>
                            <th class="pb-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 pr-4 font-medium">{{ $student->student_id }}</td>
                                <td class="py-3 pr-4">
                                    <div class="font-medium">{{ $student->name }}</div>
                                    <div class="text-gray-500">{{ $student->email }}</div>
                                </td>
                                <td class="py-3 pr-4">{{ $student->program }}</td>
                                <td class="py-3 pr-4">{{ $student->year_level }}</td>
                                <td class="py-3 pr-4">
                                    @php
                                        $statusClass = $student->status === 'active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $statusClass }}">
                                        {{ $student->status }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">{{ $student->formatted_last_visit }}</td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-icon-button
                                            icon="edit"
                                            label="Edit {{ $student->name }}"
                                            type="button"
                                            data-student-edit
                                            data-id="{{ $student->id }}"
                                            data-update-url="{{ route('students.update', $student) }}"
                                            data-name="{{ $student->name }}"
                                            data-student-id="{{ $student->student_id }}"
                                            data-email="{{ $student->email }}"
                                            data-program="{{ $student->program }}"
                                            data-year-level="{{ $student->year_level }}"
                                            data-status="{{ $student->status }}"
                                        />
                                        <form
                                            method="POST"
                                            action="{{ route('students.destroy', $student) }}"
                                            class="inline"
                                            onsubmit="return confirm(@js('Delete '.$student->name.'? This cannot be undone.'.($student->appointments_count > 0 ? ' Related appointments will also be removed.' : '')));"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <x-icon-button
                                                icon="delete"
                                                label="Delete {{ $student->name }}"
                                                variant="danger"
                                                type="submit"
                                            />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    @if ($q !== '')
                                        No students match your search.
                                    @else
                                        No students yet.
                                        <button type="button" data-student-modal-open class="font-medium text-[#140DED] hover:underline">Add your first student</button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('partials.student-modal', [
        'open' => $openStudentModal,
        'student' => $editingStudent,
    ])
@endsection
