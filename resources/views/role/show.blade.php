@extends('layouts.app')

@section('title', 'Detail Role')
@section('judul', 'Detail Role')
@section('tag', 'Otoritas')
@section('deskripsi', 'Informasi role dan daftar pengguna yang memilikinya.')

@section('aksi')
    <a href="/role" class="btn btn-light">&larr; Kembali ke Role</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="tabel">
                <tbody>
                    <tr><th>Nomor Role</th><td><span class="kode">#{{ $r->nomor_peran }}</span></td></tr>
                    <tr><th>Nama Role</th><td class="nama">{{ $r->peran }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Pengguna dengan Role {{ $r->peran }}</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr><th>Nama Pengguna</th></tr>
                </thead>
                <tbody>
                    @forelse ($detilrole as $detail)
                        <tr><td class="nama">{{ $detail->nama_pengguna }}</td></tr>
                    @empty
                        <tr><td class="kosong">Belum ada pengguna dengan role ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
