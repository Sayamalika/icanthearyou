@extends('layouts.app')

@section('title', 'Buat Penerimaan')
@section('judul', 'Buat Penerimaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Catat jumlah barang yang diterima untuk pengadaan ini.')

@section('aksi')
    <a href="/penerimaan" class="btn btn-light">&larr; Kembali ke Penerimaan</a>
@endsection

@section('content')
    <div class="card">
        <dl class="record-grid">
            <div><dt>Pengadaan</dt><dd><span class="kode">#{{ $pengadaan->nomor_pengadaan }}</span></dd></div>
            <div><dt>Vendor</dt><dd>{{ $pengadaan->vendor }}</dd></div>
            <div><dt>Waktu Pengadaan</dt><dd>{{ $pengadaan->waktu }}</dd></div>
        </dl>
    </div>

    <form action="/penerimaan/{{ $pengadaan->nomor_pengadaan }}" method="POST">
        @csrf
        <div class="card">
            <div class="table-wrap">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th class="kanan">Harga Satuan</th>
                            <th class="kanan">Jumlah Pesan</th>
                            <th class="kanan">Sudah Diterima</th>
                            <th class="kanan">Sisa</th>
                            <th class="kanan">Diterima Sekarang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="nama">{{ $item->nama_barang }}</td>
                                <td class="uang">Rp {{ number_format($item->harga_tiap_barang, 0, ',', '.') }}</td>
                                <td class="kanan">{{ $item->jumlah_pesanan }}</td>
                                <td class="kanan">{{ $item->jumlah_terima }}</td>
                                <td class="kanan"><span class="badge badge-warn">{{ $item->sisa }}</span></td>
                                <td class="kanan">
                                    <input
                                        type="number"
                                        name="items[{{ $item->nomor_detail_pengadaan }}][jumlah_terima]"
                                        min="0"
                                        max="{{ $item->sisa }}"
                                        value="{{ old('items.' . $item->nomor_detail_pengadaan . '.jumlah_terima', 0) }}"
                                        required
                                    >
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="kosong">Tidak ada sisa barang untuk diterima.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (count($items) > 0)
            <button type="submit" class="btn btn-primary">Simpan Penerimaan</button>
        @endif
    </form>
@endsection
