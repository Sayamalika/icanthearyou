@extends('layouts.app')

@section('title', 'Penjualan')
@section('judul', 'Daftar Penjualan')
@section('tag', 'Kasir')
@section('deskripsi', 'Riwayat transaksi penjualan dan ringkasan pembayarannya.')

@section('aksi')
    <a href="/penjualan/create" class="btn btn-primary">Buat Penjualan</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Penjualan</th>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th class="kanan">Margin</th>
                        <th class="kanan">Subtotal</th>
                        <th class="kanan">PPN</th>
                        <th class="kanan">Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $penjualan)
                        <tr>
                            <td><span class="kode">#{{ $penjualan->nomor_penjualan }}</span></td>
                            <td>{{ $penjualan->waktu_penjualan }}</td>
                            <td class="nama">{{ $penjualan->nama_pengguna }}</td>
                            <td class="kanan">{{ number_format($penjualan->persentase_margin, 2, ',', '.') }}%</td>
                            <td class="uang">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                            <td class="uang">Rp {{ number_format($penjualan->pajak_ppn, 0, ',', '.') }}</td>
                            <td class="uang">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('penjualan.show', $penjualan->nomor_penjualan) }}" class="btn btn-light btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="kosong">Belum ada data penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
