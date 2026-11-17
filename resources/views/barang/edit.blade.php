@extends('layouts.app')

@section('title', 'Ubah Barang')
@section('judul', 'Ubah Barang')
@section('tag', 'Inventaris')
@section('deskripsi', 'Perbarui informasi produk.')

@section('aksi')
    <a href="{{ route('barang.index') }}" class="btn btn-light">&larr; Kembali ke Barang</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('barang.update', $barang->nomor_barang) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="jenis">Jenis Barang</label>
                <select id="jenis" name="jenis" @disabled($terkunci) required>
                    @foreach (\App\Support\Referensi::JENIS as $kode => $label)
                        <option value="{{ $kode }}" @selected(old('jenis', $barang->kode_jenis) === $kode)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input
                    id="nama"
                    name="nama"
                    type="text"
                    maxlength="255"
                    value="{{ old('nama', $barang->produk) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="idsatuan">Satuan</label>
                <select id="idsatuan" name="idsatuan" @disabled($terkunci) required>
                    @foreach ($satuan as $item)
                        <option
                            value="{{ $item->nomor_satuan }}"
                            @selected((string) old('idsatuan', $barang->nomor_satuan) === (string) $item->nomor_satuan)
                        >
                            {{ $item->satuan }}
                        </option>
                    @endforeach
                </select>
                @if ($terkunci)
                    <p class="field-note">Jenis dan satuan dikunci karena barang ini sudah memiliki riwayat transaksi.</p>
                @endif
            </div>

            <div class="form-group">
                <label for="harga">Harga Dasar (Rp)</label>
                <input
                    id="harga"
                    name="harga"
                    type="number"
                    min="1"
                    step="1"
                    value="{{ old('harga', $barang->harga_dasar) }}"
                    required
                >
                <p class="field-note">Perubahan harga tidak mengubah harga pada transaksi yang sudah tercatat.</p>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="1" @selected((string) old('status', $barang->status) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('status', $barang->status) === '0')>Nonaktif</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('barang.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
