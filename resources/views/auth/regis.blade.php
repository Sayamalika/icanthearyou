@extends('layouts.guest')

@section('title', 'Registrasi')
@section('judul', 'Registrasi')
@section('deskripsi', 'Buat akun pengguna baru.')

@section('content')
    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="form-group">
            <label for="username">Username</label>
            <input
                id="username"
                name="username"
                type="text"
                maxlength="45"
                value="{{ old('username') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Ulangi Password</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
            >
        </div>

        <div class="form-group">
            <label for="nomor_peran">Role</label>
            <select id="nomor_peran" name="nomor_peran" required>
                <option value="">Pilih role</option>
                @foreach ($roles as $role)
                    <option
                        value="{{ $role->nomor_peran }}"
                        @selected((string) old('nomor_peran') === (string) $role->nomor_peran)
                    >
                        {{ $role->peran }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Daftar</button>
            <a href="{{ route('login') }}" class="auth-link">Sudah punya akun? Login</a>
        </div>
    </form>
@endsection
