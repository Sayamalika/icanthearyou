@extends('layouts.app')

@section('title', 'Detail Satuan')
@section('judul', 'Detail Satuan')
@section('tag', 'Inventaris')
@section('deskripsi', 'Informasi satuan dan barang yang menggunakannya.')

@section('aksi')
    <a href="{{ route('satuan.index') }}" class="btn btn-light">&larr; Kembali ke Satuan</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <tbody>
                    <tr><th>ID Satuan</th><td><span class="kode">#{{ $satuan->nomor_satuan }}</span></td></tr>
                    <tr><th>Nama Satuan</th><td class="nama">{{ $satuan->satuan }}</td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ $satuan->status == 1 ? 'badge-ok' : 'badge-off' }}">
                                {{ $satuan->status == 1 ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Barang dengan Satuan {{ $satuan->satuan }}</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th class="kanan">Harga Dasar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barang as $item)
                        <tr>
                            <td><span class="kode">#{{ $item->nomor_barang }}</span></td>
                            <td class="nama">{{ $item->nama_barang }}</td>
                            <td class="uang">Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $item->status == 1 ? 'badge-ok' : 'badge-off' }}">
                                    {{ $item->status == 1 ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="kosong">Belum ada barang yang menggunakan satuan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
