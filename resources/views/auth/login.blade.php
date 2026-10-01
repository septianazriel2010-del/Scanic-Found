@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Masuk ke Akun</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            Ingat saya
        </label>

        <button type="submit"
                class="w-full bg-brand-600 text-white py-2 rounded-lg font-medium hover:bg-brand-700">
            Masuk
        </button>
    </form>

    <p class="text-sm text-gray-500 mt-4 text-center">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-brand-600 font-medium">Daftar di sini</a>
    </p>
@endsection
