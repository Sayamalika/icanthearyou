<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index()
    {
        $data = DB::select(
            'SELECT *
            FROM user_vu
            ORDER BY nomor_pengguna'
        );

        return view('user.index', compact('data'));
    }

    public function show($id)
    {
        $hasil = DB::select(
            'SELECT *
            FROM user_vu
            WHERE nomor_pengguna = ?
            ORDER BY nomor_pengguna',
            [$id]
        );

        abort_if($hasil === [], 404);

        $u = $hasil[0];

        $detiluser = DB::select(
            'SELECT peran
            FROM user_vu
            WHERE nomor_pengguna = ?
            ORDER BY peran',
            [$id]
        );

        return view('user.show', compact('u', 'detiluser'));
    }

    public function edit(int $id): View
    {
        $pengguna = DB::selectOne(
            'SELECT nomor_pengguna,
                nama,
                nomor_jabatan
            FROM user_vu
            WHERE nomor_pengguna = ?',
            [$id]
        );

        abort_if($pengguna === null, 404);

        $roles = DB::select(
            'SELECT nomor_peran, peran
            FROM role_vu
            ORDER BY peran'
        );

        return view('user.edit', compact('pengguna', 'roles'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:45'],
            'nomor_peran' => ['required', 'integer'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 45 karakter.',
            'nomor_peran.required' => 'Role wajib dipilih.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $idPelaku = (int) $request->user()->iduser;

        DB::transaction(function () use ($id, $validated, $idPelaku): void {
            $pengguna = DB::selectOne(
                'SELECT iduser, idrole FROM user WHERE iduser = ? FOR UPDATE',
                [$id]
            );

            abort_if($pengguna === null, 404);

            $role = DB::selectOne(
                'SELECT nomor_peran AS idrole
                FROM role_vu
                WHERE nomor_peran = ?',
                [$validated['nomor_peran']]
            );

            if ($role === null) {
                throw ValidationException::withMessages([
                    'nomor_peran' => 'Role tidak ditemukan.',
                ]);
            }

            if ($id === $idPelaku && (int) $role->idrole !== (int) $pengguna->idrole) {
                throw ValidationException::withMessages([
                    'nomor_peran' => 'Anda tidak dapat mengubah role akun Anda sendiri.',
                ]);
            }

            $kembar = DB::selectOne(
                'SELECT nomor_pengguna
                FROM user_vu
                WHERE nama = ? AND nomor_pengguna <> ?',
                [$validated['username'], $id]
            );

            if ($kembar !== null) {
                throw ValidationException::withMessages([
                    'username' => 'Username sudah digunakan, silahkan gunakan username lain.',
                ]);
            }

            DB::update(
                'UPDATE user SET username = ?, idrole = ? WHERE iduser = ?',
                [$validated['username'], $role->idrole, $id]
            );

            if (! empty($validated['password'])) {
                DB::update(
                    'UPDATE user SET password = ? WHERE iduser = ?',
                    [Hash::make($validated['password']), $id]
                );
            }
        });

        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        if ($id === (int) $request->user()->iduser) {
            throw ValidationException::withMessages([
                'hapus' => 'Anda tidak dapat menghapus akun yang sedang digunakan.',
            ]);
        }

        try {
            $nama = DB::transaction(function () use ($id): string {
                $pengguna = DB::selectOne(
                    'SELECT iduser, username FROM user WHERE iduser = ? FOR UPDATE',
                    [$id]
                );

                abort_if($pengguna === null, 404);

                $riwayat = DB::selectOne(
                    'SELECT (
                        EXISTS (SELECT 1 FROM pengadaan WHERE user_iduser = ?)
                        OR EXISTS (SELECT 1 FROM penerimaan WHERE iduser = ?)
                        OR EXISTS (SELECT 1 FROM retur WHERE iduser = ?)
                        OR EXISTS (SELECT 1 FROM penjualan WHERE iduser = ?)
                        OR EXISTS (SELECT 1 FROM margin_penjualan WHERE iduser = ?)
                    ) AS dipakai',
                    [$id, $id, $id, $id, $id]
                );

                if ((int) $riwayat->dipakai === 1) {
                    throw ValidationException::withMessages([
                        'hapus' => "Pengguna \"{$pengguna->username}\" memiliki riwayat transaksi sehingga tidak dapat dihapus. Ubah datanya melalui menu Ubah.",
                    ]);
                }

                DB::delete('DELETE FROM user WHERE iduser = ?', [$id]);

                return $pengguna->username;
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'hapus' => 'Pengguna tidak dapat dihapus karena masih terhubung dengan data lain.',
            ]);
        }

        return redirect()->route('user.index')->with('success', "Pengguna \"{$nama}\" berhasil dihapus.");
    }
}
