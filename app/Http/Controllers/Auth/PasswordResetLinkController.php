<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'name.required' => 'Nama wajib diisi.'
        ]);

        $user = \App\Models\User::where('email', $request->email)->where('name', $request->name)->first();
        if (!$user) {
            return back()->withInput($request->only('email', 'name'))
                ->withErrors(['email' => 'Nama dan email tidak cocok atau tidak ditemukan.']);
        }

        // Generate token dan redirect ke halaman reset password (tanpa email)
        $token = app('auth.password.broker')->createToken($user);
        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email]);
    }
}
