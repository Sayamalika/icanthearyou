@extends('layouts.app')

@section('title', 'Detail Pengguna')
@section('judul', 'Detail Pengguna: ' . $u->nama)
@section('tag', 'Akun')
@section('deskripsi', 'Role yang dimiliki pengguna ini.')

@section('aksi')
    <a href="/user" class="btn btn-light">&larr; Kembali ke Pengguna</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Role yang Dimiliki</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr><th>Nama Role</th></tr>
                </thead>
                <tbody>
                    @forelse ($detiluser as $detail)
                        <tr><td class="nama">{{ $detail->peran }}</td></tr>
                    @empty
                        <tr><td class="kosong">Pengguna ini belum memiliki role.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
