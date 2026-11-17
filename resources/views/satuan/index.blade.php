@extends('layouts.app')

@section('title', 'Satuan')
@section('judul', 'Daftar Satuan')
@section('tag', 'Inventaris')
@section('deskripsi', 'Daftar satuan aktif yang digunakan untuk barang.')

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Satuan</th>
                        <th>Nama Satuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $satuan)
                        <tr>
                            <td><span class="kode">#{{ $satuan->nomor_satuan }}</span></td>
                            <td class="nama">{{ $satuan->satuan }}</td>
                            <td>
                                <span class="badge {{ $satuan->keadaan === 'aktif' ? 'badge-ok' : 'badge-off' }}">
                                    {{ $satuan->keadaan === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                @include('layouts.aksi', [
                                    'detail' => route('satuan.show', $satuan->nomor_satuan),
                                    'edit' => route('satuan.edit', $satuan->nomor_satuan),
                                    'hapus' => route('satuan.destroy', $satuan->nomor_satuan),
                                    'nama' => 'satuan ' . $satuan->satuan,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="kosong">Belum ada satuan aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
