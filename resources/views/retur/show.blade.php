
@extends('layouts.app')

@section('title', 'Detail Retur')
@section('judul', 'Detail Retur Pengadaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Rincian barang pada transaksi pengembalian kepada vendor.')

@section('aksi')
    <a href="{{ route('retur.index') }}" class="btn btn-light">
        &larr; Kembali
    </a>
@endsection

@section('content')
    <div class="detail-grid">
        <dl class="detail-primary">
            <dt>ID Retur</dt>
            <dd>#{{ $retur->nomor_pengembalian }}</dd>
        </dl>

        <div class="detail-stack">
            <dl>
                <dt>Waktu Retur</dt>
                <dd>{{ $retur->waktu }}</dd>
            </dl>
            <dl>
                <dt>ID Penerimaan</dt>
                <dd>#{{ $retur->dari_penerimaan }}</dd>
            </dl>
            <dl>
                <dt>ID Pengguna</dt>
                <dd>#{{ $retur->nomor_pengguna }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Detail Retur</th>
                        <th>ID Barang</th>
                        <th>ID Detail Penerimaan</th>
                        <th>Jumlah</th>
                        <th>Alasan Retur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>
                                <span class="kode">
                                    #{{ $item->nomor_detil_retur }}
                                </span>
                            </td>
                            <td>
                                <span class="kode">
                                    #{{ $item->nomor_barang }}
                                </span>
                            </td>
                            <td>
                                <span class="kode">
                                    #{{ $item->dari_penerimaan }}
                                </span>
                            </td>
                            <td>{{ $item->jumlah_barang }}</td>
                            <td>{{ $item->penyebab_retur }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="kosong">
                                Tidak ada detail barang pada retur ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
