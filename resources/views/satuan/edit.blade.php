@extends('layouts.app')

@section('title', 'Ubah Satuan')
@section('judul', 'Ubah Satuan')
@section('tag', 'Inventaris')
@section('deskripsi', 'Perbarui nama dan status satuan.')

@section('aksi')
    <a href="{{ route('satuan.index') }}" class="btn btn-light">&larr; Kembali ke Satuan</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('satuan.update', $satuan->nomor_satuan) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_satuan">Nama Satuan</label>
                <input
                    id="nama_satuan"
                    name="nama_satuan"
                    type="text"
                    maxlength="45"
                    value="{{ old('nama_satuan', $satuan->satuan) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="1" @selected((string) old('status', $satuan->status) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('status', $satuan->status) === '0')>Nonaktif</option>
                </select>
                <p class="field-note">Satuan tidak dapat dinonaktifkan selama masih dipakai barang aktif.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('satuan.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
