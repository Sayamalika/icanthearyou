@extends('layouts.app')

@section('title', 'Ubah Vendor')
@section('judul', 'Ubah Vendor')
@section('tag', 'Mitra')
@section('deskripsi', 'Perbarui informasi dan status kerja sama vendor.')

@section('aksi')
    <a href="{{ route('vendor.index') }}" class="btn btn-light">&larr; Kembali ke Vendor</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('vendor.update', $vendor->nomor_vendor) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_vendor">Nama Vendor</label>
                <input
                    id="nama_vendor"
                    name="nama_vendor"
                    type="text"
                    maxlength="100"
                    value="{{ old('nama_vendor', $vendor->penjual) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="badan_hukum">Bentuk Badan Hukum</label>
                <select id="badan_hukum" name="badan_hukum" required>
                    @foreach (\App\Support\Referensi::BADAN_HUKUM as $kode => $label)
                        <option value="{{ $kode }}" @selected((string) old('badan_hukum', $vendor->badan_hukum) === (string) $kode)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status Kerja Sama</label>
                <select id="status" name="status" required>
                    <option value="A" @selected(old('status', $vendor->status) === 'A')>Aktif</option>
                    <option value="N" @selected(old('status', $vendor->status) === 'N')>Tidak Aktif</option>
                </select>
                <p class="field-note">Vendor tidak aktif tidak muncul pada pilihan pengadaan baru; riwayat lama tetap tersimpan.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('vendor.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
