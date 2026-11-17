@extends('layouts.app')

@section('title', 'Dashboard')
@section('judul', 'Dashboard')
@section('tag', 'Operasional')
@section('deskripsi', 'Pintasan ke modul inventaris dan transaksi sesuai akses Anda.')

@section('content')
    <h2 class="section-title">Data Master</h2>
    <div class="menu-grid">
        <a href="/barang" class="menu-card">
            <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg></div>
            <div class="menu-label">Inventaris</div>
            <div class="menu-title">Barang</div>
            <div class="menu-desc">Instrumen, stok, dan harga jual</div>
        </a>
        @if (in_array(auth()->user()->idrole, [1, 3], true))
            <a href="/vendor" class="menu-card alt2">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg></div>
                <div class="menu-label">Mitra</div>
                <div class="menu-title">Vendor</div>
                <div class="menu-desc">Pemasok dan status kerja sama</div>
            </a>
        @endif
        @if (in_array(auth()->user()->idrole, [1, 2], true))
            <a href="/margin" class="menu-card alt">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M19 5L5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg></div>
                <div class="menu-label">Harga</div>
                <div class="menu-title">Margin Penjualan</div>
                <div class="menu-desc">Persentase margin yang berlaku</div>
            </a>
        @endif
    </div>

    <h2 class="section-title dashboard-section-title">Transaksi</h2>
    <div class="menu-grid">
        @if (in_array(auth()->user()->idrole, [1, 3], true))
            <a href="/pengadaan" class="menu-card">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                <div class="menu-label">Gudang</div>
                <div class="menu-title">Daftar Pengadaan</div>
                <div class="menu-desc">Pemesanan barang ke vendor</div>
            </a>
            <a href="/penerimaan" class="menu-card alt2">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.5-6.9A2 2 0 0016.8 4H7.2a2 2 0 00-1.7 1.1z"/></svg></div>
                <div class="menu-label">Gudang</div>
                <div class="menu-title">Daftar Penerimaan</div>
                <div class="menu-desc">Validasi barang yang datang</div>
            </a>
        @endif
        @if (in_array(auth()->user()->idrole, [1, 2], true))
            <a href="/penjualan" class="menu-card alt">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/></svg></div>
                <div class="menu-label">Kasir</div>
                <div class="menu-title">Penjualan</div>
                <div class="menu-desc">Transaksi penjualan ke pelanggan</div>
            </a>
        @endif
    </div>

    @if (auth()->user()->idrole === 1)
        <h2 class="section-title dashboard-section-title">Otoritas</h2>
        <div class="menu-grid">
            <a href="/user" class="menu-card">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                <div class="menu-label">Akun</div>
                <div class="menu-title">Pengguna</div>
                <div class="menu-desc">Daftar akun dan perannya</div>
            </a>
            <a href="/role" class="menu-card alt2">
                <div class="menu-icon"><svg class="ico" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <div class="menu-label">Hak akses</div>
                <div class="menu-title">Role</div>
                <div class="menu-desc">Jabatan dan anggotanya</div>
            </a>
        </div>
    @endif
@endsection
