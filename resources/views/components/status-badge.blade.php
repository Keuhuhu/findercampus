@props(['status'])

@php
    $classes = [
        'aktif' => 'bg-blue-100 text-blue-800 border-blue-200',
        'cocok' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'diklaim' => 'bg-amber-100 text-amber-800 border-amber-200',
        'diverifikasi' => 'bg-purple-100 text-purple-800 border-purple-200',
        'selesai' => 'bg-gray-100 text-gray-800 border-gray-200',
        'dibatalkan' => 'bg-red-100 text-red-800 border-red-200',
        'expired' => 'bg-gray-200 text-gray-600 border-gray-300',
    ];
    
    $labels = [
        'aktif' => 'Aktif',
        'cocok' => 'Ada Kecocokan',
        'diklaim' => 'Dalam Proses Klaim',
        'diverifikasi' => 'Sedang Diverifikasi',
        'selesai' => 'Selesai Dikembalikan',
        'dibatalkan' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
    ];
    
    $colorClass = $classes[$status] ?? $classes['aktif'];
    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
    {{ $label }}
</span>

