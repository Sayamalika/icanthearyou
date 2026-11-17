@extends('layouts.app')

@section('title', 'Buat Pengadaan')
@section('judul', 'Buat Pengadaan')
@section('tag', 'Gudang')
@section('deskripsi', 'Pilih vendor, lalu catat barang dan jumlah yang dipesan.')

@section('aksi')
    <a href="/pengadaan" class="btn btn-light">&larr; Kembali ke Pengadaan</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pengadaan.create') }}" method="GET" class="inline-form">
                <div class="form-group">
                    <label for="vendor_idvendor">Vendor</label>
                    <select name="vendor_idvendor" id="vendor_idvendor" required>
                        <option value="">-- Pilih vendor --</option>
                        @foreach ($vendor as $v)
                            <option
                                value="{{ $v->nomor_vendor }}"
                                @selected((string) old('vendor_idvendor', $vendorTerpilih) === (string) $v->nomor_vendor)
                            >
                                {{ $v->penjual }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Pilih Vendor</button>
            </form>
        </div>
    </div>

    @if ($vendorTerpilih === null)
        <div class="alert alert-ok">Pilih vendor terlebih dahulu untuk melihat barang yang tersedia.</div>
    @elseif (count($barang) === 0)
        <div class="alert alert-error">Vendor ini belum memiliki barang aktif untuk ditampilkan.</div>
    @else
        @php
            $formItems = old('items', [
                ['idbarang' => '', 'jumlah' => ''],
            ]);

            if ($formItems === []) {
                $formItems = [
                    ['idbarang' => '', 'jumlah' => ''],
                ];
            }

            $nextItemIndex = max(array_map('intval', array_keys($formItems))) + 1;
        @endphp

        <form action="{{ route('pengadaan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="vendor_idvendor" value="{{ $vendorTerpilih }}">

            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">Barang Pengadaan</h2>
                </div>
                <div class="card-body">
                    <div id="items-container" data-next-index="{{ $nextItemIndex }}">
                        @foreach ($formItems as $index => $baris)
                            <div class="item-row">
                                <div class="field">
                                    <label for="barang-{{ $index }}">Produk</label>
                                    <select name="items[{{ $index }}][idbarang]" id="barang-{{ $index }}" required>
                                        <option value="">-- Pilih barang --</option>
                                        @foreach ($barang as $b)
                                            <option
                                                value="{{ $b->nomor_barang }}"
                                                @selected((string) ($baris['idbarang'] ?? '') === (string) $b->nomor_barang)
                                            >
                                                {{ $b->produk }} — {{ $b->kategori }} — Rp {{ number_format($b->harga_dasar, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="jumlah-{{ $index }}">Jumlah</label>
                                    <input
                                        type="number"
                                        name="items[{{ $index }}][jumlah]"
                                        id="jumlah-{{ $index }}"
                                        min="1"
                                        value="{{ $baris['jumlah'] ?? '' }}"
                                        required
                                    >
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-actions">
                        <button type="button" id="add-item" class="btn btn-ghost">Tambah Barang</button>
                        <button type="submit" class="btn btn-primary">Simpan Pengadaan</button>
                    </div>
                </div>
            </div>
        </form>

        <template id="item-row-template">
            <div class="item-row">
                <div class="field">
                    <label for="barang-__INDEX__">Produk</label>
                    <select name="items[__INDEX__][idbarang]" id="barang-__INDEX__" required>
                        <option value="">-- Pilih barang --</option>
                        @foreach ($barang as $b)
                            <option value="{{ $b->nomor_barang }}">
                                {{ $b->produk }} — {{ $b->kategori }} — Rp {{ number_format($b->harga_dasar, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="jumlah-__INDEX__">Jumlah</label>
                    <input type="number" name="items[__INDEX__][jumlah]" id="jumlah-__INDEX__" min="1" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
            </div>
        </template>

        <script>
            const itemsContainer = document.getElementById('items-container');
            const itemTemplate = document.getElementById('item-row-template');

            document.getElementById('add-item').addEventListener('click', function () {
                const index = Number(itemsContainer.dataset.nextIndex);
                const html = itemTemplate.innerHTML.replace(/__INDEX__/g, String(index));

                itemsContainer.insertAdjacentHTML('beforeend', html);
                itemsContainer.dataset.nextIndex = String(index + 1);
            });

            itemsContainer.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-item')) {
                    return;
                }

                const rows = itemsContainer.querySelectorAll('.item-row');

                if (rows.length > 1) {
                    event.target.closest('.item-row').remove();
                }
            });
        </script>
    @endif
@endsection
