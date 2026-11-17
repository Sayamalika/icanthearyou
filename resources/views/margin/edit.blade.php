@extends('layouts.app')

@section('title', 'Ubah Margin')
@section('judul', 'Ubah Margin Penjualan')
@section('tag', 'Harga')
@section('deskripsi', 'Perbarui persentase dan status margin penjualan.')

@section('aksi')
    <a href="{{ route('margin.index') }}" class="btn btn-light">&larr; Kembali ke Margin</a>
@endsection

@section('content')
    <div class="card form-card">
        <form method="POST" action="{{ route('margin.update', $margin->idmargin_penjualan) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="persen">Persentase Margin</label>
                <input
                    id="persen"
                    name="persen"
                    type="number"
                    min="0"
                    max="49.99"
                    step="0.01"
                    value="{{ old('persen', $margin->persen) }}"
                    @disabled($terkunci)
                    required
                >
                @if ($terkunci)
                    <p class="field-note">Persentase dikunci karena margin ini sudah dipakai pada riwayat penjualan.</p>
                @endif
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="1" @selected((string) old('status', $margin->status) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('status', $margin->status) === '0')>Nonaktif</option>
                </select>
                <p class="field-note">Mengaktifkan margin ini akan menonaktifkan margin aktif lainnya. Minimal harus ada satu margin aktif.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('margin.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
