@extends('layouts.guest')

@section('title', 'Login')
@section('judul', 'Login')
@section('deskripsi', 'Masuk menggunakan akun pengguna Anda.')

@section('content')
    <form method="POST" action="{{ route('login.store') }}">
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
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Login</button>
            <p class="auth-link">Hubungi Administrator untuk membuat akun.</p>
        </div>
    </form>
@endsection
