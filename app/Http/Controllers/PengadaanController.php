<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PengadaanController extends Controller
{
    public function index(){
        $data= DB::select(
            'SELECT *
            from v_pengadaan
            order by nomor_pengadaan DESC'
        );
    return view('pengadaan.index', compact('data'));
    }

    public function create(Request $request)
{
    $vendor = DB::select(
        "SELECT nomor_vendor, penjual
        FROM vendor_vu
        WHERE status = 'Aktif'
        ORDER BY nomor_vendor"
    );

    $validated = $request->validate([
        'vendor_idvendor' => [
            'nullable',
            'integer',
            'exists:vendor,idvendor,status,A',
        ],
    ]);

    $vendorTerpilih = $validated['vendor_idvendor'] ?? null;
    $barang = [];

    if ($vendorTerpilih !== null) {
        $barang = DB::select(
            'SELECT DISTINCT
                p.nomor_barang,
                p.produk,
                p.harga_dasar,
                p.kategori
            FROM v_vendor_jenis_barang AS vj
            JOIN v_tampilkan_produk AS p
                ON p.kode_jenis = vj.jenis
            WHERE vj.nomor_vendor = ?
            AND p.status = 1
            ORDER BY p.produk',
            [$vendorTerpilih]
        );
    }

    return view('pengadaan.create', compact(
        'vendor',
        'barang',
        'vendorTerpilih'
    ));
}

    public function store(Request $request){
        $validated = $request->validate([
            'vendor_idvendor'=>[
                'required', 'integer', 'exists:vendor,idvendor,status,A',
            ],
            'items'=> ['required','array','min:1'],
            'items.*.idbarang'=>['required','integer','distinct',
            'exists:barang,idbarang,status,1',
        ],
        'items.*.jumlah'=>['required','integer','min:1'],
        ]);

        $pengguna = $request->user();

    if ($pengguna === null) {
        abort(401, 'Silakan login terlebih dahulu.');
    }

    $idUser = $pengguna->getAuthIdentifier();

    $idpengadaan = DB::transaction(function()use($validated, $idUser): int{
        DB::statement('SET @idpengadaan = NULL, @sp_status = NULL, @sp_pesan = NULL');

        DB::statement(
            'CALL sp_buat_pengadaan(?, ?, @idpengadaan, @sp_status, @sp_pesan)',
            [$idUser, $validated['vendor_idvendor']]
        );

        $hasil = DB::selectOne(
            'SELECT @idpengadaan AS idpengadaan, @sp_status AS status, @sp_pesan AS pesan'
        );

        if ($hasil === null || $hasil->status !== 'OK') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'vendor_idvendor' => $hasil->pesan ?? 'Pengadaan gagal dibuat.',
            ]);
        }

        $idpengadaan = (int) $hasil->idpengadaan;

    foreach($validated['items'] as $item) {
        DB::statement('SET @detail_status = NULL, @detail_pesan = NULL');

        DB::statement(
            'CALL sp_tambah_detail_pengadaan(?, ?, ?, @detail_status, @detail_pesan)',
                [
                    $idpengadaan,
                    $item['idbarang'],
                    $item['jumlah'],
                ]);

                $hasilDetail = DB::selectOne(
                    'SELECT @detail_status AS status, @detail_pesan AS pesan'
                );

                if ($hasilDetail === null || $hasilDetail->status !== 'OK') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => $hasilDetail->pesan ?? 'Detail pengadaan gagal ditambahkan.',
                    ]);
                }
            }
    return $idpengadaan;
    });
    return redirect('/pengadaan')
        ->with('success', "Pengadaan #{$idpengadaan} berhasil disimpan.");}

    public function show(int $id)
    {
        $pengadaan = DB::selectOne(
            'SELECT *
            FROM v_pengadaan
            WHERE nomor_pengadaan = ?',
            [$id]
        );

        abort_if($pengadaan === null, 404, 'Pengadaan tidak ditemukan.');

        $items = DB::select(
            'SELECT *
            FROM v_detail_sisa_pengadaan
            WHERE nomor_pengadaan = ?
            ORDER BY nomor_detail_pengadaan',
            [$id]
        );

        $penerimaan = DB::select(
            'SELECT nomor_penerimaan, waktu_penerimaan, pengguna, nama_barang,
                jumlah_terima, harga_satuan, subtotal
            FROM v_riwayat_penerimaan
            WHERE nomor_pengadaan = ?
            AND jumlah_terima > 0
            ORDER BY waktu_penerimaan, nomor_penerimaan, nama_barang',
            [$id]
        );

        return view('pengadaan.show', compact('pengadaan', 'items', 'penerimaan'));
    }
}
