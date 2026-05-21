@php
    use App\Http\Controllers\AppointmentController;
    $statuses = AppointmentController::STATUSES;
    $statusColors = [
        'scheduled' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'no-show' => 'bg-orange-100 text-orange-800',
    ];
@endphp

@if ($appointments->isEmpty())
    <p class="py-8 text-center text-sm text-gray-500">No sessions recorded for this counselor yet.</p>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-600">
                    <th class="pb-3 pr-4 font-medium">Date &amp; time</th>
                    <th class="pb-3 pr-4 font-medium">Student</th>
                    <th class="pb-3 pr-4 font-medium">Type</th>
                    <th class="pb-3 pr-4 font-medium">Current status</th>
                    <th class="pb-3 font-medium">Update status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $appointment)
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-4">
                            <div class="font-medium">{{ $appointment->formatted_date }}</div>
                            <div class="text-gray-500">{{ $appointment->time }}</div>
                        </td>
                        <td class="py-3 pr-4">
                            <div class="font-medium">{{ $appointment->student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $appointment->student->student_id }}</div>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="rounded border border-gray-200 px-2 py-0.5 text-xs">{{ $appointment->type }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            @php $c = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $c }}">
                                {{ str_replace('-', ' ', $appointment->status) }}
                            </span>
                        </td>
                        <td class="py-3">
                            <form
                                method="POST"
                                action="{{ route('appointments.update-status', $appointment) }}"
                                class="flex flex-wrap items-center gap-2"
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="counselor_id" value="{{ $counselor->id }}">
                                <select
                                    name="status"
                                    class="rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-[#140DED] focus:outline-none focus:ring-1 focus:ring-[#140DED]"
                                >
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($appointment->status === $status)>
                                            {{ ucfirst(str_replace('-', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-icon-button
                                    icon="save"
                                    label="Update status for {{ $appointment->student->name }}"
                                    variant="primary"
                                    type="submit"
                                />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
