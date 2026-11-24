<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || $user->hasVerifiedEmail() || ! $user->email_verification_required) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->fulfill();
        }

        return redirect()->route('dashboard')->with('success', 'Email Anda berhasil diverifikasi.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || $user->hasVerifiedEmail() || ! $user->email_verification_required) {
            return redirect()->route('dashboard');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            Log::warning('Email verification notification failed.', [
                'user_id' => $user->getKey(),
                'exception' => $exception::class,
            ]);

            return back()->with('verification-mail-error', 'Email verifikasi belum dapat dikirim. Silakan coba lagi beberapa saat.');
        }

        return back()->with('status', 'verification-link-sent');
    }
}
