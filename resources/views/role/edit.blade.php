@extends('layouts.app')

@section('title', 'Ubah Role')
@section('judul', 'Ubah Role')
@section('tag', 'Otoritas')
@section('deskripsi', 'Perbarui nama role.')

@section('aksi')
    <a href="{{ route('role.index') }}" class="btn btn-light">&larr; Kembali ke Role</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('role.update', $role->nomor_peran) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_role">Nama Role</label>
                <input
                    id="nama_role"
                    name="nama_role"
                    type="text"
                    maxlength="100"
                    value="{{ old('nama_role', $role->peran) }}"
                    required
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('role.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
