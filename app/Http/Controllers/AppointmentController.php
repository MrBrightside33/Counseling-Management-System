<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public const COUNSELING_TYPES = [
        'Academic Planning',
        'Career Guidance',
        'Personal Issue',
        'Mental Health',
    ];

    public const STATUSES = [
        'scheduled',
        'completed',
        'cancelled',
        'no-show',
    ];

    public const TIME_SLOTS = [
        '8:00 AM',
        '9:00 AM',
        '10:00 AM',
        '11:00 AM',
        '1:00 PM',
        '2:00 PM',
        '3:00 PM',
        '4:00 PM',
        '5:00 PM',
    ];

    public function index(Request $request): View
    {
        $appointments = Appointment::with(['student', 'counselor'])
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        $editingAppointment = null;
        if ($request->filled('edit')) {
            $editingAppointment = Appointment::find($request->input('edit'));
        } elseif (old('appointment_record_id')) {
            $editingAppointment = Appointment::find(old('appointment_record_id'));
        }

        $openAppointmentModal = $request->has('schedule')
            || $request->filled('edit')
            || (session()->has('errors') && old('return_to') === 'appointments');

        return view('appointments.index', array_merge(
            self::formData(),
            compact('appointments', 'editingAppointment', 'openAppointmentModal')
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('appointments.index', ['schedule' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'counselor_id' => ['required', 'exists:counselors,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'return_to' => ['nullable', 'string', 'in:appointments'],
        ]);

        Appointment::create([
            'student_id' => $validated['student_id'],
            'counselor_id' => $validated['counselor_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'type' => $validated['type'],
            'status' => 'scheduled',
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->syncCounselorSessionCount($validated['counselor_id']);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment saved successfully.');
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'exists:students,id'],
            'counselor_id' => ['required', 'exists:counselors,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'return_to' => ['nullable', 'string', 'in:appointments'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('appointments.index', ['edit' => $appointment->id])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $previousCounselorId = $appointment->counselor_id;

        $appointment->update([
            'student_id' => $validated['student_id'],
            'counselor_id' => $validated['counselor_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->syncCounselorSessionCount($validated['counselor_id']);
        if ($previousCounselorId !== (int) $validated['counselor_id']) {
            $this->syncCounselorSessionCount($previousCounselorId);
        }

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
            'counselor_id' => ['required', 'exists:counselors,id'],
        ]);

        $appointment->update(['status' => $validated['status']]);
        $this->syncCounselorSessionCount($appointment->counselor_id);

        return redirect()
            ->route('counselors.index', ['sessions' => $validated['counselor_id']])
            ->with('success', 'Session status updated successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->load('student');
        $counselorId = $appointment->counselor_id;
        $label = $appointment->student->name . ' — ' . $appointment->formatted_date . ' at ' . $appointment->time;

        $appointment->delete();
        $this->syncCounselorSessionCount($counselorId);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted: ' . $label);
    }

    private function syncCounselorSessionCount(int $counselorId): void
    {
        $counselor = Counselor::find($counselorId);
        if (!$counselor) {
            return;
        }

        $counselor->update([
            'total_sessions' => $counselor->appointments()->where('status', 'completed')->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function formData(): array
    {
        return [
            'students' => Student::orderBy('name')->get(),
            'counselors' => Counselor::orderBy('name')->get(),
            'types' => self::COUNSELING_TYPES,
            'statuses' => self::STATUSES,
            'timeSlots' => self::TIME_SLOTS,
        ];
    }
}
