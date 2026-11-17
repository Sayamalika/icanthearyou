@extends('layouts.app')

@section('title', 'Role')
@section('judul', 'Daftar Role')
@section('tag', 'Otoritas')
@section('deskripsi', 'Role yang mengatur akses dan tanggung jawab pengguna.')

@section('aksi')
    <a href="{{ route('role.create') }}" class="btn btn-primary">Tambah Role</a>
    <a href="{{ route('dashboard') }}" class="btn btn-light">&larr; Kembali ke Dashboard</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Nomor Role</th>
                        <th>Nama Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $r)
                        <tr>
                            <td><span class="kode">#{{ $r->nomor_peran }}</span></td>
                            <td class="nama">{{ $r->peran }}</td>
                            <td>
                                @include('layouts.aksi', [
                                    'detail' => route('role.show', $r->nomor_peran),
                                    'edit' => route('role.edit', $r->nomor_peran),
                                    'hapus' => route('role.destroy', $r->nomor_peran),
                                    'nama' => 'role ' . $r->peran,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="kosong">Belum ada role.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
