@extends('layouts.app')

@section('title', 'Tambah Margin')
@section('judul', 'Tambah Margin Penjualan')
@section('tag', 'Harga')
@section('deskripsi', 'Tambahkan persentase margin untuk perhitungan harga jual.')

@section('aksi')
    <a href="{{ route('margin.index') }}" class="btn btn-light">&larr; Kembali ke Margin</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('margin.store') }}">
            @csrf

            <div class="form-group">
                <label for="persen">Persentase Margin</label>
                <input
                    id="persen"
                    name="persen"
                    type="number"
                    min="0"
                    max="999.99"
                    step="0.01"
                    value="{{ old('persen') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="1" @selected(old('status', '1') === '1')>Aktif</option>
                    <option value="0" @selected(old('status') === '0')>Nonaktif</option>
                </select>
            </div>

            <p class="page-desc">
                Margin aktif terbaru akan digunakan untuk menghitung harga jual. Mengaktifkan margin ini akan menonaktifkan margin aktif sebelumnya.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Margin</button>
                <a href="{{ route('margin.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
