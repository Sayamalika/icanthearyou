@extends('layouts.app')

@section('title', 'Tambah Vendor')
@section('judul', 'Tambah Vendor')
@section('tag', 'Mitra')
@section('deskripsi', 'Masukkan informasi vendor baru.')

@section('aksi')
    <a href="{{ route('vendor.index') }}" class="btn btn-light">&larr; Kembali ke Vendor</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('vendor.store') }}">
            @csrf

            <div class="form-group">
                <label for="nama_vendor">Nama Vendor</label>
                <input
                    id="nama_vendor"
                    name="nama_vendor"
                    type="text"
                    maxlength="100"
                    value="{{ old('nama_vendor') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="badan_hukum">Bentuk Badan Hukum</label>
                <select id="badan_hukum" name="badan_hukum" required>
                    <option value="">Pilih bentuk badan hukum</option>
                    <option value="1" @selected(old('badan_hukum') === '1')>PT</option>
                    <option value="2" @selected(old('badan_hukum') === '2')>CV</option>
                    <option value="0" @selected(old('badan_hukum') === '0')>Non Badan Hukum</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Vendor</button>
                <a href="{{ route('vendor.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
