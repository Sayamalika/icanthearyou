@extends('layouts.app')

@section('title', 'Pengadaan')
@section('judul', 'Daftar Pengadaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Riwayat pengadaan dan status pemesanan barang.')

@section('aksi')
    <a href="/pengadaan/create" class="btn btn-primary">Buat Pengadaan</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Pengadaan</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th class="kanan">Subtotal</th>
                        <th class="kanan">PPN</th>
                        <th class="kanan">Total</th>
                        <th>Vendor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $pengadaan)
                        <tr>
                            <td><span class="kode">#{{ $pengadaan->nomor_pengadaan }}</span></td>
                            <td>{{ $pengadaan->waktu }}</td>
                            <td>
                                <span class="badge {{ $pengadaan->keadaan === 'S' ? 'badge-ok' : 'badge-warn' }}">
                                    {{ $pengadaan->keadaan === 'P' ? 'Diproses' : $pengadaan->keadaan }}
                                </span>
                            </td>
                            <td class="uang">Rp {{ number_format($pengadaan->sub_total, 0, ',', '.') }}</td>
                            <td class="uang">Rp {{ number_format($pengadaan->pajak, 0, ',', '.') }}</td>
                            <td class="uang">Rp {{ number_format($pengadaan->total_akhir, 0, ',', '.') }}</td>
                            <td class="nama">{{ $pengadaan->vendor }}</td>
                            <td>
                                <a href="{{ route('pengadaan.show', $pengadaan->nomor_pengadaan) }}" class="btn btn-light btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="kosong">Belum ada data pengadaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
