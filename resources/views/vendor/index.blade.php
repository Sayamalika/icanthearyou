@extends('layouts.app')

@section('title', 'Vendor')
@section('judul', 'Daftar Vendor')
@section('tag', 'Mitra')
@section('deskripsi', 'Daftar pemasok dan status kerja samanya.')

@section('aksi')
    @if (auth()->user()->idrole === 1)
        <a href="{{ route('vendor.create') }}" class="btn btn-primary">Tambah Vendor</a>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Vendor</th>
                        <th>Badan Hukum</th>
                        <th>Status Kerja Sama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $v)
                        <tr>
                            <td><span class="kode">#{{ $v->nomor_vendor }}</span></td>
                            <td class="nama">{{ $v->penjual }}</td>
                            <td>{{ $v->badan_hukum }}</td>
                            <td>
                                <span class="badge {{ $v->status === 'Aktif' ? 'badge-ok' : 'badge-off' }}">
                                    {{ $v->status }}
                                </span>
                            </td>
                            <td>
                                @include('layouts.aksi', [
                                    'edit' => route('vendor.edit', $v->nomor_vendor),
                                    'hapus' => route('vendor.destroy', $v->nomor_vendor),
                                    'nama' => 'vendor ' . $v->penjual,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="kosong">Belum ada data vendor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
