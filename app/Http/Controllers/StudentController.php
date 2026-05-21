<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public const PROGRAMS = [
        'Bachelor of Science in Information Technology',
        'Bachelor of Science in Hospitality Management',
        'Bachelor of Elementary Education',
        'Bachelor of Secondary Education',
    ];

    public const YEAR_LEVELS = [
        '1st Year',
        '2nd Year',
        '3rd Year',
        '4th Year',
    ];

    public const STATUSES = [
        'active',
        'inactive',
    ];

    private const NAME_PATTERN = '/^[a-zA-Z]+(?:[\s\'.\-][a-zA-Z]+)*$/';

    private const GMAIL_PATTERN = '/^[a-zA-Z0-9._%+-]+@gmail\.com$/i';

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));

        $query = Student::query()->withCount('appointments')->orderByDesc('created_at');

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('student_id', 'like', "%{$q}%")
                    ->orWhere('program', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $students = $query->get();
        $allStudents = Student::withCount('appointments')->orderBy('name')->get();

        $editingStudent = null;
        if ($request->filled('edit')) {
            $editingStudent = Student::find($request->input('edit'));
        } elseif (old('student_record_id')) {
            $editingStudent = Student::find(old('student_record_id'));
        }

        $openStudentModal = $request->has('add')
            || $request->filled('edit')
            || (session()->has('errors') && old('return_to') === 'students');

        return view('students.index', array_merge(
            self::formData(),
            compact('students', 'allStudents', 'q', 'editingStudent', 'openStudentModal')
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('students.index', ['add' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:'.self::NAME_PATTERN],
            'studentId' => ['required', 'digits:8', 'unique:students,student_id'],
            'email' => ['required', 'email', 'max:255', 'regex:'.self::GMAIL_PATTERN, 'unique:students,email'],
            'program' => ['required', 'string', 'max:255'],
            'yearLevel' => ['required', 'string', 'max:50'],
            'return_to' => ['nullable', 'string', 'in:students'],
        ], self::validationMessages());

        Student::create([
            'student_id' => $validated['studentId'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'program' => $validated['program'],
            'year_level' => $validated['yearLevel'],
            'status' => 'active',
            'last_visit' => now(),
        ]);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student registered successfully: '.$validated['name']);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:'.self::NAME_PATTERN],
            'studentId' => ['required', 'digits:8', Rule::unique('students', 'student_id')->ignore($student->id)],
            'email' => ['required', 'email', 'max:255', 'regex:'.self::GMAIL_PATTERN, Rule::unique('students', 'email')->ignore($student->id)],
            'program' => ['required', 'string', 'max:255'],
            'yearLevel' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'return_to' => ['nullable', 'string', 'in:students'],
        ], self::validationMessages());

        if ($validator->fails()) {
            return redirect()
                ->route('students.index', ['edit' => $student->id])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $student->update([
            'student_id' => $validated['studentId'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'program' => $validated['program'],
            'year_level' => $validated['yearLevel'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student updated successfully: '.$validated['name']);
    }

    public function destroy(Student $student): RedirectResponse
    {
        $name = $student->name;
        $appointmentCount = $student->appointments()->count();

        $student->delete();

        $message = 'Student deleted successfully: '.$name;
        if ($appointmentCount > 0) {
            $message .= ' ('.$appointmentCount.' related appointment(s) removed)';
        }

        return redirect()
            ->route('students.index')
            ->with('success', $message);
    }

    /**
     * @return array<string, string>
     */
    private static function validationMessages(): array
    {
        return [
            'name.regex' => 'Full name may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'studentId.digits' => 'Student ID must be exactly 8 digits.',
            'studentId.unique' => 'This Student ID is already registered.',
            'email.regex' => 'Email must be a valid Gmail address (example: name@gmail.com).',
            'email.unique' => 'This email is already registered.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function formData(): array
    {
        return [
            'programs' => self::PROGRAMS,
            'yearLevels' => self::YEAR_LEVELS,
            'statuses' => self::STATUSES,
        ];
    }
}
