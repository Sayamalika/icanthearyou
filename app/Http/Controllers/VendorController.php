<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VendorController extends Controller
{
    public function index()
    {
        $data = DB::select(
            'SELECT *
            FROM vendor_vu
            ORDER BY nomor_vendor'
        );

        return view('vendor.index', compact('data'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:100'],
            'badan_hukum' => ['required', Rule::in(['0', '1', '2'])],
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi.',
            'nama_vendor.max' => 'Nama vendor maksimal 100 karakter.',
            'badan_hukum.required' => 'Bentuk badan hukum wajib dipilih.',
            'badan_hukum.in' => 'Bentuk badan hukum tidak valid.',
        ]);

        DB::insert(
            'INSERT INTO v_vendor_update (penjual, badan_hukum, status) VALUES (?, ?, ?)',
            [$validated['nama_vendor'], $validated['badan_hukum'], 'A']
        );

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $vendor = DB::selectOne(
            'SELECT *
            FROM vendor_vu
            WHERE nomor_vendor = ?',
            [$id]
        );

        abort_if($vendor === null, 404);

        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:100'],
            'badan_hukum' => ['required', Rule::in(['0', '1', '2'])],
            'status' => ['required', Rule::in(['A', 'N'])],
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi.',
            'nama_vendor.max' => 'Nama vendor maksimal 100 karakter.',
            'badan_hukum.required' => 'Bentuk badan hukum wajib dipilih.',
            'badan_hukum.in' => 'Bentuk badan hukum tidak valid.',
            'status.required' => 'Status vendor wajib dipilih.',
            'status.in' => 'Status vendor tidak valid.',
        ]);

        $diubah = DB::update(
            'UPDATE v_vendor_update
            SET penjual = ?, badan_hukum = ?, status = ?
            WHERE nomor_vendor = ?',
            [$validated['nama_vendor'], $validated['badan_hukum'], $validated['status'], $id]
        );

        if ($diubah === 0) {
            $ada = DB::selectOne('SELECT nomor_vendor FROM vendor_vu WHERE nomor_vendor = ?', [$id]);
            abort_if($ada === null, 404);
        }

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        try {
            $pesan = DB::transaction(function () use ($id): string {
                $vendor = DB::selectOne(
                    'SELECT nomor_vendor, penjual, status
                    FROM vendor_vu
                    WHERE nomor_vendor = ?
                    FOR UPDATE',
                    [$id]
                );

                abort_if($vendor === null, 404);

                $pengadaan = DB::selectOne(
                    'SELECT COUNT(*) AS jumlah FROM v_pengadaan WHERE nomor_vendor = ?',
                    [$id]
                );

                if ((int) $pengadaan->jumlah === 0) {
                    DB::delete('DELETE FROM v_vendor_update WHERE nomor_vendor = ?', [$id]);

                    return "Vendor \"{$vendor->penjual}\" dihapus permanen karena belum memiliki pengadaan.";
                }

                if ($vendor->status !== 'A') {
                    DB::update("UPDATE v_vendor_update SET status = 'N' WHERE nomor_vendor = ?", [$id]);

                    return "Vendor \"{$vendor->penjual}\" sudah nonaktif dan tetap disimpan karena memiliki riwayat pengadaan.";
                }

                return "Vendor \"{$vendor->penjual}\" memiliki riwayat pengadaan, sehingga dinonaktifkan dan tidak dihapus.";
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'hapus' => 'Vendor tidak dapat dihapus karena masih terhubung dengan data lain.',
            ]);
        }

        return redirect()->route('vendor.index')->with('success', $pesan);
    }
}
