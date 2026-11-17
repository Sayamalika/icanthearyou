@extends('layouts.app')

@section('title', 'Detail Barang')
@section('judul', $b->produk)
@section('tag', 'Inventaris')
@section('deskripsi', 'Informasi produk dan pergerakan stok.')

@section('aksi')
    <a href="/barang" class="btn btn-light">&larr; Kembali ke Barang</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Informasi Barang</h2>
        </div>
        <div class="card-body">
            <dl class="record-grid">
                <div><dt>Kategori</dt><dd>{{ $b->kategori }}</dd></div>
                <div><dt>Satuan</dt><dd>{{ $b->satuan }}</dd></div>
                <div><dt>Harga Dasar</dt><dd>Rp {{ number_format($b->harga_dasar, 0, ',', '.') }}</dd></div>
                <div>
                    <dt>Harga Jual (Margin Aktif)</dt>
                    <dd>{{ $b->harga_akhir === null ? '-' : 'Rp ' . number_format($b->harga_akhir, 0, ',', '.') }}</dd>
                </div>
                <div><dt>Stok Terkini</dt><dd>{{ $b->stok_terkini }}</dd></div>
                <div>
                    <dt>Status</dt>
                    <dd>
                        <span class="badge {{ $b->status == 1 ? 'badge-ok' : 'badge-off' }}">
                            {{ $b->status == 1 ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Riwayat Kartu Stok</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Jenis Transaksi</th>
                        <th class="kanan">Masuk</th>
                        <th class="kanan">Keluar</th>
                        <th class="kanan">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stok as $k)
                        <tr>
                            <td>{{ $k->waktu_transaksi }}</td>
                            <td>{{ $k->proses_transaksi === 'T' ? 'Penerimaan' : ($k->proses_transaksi === 'J' ? 'Penjualan' : $k->proses_transaksi) }}</td>
                            <td class="kanan">{{ $k->total_in }}</td>
                            <td class="kanan">{{ $k->total_out }}</td>
                            <td class="kanan">{{ $k->jumlah_sekarang }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="kosong">Belum ada pergerakan stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
