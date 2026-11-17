@extends('layouts.app')

@section('title', 'Buat Penjualan')
@section('judul', 'Buat Penjualan')
@section('tag', 'Kasir')
@section('deskripsi', 'Pilih barang yang dijual dan tentukan jumlahnya.')

@section('aksi')
    <a href="/penjualan" class="btn btn-light">&larr; Kembali ke Penjualan</a>
@endsection

@section('content')
    @php
        $formItems = old('items', [
            ['idbarang' => '', 'jumlah' => ''],
        ]);

        if (! is_array($formItems)) {
            $formItems = [];
        }

        $formItems = array_filter($formItems, 'is_array');

        if ($formItems === []) {
            $formItems = [
                ['idbarang' => '', 'jumlah' => ''],
            ];
        }

        $nextItemIndex = max(array_map('intval', array_keys($formItems))) + 1;
    @endphp

    @if (count($barang) === 0)
        <div class="alert alert-error">Tidak ada barang aktif dengan stok dan harga jual tersedia.</div>
    @else
        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">Barang yang Dijual</h2>
                </div>
                <div class="card-body">
                    <div id="items-container" data-next-index="{{ $nextItemIndex }}">
                        @foreach ($formItems as $index => $item)
                            <div class="item-row">
                                <div class="field">
                                    <label for="barang-{{ $index }}">Barang</label>
                                    <select
                                        name="items[{{ $index }}][idbarang]"
                                        id="barang-{{ $index }}"
                                        class="product-select"
                                        required
                                    >
                                        <option value="">-- Pilih barang --</option>
                                        @foreach ($barang as $b)
                                            <option
                                                value="{{ $b->nomor_barang }}"
                                                data-stok="{{ $b->stok_terkini }}"
                                                @selected((string) ($item['idbarang'] ?? '') === (string) $b->nomor_barang)
                                            >
                                                {{ $b->produk }} — {{ $b->kategori }} — Rp {{ number_format($b->harga_akhir, 0, ',', '.') }} — Stok {{ $b->stok_terkini }}
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
                                        class="quantity-input"
                                        min="1"
                                        value="{{ $item['jumlah'] ?? '' }}"
                                        required
                                    >
                                    <span class="stock-info"></span>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-actions">
                        <button type="button" id="add-item" class="btn btn-ghost">Tambah Barang</button>
                        <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                    </div>
                </div>
            </div>
        </form>

        <template id="item-row-template">
            <div class="item-row">
                <div class="field">
                    <label for="barang-__INDEX__">Barang</label>
                    <select
                        name="items[__INDEX__][idbarang]"
                        id="barang-__INDEX__"
                        class="product-select"
                        required
                    >
                        <option value="">-- Pilih barang --</option>
                        @foreach ($barang as $b)
                            <option value="{{ $b->nomor_barang }}" data-stok="{{ $b->stok_terkini }}">
                                {{ $b->produk }} — {{ $b->kategori }} — Rp {{ number_format($b->harga_akhir, 0, ',', '.') }} — Stok {{ $b->stok_terkini }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="jumlah-__INDEX__">Jumlah</label>
                    <input
                        type="number"
                        name="items[__INDEX__][jumlah]"
                        id="jumlah-__INDEX__"
                        class="quantity-input"
                        min="1"
                        required
                    >
                    <span class="stock-info"></span>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
            </div>
        </template>

        <script>
            const itemsContainer = document.getElementById('items-container');
            const itemTemplate = document.getElementById('item-row-template');

            function updateStockInfo(row) {
                const select = row.querySelector('.product-select');
                const quantity = row.querySelector('.quantity-input');
                const stockInfo = row.querySelector('.stock-info');
                const stock = select.options[select.selectedIndex].dataset.stok;

                if (stock) {
                    quantity.max = stock;
                    stockInfo.textContent = `Stok tersedia: ${stock}`;
                } else {
                    quantity.removeAttribute('max');
                    stockInfo.textContent = '';
                }
            }

            itemsContainer.querySelectorAll('.item-row').forEach(updateStockInfo);

            itemsContainer.addEventListener('change', function (event) {
                if (event.target.classList.contains('product-select')) {
                    updateStockInfo(event.target.closest('.item-row'));
                }
            });

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
                const row = event.target.closest('.item-row');

                if (rows.length > 1) {
                    row.remove();
                } else {
                    row.querySelector('.product-select').value = '';
                    row.querySelector('.quantity-input').value = '';
                    updateStockInfo(row);
                }
            });
        </script>
    @endif
@endsection
