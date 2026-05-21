<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\Student;
use App\Services\ReportDocxExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportDocxExporter $docx
    ) {}

    public function index(Request $request): View
    {
        $period = $request->input('period', 'all');
        if (! in_array($period, ['all', 'month', 'year'], true)) {
            $period = 'all';
        }

        $analytics = $this->buildAnalytics($period);

        return view('reports.index', [
            'analytics' => $analytics,
            'period' => $period,
            'sessions' => Appointment::with(['student', 'counselor'])
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->get(),
        ]);
    }

    public function exportSummary(Request $request): BinaryFileResponse
    {
        $period = $request->input('period', 'all');
        if (! in_array($period, ['all', 'month', 'year'], true)) {
            $period = 'all';
        }

        $analytics = $this->buildAnalytics($period);
        $sessions = $this->scopedAppointments($period)
            ->with(['student', 'counselor'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return $this->docx->analyticsDocument($analytics, $sessions);
    }

    public function exportStudent(Request $request, Student $student): BinaryFileResponse
    {
        $sessions = $this->filteredSessions($request)->where('student_id', $student->id);

        return $this->docx->studentDocument($student, $sessions);
    }

    public function updateSessionNotes(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $appointment->load('student');
        $notes = filled($validated['notes'] ?? null) ? $validated['notes'] : null;
        $appointment->update(['notes' => $notes]);

        $query = array_filter(['period' => $request->input('period')]);

        $message = $notes
            ? 'Session notes saved for '.$appointment->student->name.'.'
            : 'Session notes removed for '.$appointment->student->name.'.';

        return redirect()
            ->route('reports.index', $query)
            ->withFragment('session-records')
            ->with('success', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAnalytics(string $period): array
    {
        $appointments = $this->scopedAppointments($period)->with(['student', 'counselor'])->get();
        $total = $appointments->count();
        $completed = $appointments->where('status', 'completed')->count();

        $students = Student::all();
        $counselors = Counselor::all();

        return [
            'period_label' => match ($period) {
                'month' => 'This month ('.now()->format('F Y').')',
                'year' => 'This year ('.now()->year.')',
                default => 'All time',
            },
            'students' => [
                'total' => $students->count(),
                'active' => $students->where('status', 'active')->count(),
                'inactive' => $students->where('status', 'inactive')->count(),
            ],
            'counselors' => [
                'total' => $counselors->count(),
            ],
            'appointments' => [
                'total' => $total,
                'completed' => $completed,
                'scheduled' => $appointments->where('status', 'scheduled')->count(),
                'cancelled' => $appointments->where('status', 'cancelled')->count(),
                'no_show' => $appointments->where('status', 'no-show')->count(),
                'with_notes' => $appointments->filter(fn ($a) => filled($a->notes))->count(),
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'notes_rate' => $total > 0 ? round(($appointments->filter(fn ($a) => filled($a->notes))->count() / $total) * 100) : 0,
            ],
            'by_status' => $this->groupedWithPercent(
                $appointments->groupBy('status')->map(fn ($g) => $g->count()),
                $total
            ),
            'by_type' => $this->groupedWithPercent(
                $appointments->groupBy('type')->map(fn ($g) => $g->count())->sortDesc(),
                $total
            ),
            'by_program' => $appointments
                ->groupBy(fn ($a) => $a->student->program)
                ->map(fn ($g, $label) => ['label' => $label, 'count' => $g->count()])
                ->sortByDesc('count')
                ->values()
                ->all(),
            'by_counselor' => $appointments
                ->groupBy(fn ($a) => $a->counselor->name)
                ->map(fn ($g, $label) => [
                    'label' => $label,
                    'count' => $g->count(),
                    'completed' => $g->where('status', 'completed')->count(),
                ])
                ->sortByDesc('count')
                ->values()
                ->all(),
            'by_month' => $this->monthlyBreakdown($appointments),
            'recent' => $appointments
                ->sortByDesc(fn ($a) => $a->date->format('Y-m-d').' '.$a->time)
                ->take(5)
                ->values(),
        ];
    }

    /**
     * @param  Collection<int, Appointment>  $appointments
     * @return list<array{label: string, count: int}>
     */
    private function monthlyBreakdown(Collection $appointments): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $months[$key] = [
                'label' => $date->format('M Y'),
                'count' => 0,
            ];
        }

        foreach ($appointments as $appointment) {
            $key = $appointment->date->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['count']++;
            }
        }

        return array_values($months);
    }

    /**
     * @param  Collection<string, int>  $grouped
     * @return list<array{label: string, count: int, percent: float|int}>
     */
    private function groupedWithPercent(Collection $grouped, int $total): array
    {
        return $grouped->map(function ($count, $label) use ($total) {
            return [
                'label' => ucfirst(str_replace('-', ' ', (string) $label)),
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        })->values()->all();
    }

    private function scopedAppointments(string $period): Builder
    {
        $query = Appointment::query();

        if ($period === 'month') {
            $query->whereYear('date', now()->year)->whereMonth('date', now()->month);
        } elseif ($period === 'year') {
            $query->whereYear('date', now()->year);
        }

        return $query;
    }

    /**
     * @return Collection<int, Appointment>
     */
    private function filteredSessions(Request $request): Collection
    {
        $query = Appointment::with(['student', 'counselor'])
            ->orderByDesc('date')
            ->orderByDesc('time');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->integer('student_id'));
        }

        if ($request->filled('counselor_id')) {
            $query->where('counselor_id', $request->integer('counselor_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->input('to_date'));
        }

        if ($request->boolean('notes_only')) {
            $query->whereNotNull('notes')->where('notes', '!=', '');
        }

        return $query->get();
    }
}
