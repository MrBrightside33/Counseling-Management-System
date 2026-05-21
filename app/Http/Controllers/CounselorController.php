<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CounselorController extends Controller
{
    public const SPECIALIZATIONS = [
        'Academic Counseling',
        'Career Guidance',
        'Personal Counseling',
        'Mental Health Support',
    ];

    public function index(Request $request): View
    {
        $counselors = Counselor::withCount('appointments')
            ->with(['appointments' => function ($query) {
                $query->with('student')->orderByDesc('date')->orderByDesc('time');
            }])
            ->orderBy('name')
            ->get();

        $openSessionsModal = $request->filled('sessions');
        $activeCounselorId = $request->integer('sessions') ?: null;

        $editingCounselor = null;
        if ($request->filled('edit')) {
            $editingCounselor = Counselor::find($request->input('edit'));
        } elseif (old('counselor_record_id')) {
            $editingCounselor = Counselor::find(old('counselor_record_id'));
        }

        $openCounselorModal = $request->has('add')
            || $request->filled('edit')
            || (session()->has('errors') && old('return_to') === 'counselors');

        return view('counselors.index', array_merge(
            self::formData(),
            compact(
                'counselors',
                'openSessionsModal',
                'activeCounselorId',
                'openCounselorModal',
                'editingCounselor'
            )
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('counselors.index', ['add' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:counselors,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'specialization' => ['required', 'string', 'max:255'],
            'availability' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'return_to' => ['nullable', 'string', 'in:counselors'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('counselors.index', ['add' => 1])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'specialization' => $validated['specialization'],
            'availability' => $validated['availability'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('counselor-avatars', 'public');
        }

        Counselor::create($data);

        return redirect()
            ->route('counselors.index')
            ->with('success', 'Counselor profile created: '.$validated['name']);
    }

    public function update(Request $request, Counselor $counselor): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('counselors', 'email')->ignore($counselor->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'specialization' => ['required', 'string', 'max:255'],
            'availability' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'return_to' => ['nullable', 'string', 'in:counselors'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('counselors.index', ['edit' => $counselor->id])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'specialization' => $validated['specialization'],
            'availability' => $validated['availability'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($counselor);
            $data['avatar'] = $request->file('avatar')->store('counselor-avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($counselor);
            $data['avatar'] = null;
        }

        $counselor->update($data);

        return redirect()
            ->route('counselors.index')
            ->with('success', 'Counselor updated successfully: '.$validated['name']);
    }

    public function destroy(Counselor $counselor): RedirectResponse
    {
        $name = $counselor->name;
        $appointmentCount = $counselor->appointments()->count();

        $this->deleteAvatar($counselor);
        $counselor->delete();

        $message = 'Counselor removed (resigned): '.$name;
        if ($appointmentCount > 0) {
            $message .= ' ('.$appointmentCount.' related appointment(s) removed)';
        }

        return redirect()
            ->route('counselors.index')
            ->with('success', $message);
    }

    private function deleteAvatar(Counselor $counselor): void
    {
        if ($counselor->avatar && Storage::disk('public')->exists($counselor->avatar)) {
            Storage::disk('public')->delete($counselor->avatar);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function formData(): array
    {
        return [
            'specializations' => self::SPECIALIZATIONS,
        ];
    }
}
