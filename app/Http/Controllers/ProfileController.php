<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const NAME_PATTERN = '/^[a-zA-Z]+(?:[\s\'.\-][a-zA-Z]+)*$/';

    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:'.self::NAME_PATTERN],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.regex' => 'Full name may only contain letters, spaces, hyphens, apostrophes, and periods.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user);
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user);
            $user->avatar = null;
        }

        $passwordChanged = ! empty($validated['password']);

        if ($passwordChanged) {
            $user->password = $validated['password'];
        }

        $user->save();

        $message = $passwordChanged
            ? 'Your password has been changed successfully.'
            : 'Your profile has been updated.';

        return redirect()
            ->route('profile.edit')
            ->with('success', $message)
            ->with('password_changed', $passwordChanged);
    }

    private function deleteAvatar($user): void
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
    }
}
