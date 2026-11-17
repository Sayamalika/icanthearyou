@extends('layouts.app')

@section('title', 'Ubah Pengguna')
@section('judul', 'Ubah Pengguna')
@section('tag', 'Akun')
@section('deskripsi', 'Perbarui username, role, atau password pengguna.')

@section('aksi')
    <a href="{{ route('user.index') }}" class="btn btn-light">&larr; Kembali ke Pengguna</a>
@endsection

@section('content')
    @php
        $akunSendiri = (int) auth()->user()->iduser === (int) $pengguna->nomor_pengguna;
    @endphp

    <div class="card form-card">
        <form method="POST" action="{{ route('user.update', $pengguna->nomor_pengguna) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    maxlength="45"
                    value="{{ old('username', $pengguna->nama) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="nomor_peran">Role</label>
                <select id="nomor_peran" name="nomor_peran" required>
                    @foreach ($roles as $role)
                        <option
                            value="{{ $role->nomor_peran }}"
                            @selected((string) old('nomor_peran', $pengguna->nomor_jabatan) === (string) $role->nomor_peran)
                        >
                            {{ $role->peran }}
                        </option>
                    @endforeach
                </select>
                @if ($akunSendiri)
                    <p class="field-note">Anda tidak dapat mengubah role akun Anda sendiri.</p>
                @endif
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input id="password" name="password" type="password" minlength="8" autocomplete="new-password">
                <p class="field-note">Kosongkan bila password tidak ingin diganti.</p>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Ulangi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('user.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
