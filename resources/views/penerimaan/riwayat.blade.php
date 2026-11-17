@extends('layouts.app')

@section('title', 'Riwayat Penerimaan')
@section('judul', 'Riwayat Penerimaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Catatan penerimaan barang berdasarkan transaksi.')

@section('aksi')
    <a href="{{ route('penerimaan.index') }}" class="btn btn-light">
        &larr; Kembali ke Penerimaan
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Penerimaan</th>
                        <th>ID Pengadaan</th>
                        <th>Waktu</th>
                        <th>Vendor</th>
                        <th>Pengguna</th>
                        <th class="tengah">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $baris)
                        <tr>
                            <td><span class="kode">#{{ $baris->nomor_penerimaan }}</span></td>
                            <td><span class="kode">#{{ $baris->nomor_pengadaan }}</span></td>
                            <td>{{ $baris->waktu_penerimaan }}</td>
                            <td class="nama">{{ $baris->vendor }}</td>
                            <td>{{ $baris->pengguna }}</td>
                            <td class="tengah">
                                <a
                                    href="{{ route('penerimaan.riwayat.detail', $baris->nomor_penerimaan) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    Lihat detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="kosong">Belum ada riwayat penerimaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
