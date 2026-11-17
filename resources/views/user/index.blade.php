@extends('layouts.app')

@section('title', 'Pengguna')
@section('judul', 'Daftar Pengguna')
@section('tag', 'Akun')
@section('deskripsi', 'Akun pengguna dan role yang dimiliki.')

@section('aksi')
    <a href="{{ route('register') }}" class="btn btn-primary">Tambah Pengguna</a>
    <a href="/" class="btn btn-light">&larr; Kembali ke Dashboard</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Nomor Pengguna</th>
                        <th>Nama Pengguna</th>
                        <th>Nomor Role</th>
                        <th>Nama Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $user)
                        <tr>
                            <td><span class="kode">#{{ $user->nomor_pengguna }}</span></td>
                            <td class="nama">{{ $user->nama }}</td>
                            <td>{{ $user->nomor_jabatan }}</td>
                            <td>{{ $user->peran }}</td>
                            <td>
                                @include('layouts.aksi', [
                                    'detail' => route('user.show', $user->nomor_pengguna),
                                    'edit' => route('user.edit', $user->nomor_pengguna),
                                    'hapus' => route('user.destroy', $user->nomor_pengguna),
                                    'nama' => 'pengguna ' . $user->nama,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="kosong">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
