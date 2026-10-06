<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Admin' => '/admin', 'Manajemen Pengguna' => '#']" />
    </x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengguna</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar seluruh pengguna yang terdaftar di FinderCampus.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Pengguna</th>
                        <th class="p-4 font-semibold">NIM/NIP</th>
                        <th class="p-4 font-semibold">Fakultas</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $user->role === 'admin' ? 'bg-primary' : 'bg-gray-200' }} text-{{ $user->role === 'admin' ? 'white' : 'gray-600' }} flex items-center justify-center font-bold text-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm text-gray-700 font-medium">{{ $user->nim_nip ?? '-' }}</td>
                            <td class="p-4 text-sm text-gray-700">{{ $user->fakultas ?? '-' }}</td>
                            <td class="p-4">
                                @if($user->role === 'admin')
                                    <span class="px-2 py-0.5 bg-primary/10 text-primary rounded text-xs font-bold uppercase">Admin</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-bold uppercase">User</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>
</x-app-layout>
