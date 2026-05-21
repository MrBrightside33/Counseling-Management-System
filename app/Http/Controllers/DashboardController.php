<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $students = Student::orderBy('name')->get();
        $counselors = Counselor::orderBy('name')->get();

        $activeStudents = $students->where('status', 'active')->count();
        $scheduledAppointments = Appointment::where('status', 'scheduled')->count();
        $completedThisWeek = Appointment::where('status', 'completed')->count();

        $recentAppointments = Appointment::with(['student', 'counselor'])
            ->where('status', 'scheduled')
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'counselors',
            'activeStudents',
            'scheduledAppointments',
            'completedThisWeek',
            'recentAppointments'
        ));
    }
}
