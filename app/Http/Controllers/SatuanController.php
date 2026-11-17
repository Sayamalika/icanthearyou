<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SatuanController extends Controller
{
    public function index(): View
    {
        $data = DB::select(
            'SELECT nomor_satuan, satuan, keadaan
            FROM satuan_vu
            ORDER BY satuan',
        );

        return view('satuan.index', compact('data'));
    }

    public function show(int $id)
    {
        $satuan = DB::selectOne(
            'SELECT *
            FROM v_satuan_barang
            WHERE nomor_satuan = ?',
            [$id]
        );

        if ($satuan === null) {
            abort(404);
        }

        $barang = DB::select(
            'SELECT *
            FROM v_selectOne_satuan
            WHERE nomor_satuan = ?
            ORDER BY nama_barang',
            [$id]
        );

        return view('satuan.show', compact('satuan', 'barang'));
    }

    public function edit(int $id): View
    {
        $satuan = DB::selectOne(
            'SELECT nomor_satuan,
                satuan,
            CASE
                WHEN keadaan COLLATE utf8mb4_unicode_ci = "aktif"
                THEN 1
                ELSE 0
            END AS status
            FROM satuan_vu
            WHERE nomor_satuan = ?',
            [$id]
        );

        abort_if($satuan === null, 404);

        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nama_satuan' => ['required', 'string', 'max:45'],
            'status' => ['required', Rule::in(['0', '1'])],
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.max' => 'Nama satuan maksimal 45 karakter.',
            'status.required' => 'Status satuan wajib dipilih.',
            'status.in' => 'Status satuan tidak valid.',
        ]);

        DB::transaction(function () use ($id, $validated): void {
            $satuan = DB::selectOne(
                'SELECT nomor_satuan FROM satuan_vu WHERE nomor_satuan = ? FOR UPDATE',
                [$id]
            );

            abort_if($satuan === null, 404);

            $kembar = DB::selectOne(
                'SELECT nomor_satuan FROM satuan_vu WHERE satuan = ? AND nomor_satuan <> ?',
                [$validated['nama_satuan'], $id]
            );

            if ($kembar !== null) {
                throw ValidationException::withMessages([
                    'nama_satuan' => 'Nama satuan sudah digunakan.',
                ]);
            }

            if ((int) $validated['status'] === 0) {
                $this->pastikanTidakDipakaiBarangAktif($id);
            }

            DB::update(
                'UPDATE satuan_vu SET satuan = ?, keadaan = ? WHERE nomor_satuan = ?',
                [$validated['nama_satuan'], (int) $validated['status'], $id]
            );
        });

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        try {
            $pesan = DB::transaction(function () use ($id): string {
                $satuan = DB::selectOne(
                    'SELECT nomor_satuan, satuan, keadaan
                    FROM satuan_vu
                    WHERE nomor_satuan = ?
                    FOR UPDATE',
                    [$id]
                );

                abort_if($satuan === null, 404);

                $jumlahBarang = DB::selectOne(
                    'SELECT COUNT(*) AS jumlah FROM v_tampilkan_produk WHERE nomor_satuan = ?',
                    [$id]
                );

                if ((int) $jumlahBarang->jumlah === 0) {
                    DB::delete('DELETE FROM satuan_vu WHERE nomor_satuan = ?', [$id]);

                    return "Satuan \"{$satuan->nama_satuan}\" dihapus permanen karena belum dipakai barang.";
                }

                $this->pastikanTidakDipakaiBarangAktif($id);

                if ((int) $satuan->status === 0) {
                    return "Satuan \"{$satuan->nama_satuan}\" sudah nonaktif dan tetap disimpan karena masih tercatat pada barang lama.";
                }

                DB::update('UPDATE satuan_vu SET status = 0 WHERE nomor_satuan = ?', [$id]);

                return "Satuan \"{$satuan->nama_satuan}\" masih tercatat pada barang nonaktif, sehingga dinonaktifkan dan tidak dihapus.";
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'hapus' => 'Satuan tidak dapat dihapus karena masih terhubung dengan data lain.',
            ]);
        }

        return redirect()->route('satuan.index')->with('success', $pesan);
    }

    private function pastikanTidakDipakaiBarangAktif(int $id): void
    {
        $aktif = DB::selectOne(
            'SELECT COUNT(*) AS jumlah FROM v_tampilkan_produk WHERE nomor_satuan = ? AND status = 1',
            [$id]
        );

        if ((int) $aktif->jumlah > 0) {
            throw ValidationException::withMessages([
                'hapus' => "Satuan masih dipakai {$aktif->jumlah} barang aktif. Ubah satuan barang tersebut terlebih dahulu.",
            ]);
        }
    }
}
