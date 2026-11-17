@extends('layouts.app')

@section('title', 'Margin Penjualan')
@section('judul', 'Margin Penjualan')
@section('tag', 'Harga')
@section('deskripsi', 'Riwayat persentase margin yang digunakan untuk menentukan harga jual.')

@section('aksi')
    @if (auth()->user()->idrole === 1)
        <a href="{{ route('margin.create') }}" class="btn btn-primary">Tambah Margin</a>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th class="kanan">Persentase</th>
                        <th>Status</th>
                        <th>ID Pengguna</th>
                        <th>Nama Pengguna</th>
                        <th>Dibuat</th>
                        <th>Diperbarui</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $margin)
                        <tr>
                            <td><span class="kode">#{{ $margin->nomor_margin }}</span></td>
                            <td class="kanan">{{ number_format($margin->persentase, 2, ',', '.') }}%</td>
                            <td>
                                <span class="badge {{ $margin->status_margin === 'Aktif' ? 'badge-ok' : 'badge-off' }}">
                                    {{ $margin->status_margin }}
                                </span>
                            </td>
                            <td>{{ $margin->nomor_pengguna }}</td>
                            <td class="nama">{{ $margin->nama }}</td>
                            <td>{{ $margin->created_at }}</td>
                            <td>{{ $margin->updated_at }}</td>
                            <td>
                                @include('layouts.aksi', [
                                    'detail' => route('margin.show', $margin->nomor_margin),
                                    'edit' => route('margin.edit', $margin->nomor_margin),
                                    'hapus' => route('margin.destroy', $margin->nomor_margin),
                                    'nama' => 'margin #' . $margin->nomor_margin,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="kosong">Belum ada data margin penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
