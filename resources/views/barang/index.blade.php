@extends('layouts.app')

@section('title', 'Barang')
@section('judul', 'Daftar Barang')
@section('tag', 'Inventaris')
@section('deskripsi', 'Lihat status, stok, dan harga produk.')

@section('aksi')
    @if (auth()->user()->idrole === 1)
        <a href="/barang/tambah" class="btn btn-primary">Tambah Barang</a>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="kanan">Harga Dasar</th>
                        <th class="kanan">Harga Jual</th>
                        <th class="kanan">Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $b)
                        <tr>
                            <td><span class="kode">#{{ $b->nomor_barang }}</span></td>
                            <td class="nama">{{ $b->produk }}</td>
                            <td>{{ $b->kategori }}</td>
                            <td>{{ $b->satuan }}</td>
                            <td class="uang">Rp {{ number_format($b->harga_dasar, 0, ',', '.') }}</td>
                            <td class="uang">
                                {{ $b->harga_akhir === null ? '-' : 'Rp ' . number_format($b->harga_akhir, 0, ',', '.') }}
                            </td>
                            <td class="kanan">{{ $b->stok_terkini }}</td>
                            <td>
                                <span class="badge {{ $b->status == 1 ? 'badge-ok' : 'badge-off' }}">
                                    {{ $b->status == 1 ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                @include('layouts.aksi', [
                                    'detail' => route('barang.show', $b->nomor_barang),
                                    'edit' => route('barang.edit', $b->nomor_barang),
                                    'hapus' => route('barang.destroy', $b->nomor_barang),
                                    'nama' => $b->produk,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="kosong">Belum ada data barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
