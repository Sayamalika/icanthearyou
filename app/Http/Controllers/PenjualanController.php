<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(){
        $data = DB::select('
        SELECT * FROM v_penjualan_index');
    return view('penjualan.index', compact('data'));}

    public function create(){
        $barang = DB::select('
        SELECT
        nomor_barang,
        produk,
        kategori,
        harga_akhir,
        stok_terkini
        FROM v_tampilkan_produk
        WHERE status = 1
        AND stok_terkini > 0
        ORDER BY produk');
    return view('penjualan.create', compact('barang'));}

    public function store(Request $request){
        $validated = $request->validate([
        'items' => ['required', 'array', 'min:1'],
        'items.*.idbarang' => [
            'required',
            'integer',
            'distinct',
            'exists:barang,idbarang,status,1',
        ],
        'items.*.jumlah' => ['required', 'integer', 'min:1'],
    ]);

    $pengguna = $request->user();

    if ($pengguna === null) {
        abort(401, 'Silakan login terlebih dahulu.');
    }

    $idUser = $pengguna->getAuthIdentifier();

    $idPenjualan = DB::transaction(function () use ($validated, $idUser): int {
        DB::statement(
            'SET @idpenjualan = NULL, @penjualan_status = NULL, @penjualan_pesan = NULL'
        );

        DB::statement(
            'CALL sp_tambah_penjualan(?, @idpenjualan, @penjualan_status, @penjualan_pesan)',
            [$idUser]
        );

        $hasilPenjualan = DB::selectOne(
            'SELECT
                @idpenjualan AS idpenjualan,
                @penjualan_status AS status,
                @penjualan_pesan AS pesan'
        );

        if (
            $hasilPenjualan === null
            || $hasilPenjualan->status !== 'OK'
            || $hasilPenjualan->idpenjualan === null
        ) {
            throw ValidationException::withMessages([
                'items' => $hasilPenjualan->pesan
                    ?? 'Penjualan gagal dibuat.',
            ]);
        }

        $idPenjualan = (int) $hasilPenjualan->idpenjualan;

        foreach ($validated['items'] as $item) {
            DB::statement(
                'SET @detail_status = NULL, @detail_pesan = NULL'
            );

            DB::statement(
                'CALL sp_tambah_detail_penjualan(?, ?, ?, @detail_status, @detail_pesan)',
                [
                    $idPenjualan,
                    $item['idbarang'],
                    $item['jumlah'],
                ]
            );

            $hasilDetail = DB::selectOne(
                'SELECT
                    @detail_status AS status,
                    @detail_pesan AS pesan'
            );

            if ($hasilDetail === null || $hasilDetail->status !== 'OK') {
                throw ValidationException::withMessages([
                    'items' => $hasilDetail->pesan
                        ?? 'Detail penjualan gagal ditambahkan.',
                ]);
            }
        }

        return $idPenjualan;
    });

    return redirect()
        ->route('penjualan.index')
        ->with('success', "Penjualan #{$idPenjualan} berhasil disimpan.");
    }

    public function show(int $id)
    {
        $penjualan = DB::selectOne(
            'SELECT *
            FROM v_penjualan_index
            WHERE nomor_penjualan = ?',
            [$id]
        );

        abort_if($penjualan === null, 404, 'Penjualan tidak ditemukan.');

        $items = DB::select(
            'SELECT *
            FROM v_penjualan_show
            WHERE nomor_penjualan = ?
            ORDER BY nomor_detil_penjualan',
            [$id]
        );

        return view('penjualan.show', compact('penjualan', 'items'));
    }
}
