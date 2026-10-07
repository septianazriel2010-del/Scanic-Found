@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Kelola Pengguna</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/email..."
               class="rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500 w-full sm:w-80">
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="rounded-lg text-xs focus:border-brand-500 focus:ring-brand-500">
                                    @foreach (['student', 'teacher', 'staff', 'admin'] as $role)
                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs text-brand-600 hover:underline">Simpan</button>
                            </form>
                        </td>
                        <td class="px-4 py-2 text-gray-400">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endsection
