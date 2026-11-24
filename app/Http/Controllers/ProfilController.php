<?php

namespace App\Http\Controllers;

use App\Services\AssetStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class ProfilController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['laporans.fotos', 'laporans.kategori', 'laporans.lokasi']);

        $stats = [
            'total_laporan' => $user->laporans->count(),
            'laporan_hilang' => $user->laporans->where('tipe', 'hilang')->count(),
            'laporan_ditemukan' => $user->laporans->where('tipe', 'ditemukan')->count(),
            'barang_dikembalikan' => $user->laporans->where('status', 'selesai')->count(),
        ];

        return view('profil.show', compact('user', 'stats'));
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
}
