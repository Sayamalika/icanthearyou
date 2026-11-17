@extends('layouts.app')

@section('title', 'Detail Penjualan')
@section('judul', 'Penjualan #' . $penjualan->nomor_penjualan)
@section('tag', 'Kasir')
@section('deskripsi', 'Rincian setiap barang yang terjual pada transaksi ini.')

@section('aksi')
    <a href="{{ route('penjualan.index') }}" class="btn btn-light">&larr; Kembali ke Penjualan</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Ringkasan Penjualan</h2>
        </div>
        <div class="card-body">
            <dl class="record-grid">
                <div><dt>ID Penjualan</dt><dd>#{{ $penjualan->nomor_penjualan }}</dd></div>
                <div><dt>Waktu</dt><dd>{{ $penjualan->waktu_penjualan }}</dd></div>
                <div><dt>Pengguna</dt><dd>{{ $penjualan->nama_pengguna }}</dd></div>
                <div><dt>Margin</dt><dd>{{ number_format($penjualan->persentase_margin, 2, ',', '.') }}%</dd></div>
                <div><dt>Subtotal</dt><dd>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</dd></div>
                <div><dt>PPN</dt><dd>Rp {{ number_format($penjualan->pajak_ppn, 0, ',', '.') }}</dd></div>
                <div><dt>Total</dt><dd>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Barang yang Dijual</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Barang</th>
                        <th>Barang</th>
                        <th>Satuan</th>
                        <th class="kanan">Harga per Barang</th>
                        <th class="kanan">Jumlah</th>
                        <th class="kanan">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td><span class="kode">#{{ $item->nomor_barang }}</span></td>
                            <td class="nama">{{ $item->nama_barang }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td class="uang">Rp {{ number_format($item->harga_perBarang, 0, ',', '.') }}</td>
                            <td class="kanan">{{ $item->jumlah }}</td>
                            <td class="uang">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="kosong">Penjualan ini belum memiliki barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
