<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CampusEmailVerification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

    public function verifyCampusEmail(int $verification, string $hash): Response
    {
        $isVerified = DB::transaction(function () use ($verification, $hash): bool {
            $campusEmailVerification = CampusEmailVerification::query()
                ->lockForUpdate()
                ->find($verification);

            if (! $campusEmailVerification
                || ! hash_equals(sha1($campusEmailVerification->email), $hash)
                || ! CampusEmailVerification::supportsEmailDomain($campusEmailVerification->email)) {
                return false;
            }

            $user = User::query()->lockForUpdate()->find($campusEmailVerification->user_id);

            if (! $user) {
                return false;
            }

            if ($campusEmailVerification->status === 'verified') {
                return true;
            }

            if ($campusEmailVerification->status !== 'pending') {
                return false;
            }

            $user->campusEmailVerifications()
                ->where('status', 'verified')
                ->whereKeyNot($campusEmailVerification->getKey())
                ->delete();

            $campusEmailVerification->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);

            $user->forceFill(['is_verified' => true])->save();

            return true;
        });

        return response()->view(
            'auth.campus-email-verification-result',
            ['verified' => $isVerified],
            $isVerified ? Response::HTTP_OK : Response::HTTP_FORBIDDEN,
        );
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
