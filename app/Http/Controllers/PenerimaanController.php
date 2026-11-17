<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function index()
    {
        $data = DB::select(
            'SELECT *
            from v_penerimaan_index
            order by nomor_pengadaan'
        );

        return view('penerimaan.index', compact('data'));
    }

    public function create(int $id)
    {
        $pengadaan = DB::selectOne(
            'SELECT *
            from v_penerimaan_index
            where nomor_pengadaan=?', [$id]
        );
        if ($pengadaan === null) {
            abort(404, 'pengadaan tidak ditemukan');
        }

        $items = DB::select(
            'SELECT *
            FROM v_detail_sisa_pengadaan
            WHERE nomor_pengadaan = ?
            AND sisa > 0
            ORDER BY nomor_detail_pengadaan',
            [$id]
        );

        return view('penerimaan.create', compact('pengadaan', 'items'));
    }

    public function store(Request $request, int $id)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.jumlah_terima' => ['nullable', 'integer', 'min:0'],
        ]);

        $pengguna = $request->user();

        if ($pengguna === null) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $itemsDiterima = array_filter(
            $validated['items'],
            static fn (array $baris): bool => ($baris['jumlah_terima'] ?? 0) > 0
        );

        if ($itemsDiterima === []) {
            return back()->withErrors([
                'items' => 'minimal satu barang harus memiliki 1 atau lebih barang diterima.',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $id, $pengguna): void {
            $pengadaan = DB::selectOne(
                'SELECT nomor_pengadaan
                FROM v_penerimaan_index
                WHERE nomor_pengadaan = ?',
                [$id]
            );

            if ($pengadaan === null) {
                abort(404, 'Pengadaan tidak ditemukan atau sudah selesai.');
            }

            DB::insert(
                "INSERT INTO v_penerimaan_store_item (keadaan, nomor_pengadaan, nomor_user)
                VALUES ('S', ?, ?)",
                [$id, $pengguna->getAuthIdentifier()]
            );

            $idPenerimaan = (int) DB::getPdo()->lastInsertId();

            foreach ($validated['items'] as $idDetail => $baris) {
                $jumlahTerima = (int) $baris['jumlah_terima'];

                $item = DB::selectOne(
                    'SELECT nomor_barang, harga_tiap_barang
                    FROM v_detail_sisa_pengadaan
                    WHERE nomor_detail_pengadaan = ?
                    AND nomor_pengadaan = ?',
                    [$idDetail, $id]
                );

                DB::insert(
                    'INSERT INTO v_detail_masuk_gudang
                    VALUES (?, ?, ?, ?, ?)',
                    [
                        $idPenerimaan,
                        $item->nomor_barang,
                        $jumlahTerima,
                        $item->harga_tiap_barang,
                        $item->harga_tiap_barang * $jumlahTerima,
                    ]
                );
            }

            $sisa = DB::selectOne(
                'SELECT COUNT(*) AS jumlah_sisa
                FROM v_detail_sisa_pengadaan
                WHERE nomor_pengadaan = ?
                  AND sisa > 0',
                [$id]
            );

            DB::update(
                'UPDATE v_pengadaan_update
                SET keadaan = ?
                WHERE nomor_pengadaan = ?',
                [(int) $sisa->jumlah_sisa === 0 ? 'S' : 'P', $id]
            );
        });

        return redirect('/penerimaan')
            ->with('success', 'Penerimaan berhasil disimpan.');
    }

    public function riwayat()
    {
        $data = DB::select(
            'SELECT DISTINCT nomor_penerimaan,
        nomor_pengadaan, waktu_penerimaan, vendor, pengguna
        FROM v_riwayat_penerimaan
        where jumlah_terima >0
        ORDER BY waktu_penerimaan DESC, nomor_penerimaan DESC'
        );

        return view('penerimaan.riwayat', compact('data'));
    }

    public function detailRiwayat($id)
    {
        $detail = DB::select(
            'SELECT *
            FROM v_riwayat_penerimaan
            WHERE nomor_penerimaan=?
            and jumlah_terima>0
            order by nama_barang', [$id]
        );

        if ($detail === []) {
            abort(404, 'Detail Riwayat tidak ditemukan');
        }

        return view('penerimaan.detail_riwayat', compact('detail'));
    }

}
