@extends('layouts.app')

@section('title', 'Tambah Role')
@section('judul', 'Tambah Role')
@section('tag', 'Otoritas')
@section('deskripsi', 'Tambahkan nama role baru ke dalam daftar.')

@section('aksi')
    <a href="{{ route('role.index') }}" class="btn btn-light">&larr; Kembali ke Role</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('role.store') }}">
            @csrf

            <div class="form-group">
                <label for="nama_role">Nama Role</label>
                <input
                    id="nama_role"
                    name="nama_role"
                    type="text"
                    maxlength="100"
                    value="{{ old('nama_role') }}"
                    required
                >
            </div>

            <p class="page-desc">
                Hak akses aplikasi saat ini tetap ditetapkan untuk Administrator, Kasir, dan Staf Gudang.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Role</button>
                <a href="{{ route('role.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
