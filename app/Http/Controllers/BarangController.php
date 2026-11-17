<?php

namespace App\Http\Controllers;

use App\Support\Referensi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class BarangController extends Controller
{
    public function index()
    {
        $data = DB::select(
            'SELECT *
            FROM v_tampilkan_produk
            ORDER BY nomor_barang'
        );

        return view('barang.index', compact('data'));
    }

    public function show($id)
    {
        $hasil = DB::select(
            'SELECT *
            FROM v_tampilkan_produk
            WHERE nomor_barang = ?',
            [$id]
        );

        if (empty($hasil)) {
            abort(404);
        }
        $b = $hasil[0];

        $stok = DB::select(
            'SELECT *
            FROM V_show_barang
            WHERE nomor_barang = ?
            ORDER BY nomor_stok DESC',
            [$id]
        );

        return view('barang.show', compact('b', 'stok'));
    }

    public function add()
    {
        $jenisBarang = DB::select(
        'SELECT DISTINCT kode_jenis, kategori
        FROM v_tampilkan_produk
        ORDER BY kategori'
        );

        $satuan = DB::select(
            'SELECT nomor_satuan, satuan
            FROM satuan_vu
            WHERE keadaan = ?
            ORDER BY satuan',
            ['aktif']
        );

        return view('barang.add', compact('jenisBarang', 'satuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => ['required', Rule::in(['G', 'B', 'V', 'E', 'D', 'A', 'L', 'K'])],
            'nama' => ['required', 'string', 'max:255', Rule::unique('barang', 'nama')],
            'idsatuan' => [
                'required',
                'integer',
                Rule::exists('satuan', 'idsatuan')->where('status', 1),
            ],
            'harga' => ['required', 'integer', 'min:1'],
        ]);

        DB::statement(
            'CALL sp_tambah_barang(?, ?, ?, ?, @p_idbarang)',
            [
                $validated['jenis'],
                $validated['nama'],
                $validated['idsatuan'],
                $validated['harga'],
            ]
        );

        $hasil = DB::selectOne('SELECT @p_idbarang AS idbarang');
        $idBarang = $hasil->idbarang ?? null;

        if ($idBarang === null) {
            return back()
                ->withErrors(['nama' => 'Barang tidak dapat ditambahkan. Periksa kembali data.'])
                ->withInput();
        }

        return redirect('/barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $barang = DB::selectOne(
            'SELECT nomor_barang, nomor_satuan, kode_jenis, produk, satuan, status, harga_dasar, harga_akhir
            FROM v_tampilkan_produk
            WHERE nomor_barang = ?',
            [$id]
        );

        abort_if($barang === null, 404);

        $satuan = DB::select(
            'SELECT nomor_satuan, satuan
            FROM satuan_vu
            WHERE keadaan = ? OR nomor_satuan = ?
            ORDER BY satuan',
            ['aktif', $barang->nomor_satuan]
        );

        $terkunci = $this->memilikiRiwayat($id);

        return view('barang.edit', compact('barang', 'satuan', 'terkunci'));
    }

    public function update(Request $request, int $id)
    {
        $aturan = [
            'nama' => ['required', 'string', 'max:255', Rule::unique('barang', 'nama')->ignore($id, 'idbarang')],
            'harga' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['0', '1'])],
        ];

        if (! $this->memilikiRiwayat($id)) {
            $aturan['jenis'] = ['required', Rule::in(array_keys(Referensi::JENIS))];
            $aturan['idsatuan'] = ['required', 'integer', Rule::exists('satuan', 'idsatuan')];
        }

        $validated = $request->validate($aturan);

        DB::transaction(function () use ($id, $validated): void {
            $barang = DB::selectOne(
                'SELECT nomor_barang, kode_jenis, nomor_satuan
                FROM v_tampilkan_produk
                WHERE nomor_barang = ?
                FOR UPDATE',
                [$id]
            );

            abort_if($barang === null, 404);

            $terkunci = $this->memilikiRiwayat($id);
            $jenis = $terkunci ? $barang->kode_jenis : ($validated['jenis'] ?? $barang->kode_jenis);
            $idSatuan = $terkunci ? $barang->nomor_satuan : ($validated['idsatuan'] ?? $barang->nomor_satuan);

            if ((int) $idSatuan !== (int) $barang->nomor_satuan) {
                $satuanAktif = DB::selectOne(
                    'SELECT nomor_satuan FROM satuan_vu WHERE nomor_satuan = ? AND status = 1',
                    [$idSatuan]
                );

                if ($satuanAktif === null) {
                    throw ValidationException::withMessages([
                        'idsatuan' => 'Satuan yang dipilih sudah tidak aktif.',
                    ]);
                }
            }

            DB::update(
                'UPDATE v_barang_update
                SET kode_jenis = ?, produk = ?, nomor_satuan = ?, harga_dasar = ?, status = ?
                WHERE nomor_barang = ?',
                [$jenis, $validated['nama'], $idSatuan, $validated['harga'], (int) $validated['status'], $id]
            );
        });

        return redirect('/barang')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        try {
            $pesan = DB::transaction(function () use ($id): string {
                $barang = DB::selectOne(
                    'SELECT nomor_barang, produk, status
                    FROM v_tampilkan_produk
                    WHERE nomor_barang = ?
                    FOR UPDATE',
                    [$id]
                );

                abort_if($barang === null, 404);

                if (! $this->memilikiRiwayat($id)) {
                    DB::delete('DELETE FROM v_barang_delete WHERE nomor_barang = ?', [$id]);

                    return "Barang \"{$barang->produk}\" dihapus permanen karena belum memiliki riwayat transaksi.";
                }

                if ((int) $barang->status === 0) {
                    return "Barang \"{$barang->produk}\" sudah nonaktif dan tetap disimpan karena memiliki riwayat transaksi.";
                }

                DB::update('UPDATE v_barang_delete SET status = 0 WHERE nomor_barang = ?', [$id]);

                return "Barang \"{$barang->produk}\" memiliki riwayat transaksi, sehingga dinonaktifkan dan tidak dihapus. Riwayat tetap utuh.";
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'hapus' => 'Barang tidak dapat dihapus karena masih terhubung dengan data lain.',
            ]);
        }

        return redirect('/barang')->with('success', $pesan);
    }

    private function memilikiRiwayat(int $id): bool
    {
        $hasil = DB::selectOne(
            'SELECT (
                EXISTS (SELECT 1 FROM detail_pengadaan WHERE idbarang = ?)
                OR EXISTS (SELECT 1 FROM detail_penerimaan WHERE barang_idbarang = ?)
                OR EXISTS (SELECT 1 FROM detail_penjualan WHERE idbarang = ?)
                OR EXISTS (SELECT 1 FROM kartu_stok WHERE idbarang = ?)
            ) AS dipakai',
            [$id, $id, $id, $id]
        );

        return (int) $hasil->dipakai === 1;
    }
}
