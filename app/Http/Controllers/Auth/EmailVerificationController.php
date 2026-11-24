<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function verify(int $id, string $hash): Response
    {
        $user = User::query()->find($id);

        if (! $user instanceof User || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->view('auth.email-verification-result', ['verified' => false], Response::HTTP_FORBIDDEN);
        }

        if (! $user->hasVerifiedEmail()) {
            if (! $user->markEmailAsVerified()) {
                return response()->view('auth.email-verification-result', ['verified' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            event(new Verified($user));
        }

        return response()->view('auth.email-verification-result', ['verified' => true]);
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
