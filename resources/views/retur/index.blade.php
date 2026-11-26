
@extends('layouts.app')

@section('title', 'Retur Pengadaan')
@section('judul', 'Riwayat Retur Pengadaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Riwayat pengembalian barang kepada vendor.')

@section('aksi')
    <a href="{{ route('retur.create') }}" class="btn btn-primary">
        + Buat Retur
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Retur</th>
                        <th>Waktu</th>
                        <th>ID Penerimaan</th>
                        <th>ID Pengguna</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $retur)
                        <tr>
                            <td>
                                <span class="kode">
                                    #{{ $retur->nomor_pengembalian }}
                                </span>
                            </td>
                            <td>{{ $retur->waktu }}</td>
                            <td>
                                <span class="kode">
                                    #{{ $retur->dari_penerimaan }}
                                </span>
                            </td>
                            <td>
                                <span class="kode">
                                    #{{ $retur->nomor_pengguna }}
                                </span>
                            </td>
                            <td>
                                <a
                                    href="{{ route('retur.show', $retur->nomor_pengembalian) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="kosong">
                                Belum ada riwayat retur pengadaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
