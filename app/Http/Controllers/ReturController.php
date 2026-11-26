<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturController extends Controller
{
    public function index(){
        $data= DB::select(
        'SELECT *
        FROM v_retur_index
        ORDER BY waktu DESC');



    return view('retur.index', compact('data'));}

    public function show(int $id){
        $retur= DB::selectOne(
        'SELECT *
        FROM v_retur_index
        where nomor_pengembalian =?
        ORDER BY waktu DESC', [$id]);

        abort_if(
            $retur === null,
            404,
            'Retur tidak ditemukan.'
        );

        $items= DB::select(
            'SELECT *
            FROM v_retur_show WHERE nomor_pengembalian = ?
            ORDER BY nomor_detil_retur',
            [$id]);

    return view('retur.show', compact('retur', 'items'));}



    public function create()
    {
        // Mengambil daftar penerimaan untuk dropdown
        $penerimaanList = DB::select(
            'SELECT DISTINCT
                nomor_penerimaan,
                nomor_pengadaan,
                waktu_penerimaan,
                vendor
            FROM v_riwayat_penerimaan
            ORDER BY waktu_penerimaan DESC'
        );

        $idpenerimaan = request()->query('idpenerimaan');

        $penerimaan = null;
        $items = [];

        if ($idpenerimaan !== null) {
            $penerimaan = DB::selectOne(
                'SELECT
                    nomor_penerimaan,
                    nomor_pengadaan,
                    waktu_penerimaan,
                    vendor,
                    pengguna
                FROM v_riwayat_penerimaan
                WHERE nomor_penerimaan = ?
                LIMIT 1',
                [$idpenerimaan]
            );

            abort_if(
                $penerimaan === null,
                404,
                'Penerimaan tidak ditemukan.'
            );


            $items = DB::select(
                'SELECT
                *
                FROM v_detail_sisa_retur
                WHERE nomor_penerimaan = ?
                AND jumlah_terima > 0
                ORDER BY nama_barang',
                [$idpenerimaan]
            );
        }

        return view(
            'retur.create',
            compact(
                'penerimaanList',
                'idpenerimaan',
                'penerimaan',
                'items'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpenerimaan' => [
                'required',
                'integer',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.iddetail_penerimaan' => [
                'required',
                'integer',
                'distinct',
            ],
            'items.*.jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'items.*.alasan' => [
                'required',
                'string',
                'max:200',
            ],
        ]);

        $pengguna = $request->user();

        if ($pengguna === null) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $idUser = $pengguna->getAuthIdentifier();


try {
    $idRetur = DB::transaction(function () use ($validated, $idUser): int {

        DB::statement('SET @idretur = NULL');

        DB::statement(
            'CALL sp_tambah_retur(?, ?, @idretur)',
            [
                $validated['idpenerimaan'],
                $idUser,
            ]
        );

        $hasilRetur = DB::selectOne(
            'SELECT @idretur AS idretur'
        );

        if (
            $hasilRetur === null|| $hasilRetur->idretur === null
        ) {
            throw new \RuntimeException(
                'Retur gagal dibuat.'
            );
        }

        $idRetur = (int) $hasilRetur->idretur;

        foreach ($validated['items'] as $item) {
            DB::statement(
                'SET @status = 0, @pesan = NULL'
            );

            DB::statement(
                'CALL sp_tambah_detail_retur(?, ?, ?, ?, @status, @pesan)',
                [
                    $idRetur,
                    $item['iddetail_penerimaan'],
                    $item['jumlah'],
                    $item['alasan'],
                ]
            );

            $hasil = DB::selectOne(
                'SELECT @status AS status, @pesan AS pesan'
            );

            if (
                $hasil === null
                || (int) $hasil->status !== 1
            ) {
                throw new \RuntimeException(
                    $hasil->pesan
                    ?? 'Detail retur gagal disimpan.'
                );
            }
        }

        return $idRetur;
    });
} catch (\RuntimeException $e) {
    return back()
        ->withErrors(['items' => $e->getMessage()])
        ->withInput();
}
    return redirect()
    ->route('retur.index')
    ->with('success', "Retur #{$idRetur} berhasil disimpan.");}
}
