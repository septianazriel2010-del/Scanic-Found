@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Buat Akun Baru</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email Sekolah</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Saya adalah</label>
            <select name="role" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Siswa</option>
                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Guru</option>
                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staf</option>
            </select>
            <p class="text-xs text-gray-400 mt-1">Akun admin dibuat manual oleh pengelola sistem, tidak lewat form ini.</p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <button type="submit"
                class="w-full bg-brand-600 text-white py-2 rounded-lg font-medium hover:bg-brand-700">
            Daftar
        </button>
    </form>

    <p class="text-sm text-gray-500 mt-4 text-center">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-brand-600 font-medium">Masuk di sini</a>
    </p>
@endsection
