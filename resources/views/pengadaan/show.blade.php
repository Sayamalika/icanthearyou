@extends('layouts.app')

@section('title', 'Detail Pengadaan')
@section('judul', 'Pengadaan #' . $pengadaan->nomor_pengadaan)
@section('tag', 'Gudang')
@section('deskripsi', 'Rincian barang yang dipesan, jumlah yang sudah diterima, dan riwayat penerimaannya.')

@section('aksi')
    <a href="{{ route('pengadaan.index') }}" class="btn btn-light">&larr; Kembali ke Pengadaan</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Ringkasan Pengadaan</h2>
        </div>
        <div class="card-body">
            <dl class="record-grid">
                <div><dt>ID Pengadaan</dt><dd>#{{ $pengadaan->nomor_pengadaan }}</dd></div>
                <div><dt>Waktu</dt><dd>{{ $pengadaan->waktu }}</dd></div>
                <div><dt>Vendor</dt><dd>{{ $pengadaan->vendor }}</dd></div>
                <div>
                    <dt>Status</dt>
                    <dd>
                        <span class="badge {{ $pengadaan->keadaan === 'S' ? 'badge-ok' : 'badge-warn' }}">
                            {{ $pengadaan->keadaan === 'S' ? 'Selesai' : 'Diproses' }}
                        </span>
                    </dd>
                </div>
                <div><dt>Subtotal</dt><dd>Rp {{ number_format($pengadaan->sub_total, 0, ',', '.') }}</dd></div>
                <div><dt>PPN</dt><dd>Rp {{ number_format($pengadaan->pajak, 0, ',', '.') }}</dd></div>
                <div><dt>Total</dt><dd>Rp {{ number_format($pengadaan->total_akhir, 0, ',', '.') }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Barang yang Dipesan</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="kanan">Harga Satuan</th>
                        <th class="kanan">Dipesan</th>
                        <th class="kanan">Diterima</th>
                        <th class="kanan">Sisa</th>
                        <th class="kanan">Subtotal Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="nama">{{ $item->nama_barang }}</td>
                            <td class="uang">Rp {{ number_format($item->harga_tiap_barang, 0, ',', '.') }}</td>
                            <td class="kanan">{{ $item->jumlah_pesanan }}</td>
                            <td class="kanan">{{ $item->jumlah_terima }}</td>
                            <td class="kanan">
                                <span class="badge {{ $item->sisa > 0 ? 'badge-warn' : 'badge-ok' }}">{{ $item->sisa }}</span>
                            </td>
                            <td class="uang">Rp {{ number_format($item->harga_tiap_barang * $item->jumlah_pesanan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="kosong">Pengadaan ini belum memiliki barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Riwayat Penerimaan</h2>
        </div>
        <div class="table-wrap">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>ID Penerimaan</th>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Barang</th>
                        <th class="kanan">Diterima</th>
                        <th class="kanan">Harga Satuan</th>
                        <th class="kanan">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penerimaan as $baris)
                        <tr>
                            <td><span class="kode">#{{ $baris->nomor_penerimaan }}</span></td>
                            <td>{{ $baris->waktu_penerimaan }}</td>
                            <td>{{ $baris->pengguna }}</td>
                            <td class="nama">{{ $baris->nama_barang }}</td>
                            <td class="kanan">{{ $baris->jumlah_terima }}</td>
                            <td class="uang">Rp {{ number_format($baris->harga_satuan, 0, ',', '.') }}</td>
                            <td class="uang">Rp {{ number_format($baris->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="kosong">Belum ada barang yang diterima untuk pengadaan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
