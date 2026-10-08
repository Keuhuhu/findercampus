<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['laporans.fotos', 'laporans.kategori', 'laporans.lokasi']);

        $stats = [
            'total_laporan'      => $user->laporans->count(),
            'laporan_hilang'     => $user->laporans->where('tipe', 'hilang')->count(),
            'laporan_ditemukan'  => $user->laporans->where('tipe', 'ditemukan')->count(),
            'barang_dikembalikan'=> $user->laporans->where('status', 'selesai')->count(),
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
            'name'       => ['required', 'string', 'max:255'],
            'nim_nip'    => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'fakultas'   => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'whatsapp'   => ['required', 'string', 'max:20'],
            'password'   => ['nullable', 'min:8', 'confirmed'],
        ]);

        $user->name      = $request->name;
        $user->nim_nip   = $request->nim_nip;
        $user->fakultas  = $request->fakultas;
        $user->email     = $request->email;
        $user->whatsapp  = $request->whatsapp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect('/profil')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::delete($user->avatar);
        }

        $path = $request->file('avatar')->store('images/avatars');
        $user->avatar = $path;
        $user->save();

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function toggleWhatsapp(Request $request)
    {
        $user = Auth::user();
        $user->whatsapp_visible = !$user->whatsapp_visible;
        $user->save();

        return back()->with('success', 'Pengaturan WhatsApp diperbarui.');
    }
}
