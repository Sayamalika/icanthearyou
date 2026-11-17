@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('judul', 'Tambah Barang')
@section('tag', 'Inventaris')
@section('deskripsi', 'Masukkan informasi produk yang akan ditambahkan.')

@section('aksi')
    <a href="/barang" class="btn btn-light">&larr; Kembali ke Barang</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="/barang/tambah">
            @csrf

            <div class="form-group">
                <label for="jenis">Jenis Barang</label>
                <select id="jenis" name="jenis" required>
                    <option value="">Pilih jenis</option>
                    @foreach ($jenisBarang as $jenis)
                        <option value="{{ $jenis->kode_jenis }}" @selected(old('jenis') === $jenis->kode_jenis)>
                            {{ $jenis->kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input id="nama" name="nama" type="text" maxlength="255" value="{{ old('nama') }}" required>
            </div>

            <div class="form-group">
                <label for="idsatuan">Satuan</label>
                <select id="idsatuan" name="idsatuan" required>
                    <option value="">Pilih satuan</option>
                    @foreach ($satuan as $item)
                        <option
                            value="{{ $item->nomor_satuan }}"
                            @selected((string) old('idsatuan') === (string) $item->nomor_satuan)
                        >
                            {{ $item->satuan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga Dasar (Rp)</label>
                <input id="harga" name="harga" type="number" min="1" step="1" value="{{ old('harga') }}" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Barang</button>
                <a href="/barang" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
