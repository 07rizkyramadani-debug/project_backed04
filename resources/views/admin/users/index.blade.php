<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <h3 class="text-lg font-semibold mb-4">Daftar Semua User</h3>

                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="border-b text-gray-600">
                                <th class="py-2 pe-4">#</th>
                                <th class="py-2 pe-4">Nama</th>
                                <th class="py-2 pe-4">Email</th>
                                <th class="py-2 pe-4">Role</th>
                                <th class="py-2 pe-4">Ubah Role</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b">
                                    <td class="py-2 pe-4">{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="py-2 pe-4">{{ $user->name }}</td>
                                    <td class="py-2 pe-4">{{ $user->email }}</td>
                                    <td class="py-2 pe-4">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->isAdmin() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="py-2 pe-4">
                                        @if (auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="border-gray-300 rounded-md text-sm">
                                                    <option value="user" @selected($user->role === 'user')>user</option>
                                                    <option value="admin" @selected($user->role === 'admin')>admin</option>
                                                </select>
                                                <x-secondary-button type="submit">Simpan</x-secondary-button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">(akun Anda)</span>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        @if (auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Hapus user {{ $user->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button>Hapus</x-danger-button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
