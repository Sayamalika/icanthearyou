@extends('layouts.app')

@section('title', 'Detail Margin Penjualan')
@section('judul', 'Detail Margin Penjualan')
@section('tag', 'Harga')
@section('deskripsi', 'Rincian margin dan pengguna yang mencatatnya.')

@section('aksi')
    <a href="/margin" class="btn btn-light">&larr; Kembali ke Margin</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <tbody>
                    <tr><th>ID Margin</th><td><span class="kode">#{{ $margin->nomor_margin }}</span></td></tr>
                    <tr><th>Persentase</th><td>{{ number_format($margin->persentase, 2, ',', '.') }}%</td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ $margin->status_margin === 'Aktif' ? 'badge-ok' : 'badge-off' }}">
                                {{ $margin->status_margin }}
                            </span>
                        </td>
                    </tr>
                    <tr><th>ID Pengguna</th><td>{{ $margin->nomor_pengguna }}</td></tr>
                    <tr><th>Nama Pengguna</th><td>{{ $margin->nama }}</td></tr>
                    <tr><th>Dibuat</th><td>{{ $margin->created_at }}</td></tr>
                    <tr><th>Diperbarui</th><td>{{ $margin->updated_at }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
