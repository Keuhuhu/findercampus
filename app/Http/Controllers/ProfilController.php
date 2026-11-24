<?php

namespace App\Http\Controllers;

use App\Models\CampusEmailVerification;
use App\Models\User;
use App\Notifications\VerifyCampusEmail;
use App\Services\AssetStorage;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProfilController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['laporans.fotos', 'laporans.kategori', 'laporans.lokasi', 'campusEmailVerifications']);

        $stats = [
            'total_laporan' => $user->laporans->count(),
            'laporan_hilang' => $user->laporans->where('tipe', 'hilang')->count(),
            'laporan_ditemukan' => $user->laporans->where('tipe', 'ditemukan')->count(),
            'barang_dikembalikan' => $user->laporans->where('status', 'selesai')->count(),
        ];

        return view('profil.show', compact('user', 'stats'));
    }

    public function requestCampusEmailVerification(Request $request)
    {
        $validated = $request->validate([
            'campus_email' => ['required', 'email:filter', 'max:255'],
        ]);
        $campusEmail = mb_strtolower(trim($validated['campus_email']));

        if (! CampusEmailVerification::supportsEmailDomain($campusEmail)) {
            throw ValidationException::withMessages([
                'campus_email' => 'Gunakan email @student.unsri.ac.id, @unsri.ac.id, atau @ilkom.unsri.ac.id.',
            ]);
        }

        $user = $request->user();
        $verifiedEmail = $user->campusEmailVerifications()->where('status', 'verified')->first();

        if ($verifiedEmail?->email === $campusEmail) {
            return back()->with('success', 'Email kampus ini sudah terverifikasi.');
        }

        try {
            $verification = DB::transaction(function () use ($user, $campusEmail): CampusEmailVerification {
                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
                $emailClaim = CampusEmailVerification::query()
                    ->where('email', $campusEmail)
                    ->lockForUpdate()
                    ->first();

                if ($emailClaim && $emailClaim->user_id !== $lockedUser->getKey()) {
                    throw ValidationException::withMessages([
                        'campus_email' => 'Email kampus ini tidak dapat digunakan untuk akun tersebut.',
                    ]);
                }

                $pendingEmail = $lockedUser->campusEmailVerifications()->where('status', 'pending')->first();

                if ($pendingEmail && $pendingEmail->email !== $campusEmail) {
                    $pendingEmail->delete();
                }

                return $lockedUser->campusEmailVerifications()->updateOrCreate(
                    ['status' => 'pending'],
                    ['email' => $campusEmail, 'verified_at' => null],
                );
            });
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages([
                'campus_email' => 'Email kampus ini tidak dapat digunakan untuk akun tersebut.',
            ]);
        }

        try {
            $this->sendCampusEmailVerification($user, $verification);
        } catch (Throwable $exception) {
            Log::warning('Campus email verification notification failed.', [
                'user_id' => $user->getKey(),
                'exception' => $exception::class,
            ]);

            return back()->with('campus-verification-mail-error', 'Tautan belum dapat dikirim. Email kampus tersimpan; coba kirim ulang sebentar lagi.');
        }

        return back()->with('campus-verification-link-sent', true);
    }

    public function resendCampusEmailVerification(Request $request)
    {
        $user = $request->user();
        $verification = $user->campusEmailVerifications()->where('status', 'pending')->first();

        if (! $verification) {
            return back()->with('campus-verification-mail-error', 'Tidak ada email kampus yang menunggu verifikasi.');
        }

        try {
            $this->sendCampusEmailVerification($user, $verification);
        } catch (Throwable $exception) {
            Log::warning('Campus email verification resend failed.', [
                'user_id' => $user->getKey(),
                'exception' => $exception::class,
            ]);

            return back()->with('campus-verification-mail-error', 'Tautan belum dapat dikirim. Silakan coba lagi sebentar.');
        }

        return back()->with('campus-verification-link-sent', true);
    }

    public function edit()
    {
        $user = Auth::user();

        return view('profil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim_nip' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'fakultas' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'whatsapp' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        $emailChanged = $user->email !== $request->email;

        $user->name = $request->name;
        $user->nim_nip = $request->nim_nip;
        $user->fakultas = $request->fakultas;
        $user->email = $request->email;
        $user->whatsapp = $request->whatsapp;

        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->email_verification_required = true;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($emailChanged) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $exception) {
                Log::warning('Email verification notification failed after email change.', [
                    'user_id' => $user->getKey(),
                    'exception' => $exception::class,
                ]);

                return redirect('/profil')->with('success', 'Profil diperbarui. Email baru belum terverifikasi karena tautan belum dapat dikirim.')->with('verification-mail-error', 'Silakan kirim ulang email verifikasi dari profil.');
            }

            return redirect('/profil')->with('success', 'Profil diperbarui. Silakan verifikasi email baru Anda.')->with('status', 'verification-link-sent');
        }

        return redirect('/profil')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAvatar(Request $request, AssetStorage $assetStorage)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'extensions:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        $oldAvatar = $user->avatar;
        $path = $assetStorage->storePublic($request->file('avatar'), 'images/avatars');
        $user->avatar = $path;

        try {
            $user->save();
        } catch (Throwable $exception) {
            $assetStorage->deletePublic($path);

            throw $exception;
        }

        if ($oldAvatar) {
            $assetStorage->deletePublic($oldAvatar);
        }

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function toggleWhatsapp(Request $request)
    {
        $user = Auth::user();
        $user->whatsapp_visible = ! $user->whatsapp_visible;
        $user->save();

        return back()->with('success', 'Pengaturan WhatsApp diperbarui.');
    }

    private function sendCampusEmailVerification(User $user, CampusEmailVerification $verification): void
    {
        $relativeUrl = URL::temporarySignedRoute(
            'verification.campus',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'verification' => $verification->getKey(),
                'hash' => sha1($verification->email),
            ],
            absolute: false,
        );

        Notification::route('mail', [$verification->email => $user->name])
            ->notify(new VerifyCampusEmail($user->name, URL::to($relativeUrl)));
    }
}
