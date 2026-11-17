@extends('layouts.app')

@section('title', 'Penerimaan')
@section('judul', 'Penerimaan Barang')
@section('tag', 'Gudang')
@section('deskripsi', 'Daftar pengadaan yang masih memiliki barang untuk diterima.')

@section('aksi')
    <a href="/penerimaan/riwayat" class="btn btn-primary">Riwayat Penerimaan</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Pengadaan</th>
                        <th>Waktu</th>
                        <th>Vendor</th>
                        <th class="kanan">Sisa Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $pengadaan)
                        <tr>
                            <td><span class="kode">#{{ $pengadaan->nomor_pengadaan }}</span></td>
                            <td>{{ $pengadaan->waktu }}</td>
                            <td class="nama">{{ $pengadaan->vendor }}</td>
                            <td class="kanan"><span class="badge badge-warn">{{ $pengadaan->jumlah_sisa }}</span></td>
                            <td>
                                <a href="/penerimaan/{{ $pengadaan->nomor_pengadaan }}/create" class="btn btn-light btn-sm">
                                    Buat Penerimaan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="kosong">Belum ada penerimaan yang perlu diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
