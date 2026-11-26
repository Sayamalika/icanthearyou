<?php

namespace App\Http\Controllers;

use App\Auth\DatabaseUser;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class MarginPenjualanController extends Controller
{
    public function index()
    {
        $data = DB::select(
            'SELECT * FROM margin_vu
            ORDER BY nomor_margin'
        );

        return view('margin.index', compact('data'));
    }

    public function create()
    {
        return view('margin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'persen' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:49.99'],
            'status' => ['required', Rule::in(['0', '1'])],
        ], [
            'persen.required' => 'Persentase margin wajib diisi.',
            'persen.numeric' => 'Persentase margin harus berupa angka.',
            'persen.decimal' => 'Persentase margin maksimal memiliki 2 angka desimal.',
            'persen.min' => 'Persentase margin tidak boleh kurang dari 0%.',
            'persen.max' => 'Persentase margin maksimal 49,99%.',
            'status.required' => 'Status margin wajib dipilih.',
            'status.in' => 'Status margin tidak valid.',
        ]);

        $user = $request->user();
        abort_unless($user instanceof DatabaseUser, 403);

        DB::statement(
            'CALL sp_tambah_margin(?, ?, ?)',
            [$validated['persen'], (int) $validated['status'], $user->iduser]
        );

        return redirect()
            ->route('margin.index')
            ->with('success', 'Margin penjualan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $hasil = DB::select(
            'SELECT * FROM margin_vu
            WHERE nomor_margin = ?',
            [$id]
        );

        if (empty($hasil)) {
            abort(404);
        }

        $margin = $hasil[0];

        return view('margin.show', compact('margin'));
    }

    public function edit(int $id)
    {
        $margin = DB::selectOne(
            'SELECT nomor_margin AS idmargin_penjualan,
                persentase AS persen,
                CASE status_margin WHEN \'Aktif\' THEN 1 ELSE 0 END AS status
            FROM margin_vu
            WHERE nomor_margin = ?',
            [$id]
        );

        abort_if($margin === null, 404);

        $terkunci = $this->dipakaiPenjualan($id);

        return view('margin.edit', compact('margin', 'terkunci'));
    }

    public function update(Request $request, int $id)
    {
        $aturan = ['status' => ['required', Rule::in(['0', '1'])]];

        if (! $this->dipakaiPenjualan($id)) {
            $aturan['persen'] = ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:49.99'];
        }

        $validated = $request->validate($aturan, [
            'persen.required' => 'Persentase margin wajib diisi.',
            'persen.numeric' => 'Persentase margin harus berupa angka.',
            'persen.decimal' => 'Persentase margin maksimal memiliki 2 angka desimal.',
            'persen.min' => 'Persentase margin tidak boleh kurang dari 0%.',
            'persen.max' => 'Persentase margin maksimal 49,99%.',
            'status.required' => 'Status margin wajib dipilih.',
            'status.in' => 'Status margin tidak valid.',
        ]);

        DB::transaction(function () use ($id, $validated): void {
            $margin = DB::selectOne(
                'SELECT nomor_margin, persentase, status_margin
                FROM margin_vu
                WHERE nomor_margin = ?
                FOR UPDATE',
                [$id]
            );

            abort_if($margin === null, 404);

            $statusBaru = (int) $validated['status'];
            $persen = $this->dipakaiPenjualan($id) ? $margin->persentase : ($validated['persen'] ?? $margin->persentase);

            if ($statusBaru === 0 && (int) $margin->status === 1 && ! $this->adaMarginAktifLain($id)) {
                throw ValidationException::withMessages([
                    'status' => 'Minimal harus ada satu margin aktif agar harga jual dapat dihitung.',
                ]);
            }

            if ($statusBaru === 1) {
                $hasil = DB::selectOne('SELECT fn_aktifkan_margin(?) AS hasil', [$id]);
                abort_if((int) $hasil->hasil === 0, 404);
            }

            DB::update(
                'UPDATE v_margin_update
                SET persentase = ?, status_margin = ?
                WHERE nomor_margin = ?',
                [$persen, $statusBaru, $id]
            );
        });

        return redirect()
            ->route('margin.index')
            ->with('success', 'Margin penjualan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        try {
            $pesan = DB::transaction(function () use ($id): string {
                $margin = DB::selectOne(
                    'SELECT nomor_margin, status
                    FROM margin_vu
                    WHERE nomor_margin = ?
                    FOR UPDATE',
                    [$id]
                );

                abort_if($margin === null, 404);

                if ((int) $margin->status === 1 && ! $this->adaMarginAktifLain($id)) {
                    throw ValidationException::withMessages([
                        'hapus' => 'Margin aktif terakhir tidak dapat dihapus. Tambahkan atau aktifkan margin lain terlebih dahulu.',
                    ]);
                }

                if (! $this->dipakaiPenjualan($id)) {
                    DB::delete('DELETE FROM margin_vu WHERE nomor_margin = ?', [$id]);

                    return "Margin #{$id} dihapus permanen karena belum dipakai penjualan.";
                }

                if ((int) $margin->status === 0) {
                    return "Margin #{$id} sudah nonaktif dan tetap disimpan karena dipakai riwayat penjualan.";
                }

                DB::update('UPDATE margin_vu SET status = 0 WHERE nomor_margin = ?', [$id]);

                return "Margin #{$id} dipakai riwayat penjualan, sehingga dinonaktifkan dan tidak dihapus.";
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'hapus' => 'Margin tidak dapat dihapus karena masih terhubung dengan data lain.',
            ]);
        }

        return redirect()->route('margin.index')->with('success', $pesan);
    }

    private function dipakaiPenjualan(int $id): bool
    {
        $hasil = DB::selectOne(
            'SELECT EXISTS (SELECT 1 FROM v_penjualan_margin WHERE nomor_margin = ?) AS dipakai',
            [$id]
        );

        return (int) $hasil->dipakai === 1;
    }

    private function adaMarginAktifLain(int $id): bool
    {
        $hasil = DB::selectOne(
            'SELECT COUNT(*) AS jumlah
            FROM margin_vu
            WHERE status_margin = 1
            AND nomor_margin <> ?',
            [$id]
        );

        return (int) $hasil->jumlah > 0;
    }
}
