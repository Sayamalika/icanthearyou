
@extends('layouts.app')

@section('title', 'Buat Retur')
@section('judul', 'Buat Retur Barang')
@section('tag', 'Gudang')
@section('deskripsi', 'Pilih penerimaan, barang, jumlah, dan alasan retur.')

@section('aksi')
    <a href="{{ route('retur.index') }}" class="btn btn-light">
        &larr; Kembali
    </a>
@endsection

@section('content')
    @php
        $formItems = old('items', [
            [
                'iddetail_penerimaan' => '',
                'jumlah' => '',
                'alasan' => '',
            ],
        ]);

        if (! is_array($formItems)) {
            $formItems = [];
        }

        $formItems = array_filter($formItems, 'is_array');

        if ($formItems === []) {
            $formItems = [
                [
                    'iddetail_penerimaan' => '',
                    'jumlah' => '',
                    'alasan' => '',
                ],
            ];
        }

        $nextItemIndex = max(array_map('intval', array_keys($formItems))) + 1;
    @endphp

    <div class="card">
        <div class="card-head">
            <h2 class="card-title">Pilih Penerimaan</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('retur.create') }}" method="GET">
                <div class="field">
                    <label for="idpenerimaan">Nomor Penerimaan</label>

                    <select name="idpenerimaan" id="idpenerimaan" required>
                        <option value="">-- Pilih penerimaan --</option>

                        @foreach ($penerimaanList as $p)
                            <option
                                value="{{ $p->nomor_penerimaan }}"
                                @selected((string) $idpenerimaan === (string) $p->nomor_penerimaan)
                            >
                                #{{ $p->nomor_penerimaan }}
                                — {{ $p->vendor }}
                                — {{ $p->waktu_penerimaan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Tampilkan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($penerimaan)
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">Informasi Penerimaan</h2>
            </div>

            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-primary">
                        <span class="detail-label">Penerimaan</span>
                        <span class="detail-value">
                            #{{ $penerimaan->nomor_penerimaan }}
                        </span>
                    </div>

                    <div class="detail-stack">
                        <div>
                            <span class="detail-label">Pengadaan</span>
                            <span class="detail-value">
                                #{{ $penerimaan->nomor_pengadaan }}
                            </span>
                        </div>

                        <div>
                            <span class="detail-label">Vendor</span>
                            <span class="detail-value">
                                {{ $penerimaan->vendor }}
                            </span>
                        </div>

                        <div>
                            <span class="detail-label">Waktu Penerimaan</span>
                            <span class="detail-value">
                                {{ $penerimaan->waktu_penerimaan }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (count($items) === 0)
            <div class="alert alert-error">
                Tidak ada barang pada penerimaan ini.
            </div>
        @else
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('retur.store') }}" method="POST">
                @csrf

                <input
                    type="hidden"
                    name="idpenerimaan"
                    value="{{ $penerimaan->nomor_penerimaan }}"
                >

                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Barang yang Diretur</h2>
                    </div>

                    <div class="card-body">
                        <div
                            id="items-container"
                            data-next-index="{{ $nextItemIndex }}"
                        >
                            @foreach ($formItems as $index => $item)
                                <div class="item-row">
                                    <div class="field">
                                        <label for="barang-{{ $index }}">
                                            Barang
                                        </label>

                                        <select
                                            name="items[{{ $index }}][iddetail_penerimaan]"
                                            id="barang-{{ $index }}"
                                            class="product-select"
                                            required
                                        >
                                            <option value="">
                                                -- Pilih barang --
                                            </option>

                                            @foreach ($items as $b)
                                                <option
                                                    value="{{ $b->nomor_detail_penerimaan }}"
                                                    data-jumlah-terima="{{ $b->jumlah_terima }}"
                                                    @selected((string) ($item['iddetail_penerimaan'] ?? '') === (string) $b->nomor_detail_penerimaan)
                                                >
                                                    {{ $b->nama_barang }}
                                                    — Diterima {{ $b->jumlah_terima }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label for="jumlah-{{ $index }}">
                                            Jumlah Retur
                                        </label>

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

                                    <div class="field">
                                        <label for="alasan-{{ $index }}">
                                            Alasan Retur
                                        </label>

                                        <input
                                            type="text"
                                            name="items[{{ $index }}][alasan]"
                                            id="alasan-{{ $index }}"
                                            maxlength="200"
                                            value="{{ $item['alasan'] ?? '' }}"
                                            placeholder="Masukkan alasan retur"
                                            required
                                        >
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm remove-item"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-actions">
                            <button
                                type="button"
                                id="add-item"
                                class="btn btn-ghost"
                            >
                                Tambah Barang
                            </button>

                            <a href="{{ route('retur.index') }}" class="btn btn-light">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Simpan Retur
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <template id="item-row-template">
                <div class="item-row">
                    <div class="field">
                        <label for="barang-__INDEX__">Barang</label>

                        <select
                            name="items[__INDEX__][iddetail_penerimaan]"
                            id="barang-__INDEX__"
                            class="product-select"
                            required
                        >
                            <option value="">-- Pilih barang --</option>

                            @foreach ($items as $b)
                                <option
                                    value="{{ $b->nomor_detail_penerimaan }}"
                                    data-jumlah-terima="{{ $b->jumlah_terima }}"
                                >
                                    {{ $b->nama_barang }}
                                    — Diterima {{ $b->jumlah_terima }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="jumlah-__INDEX__">Jumlah Retur</label>

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

                    <div class="field">
                        <label for="alasan-__INDEX__">Alasan Retur</label>

                        <input
                            type="text"
                            name="items[__INDEX__][alasan]"
                            id="alasan-__INDEX__"
                            maxlength="200"
                            placeholder="Masukkan alasan retur"
                            required
                        >
                    </div>

            <button type="button" class="btn btn-danger btn-sm remove-item">
                Hapus
            </button>
        </div>
    </template>

    <script>
        const itemsContainer = document.getElementById('items-container');
        const itemTemplate = document.getElementById('item-row-template');

        function updateStockInfo(row) {
            const select = row.querySelector('.product-select');
            const quantity = row.querySelector('.quantity-input');
            const stockInfo = row.querySelector('.stock-info');

            const option = select.options[select.selectedIndex];
            const jumlahTerima = option.dataset.jumlahTerima;

            if (jumlahTerima !== undefined && jumlahTerima !== '') {
                quantity.max = jumlahTerima;
                stockInfo.textContent = `Jumlah diterima: ${jumlahTerima}`;
            } else {
                quantity.removeAttribute('max');
                stockInfo.textContent = '';
            }
        }

        // Memperbarui informasi pada baris yang sudah ada
        itemsContainer.querySelectorAll('.item-row').forEach(updateStockInfo);

        // Memperbarui informasi ketika barang dipilih
        itemsContainer.addEventListener('change', function (event) {
            if (event.target.classList.contains('product-select')) {
                updateStockInfo(event.target.closest('.item-row'));
            }
        });

        // Menambahkan baris barang
        document.getElementById('add-item').addEventListener('click', function () {
            const index = Number(itemsContainer.dataset.nextIndex);
            const html = itemTemplate.innerHTML.replace(/__INDEX__/g, String(index));

            itemsContainer.insertAdjacentHTML('beforeend', html);
            itemsContainer.dataset.nextIndex = String(index + 1);
        });

        // Menghapus baris barang
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
                row.querySelector('input[type="text"]').value = '';
                updateStockInfo(row);
            }
        });
    </script>
        @endif
    @endif
@endsection
