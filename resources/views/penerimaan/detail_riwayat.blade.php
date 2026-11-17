@extends('layouts.app')

@section('title', 'Detail Riwayat Penerimaan')
@section('judul', 'Detail Riwayat Penerimaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Rincian barang yang diterima dalam transaksi ini.')

@section('aksi')
    <a href="{{ url('/penerimaan/riwayat') }}" class="btn btn-light">
        &larr; Kembali ke Riwayat
    </a>
@endsection

@section('content')
    @php
        $penerimaan = $detail[0];
    @endphp

    <div class="detail-grid">
        <dl class="detail-primary">
            <dt>ID Pengadaan</dt>
            <dd>#{{ $penerimaan->nomor_pengadaan }}</dd>
        </dl>

        <div class="detail-stack">
            <dl>
                <dt>ID Penerimaan</dt>
                <dd>#{{ $penerimaan->nomor_penerimaan }}</dd>
            </dl>
            <dl>
                <dt>Waktu</dt>
                <dd>{{ $penerimaan->waktu_penerimaan }}</dd>
            </dl>
            <dl>
                <dt>Vendor</dt>
                <dd>{{ $penerimaan->vendor }}</dd>
            </dl>
            <dl>
                <dt>Pengguna</dt>
                <dd>{{ $penerimaan->pengguna }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="kanan">Jumlah Diterima</th>
                        <th class="kanan">Harga Satuan</th>
                        <th class="kanan">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detail as $baris)
                        <tr>
                            <td class="nama">{{ $baris->nama_barang }}</td>
                            <td class="kanan">{{ $baris->jumlah_terima }}</td>
                            <td class="uang">
                                Rp {{ number_format($baris->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="uang">
                                Rp {{ number_format($baris->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
