<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    private const ROLE_BAWAAN = [1, 2, 3];

    public function index()
    {
        $data = DB::select(
            'SELECT *
            FROM role_vu
            ORDER BY nomor_peran'
        );

        return view('role.index', compact('data'));
    }

    public function create()
    {
        return view('role.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => ['required', 'string', 'max:100'],
        ], [
            'nama_role.required' => 'Nama role wajib diisi.',
            'nama_role.max' => 'Nama role maksimal 100 karakter.',
        ]);

        $existingRole = DB::selectOne(
            'SELECT nomor_peran
            FROM role_vu
            WHERE peran = ?',
            [$validated['nama_role']]
        );

        if ($existingRole !== null) {
            return back()
                ->withErrors(['nama_role' => 'Nama role sudah digunakan.'])
                ->withInput();
        }

        DB::insert(
            'INSERT INTO role_vu (peran) VALUES (?)',
            [$validated['nama_role']]
        );

        return redirect()
            ->route('role.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function show($id)
    {
        $hasil = DB::select(
            'SELECT *
        FROM role_vu
        where nomor_peran=?
        order by nomor_peran', [$id]
        );

        $r = $hasil[0];

        $detilrole = DB::select(
            'SELECT nama_pengguna
            FROM v_role_show
            WHERE nomor_peran = ?
            ORDER BY nama_pengguna',
            [$id]
        );

        return view('role.show', compact('r', 'detilrole'));
    }

    public function edit(int $id)
    {
        $role = DB::selectOne(
            'SELECT nomor_peran, peran
            FROM role_vu
            WHERE nomor_peran = ?',
            [$id]
        );

        abort_if($role === null, 404);

        return view('role.edit', compact('role'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nama_role' => ['required', 'string', 'max:100'],
        ], [
            'nama_role.required' => 'Nama role wajib diisi.',
            'nama_role.max' => 'Nama role maksimal 100 karakter.',
        ]);

        DB::transaction(function () use ($id, $validated): void {
            $role = DB::selectOne('SELECT nomor_peran FROM role_vu WHERE nomor_peran = ? FOR UPDATE', [$id]);

            abort_if($role === null, 404);

            $kembar = DB::selectOne(
                'SELECT nomor_peran
                FROM role_vu
                WHERE peran = ? AND nomor_peran <> ?',
                [$validated['nama_role'], $id]
            );

            if ($kembar !== null) {
                throw ValidationException::withMessages([
                    'nama_role' => 'Nama role sudah digunakan.',
                ]);
            }

            DB::update(
                'UPDATE role_vu SET peran = ? WHERE nomor_peran = ?',
                [$validated['nama_role'], $id]
            );
        });

        return redirect()->route('role.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (in_array($id, self::ROLE_BAWAAN, true)) {
            throw ValidationException::withMessages([
                'hapus' => 'Role bawaan sistem tidak dapat dihapus karena dipakai untuk pengaturan hak akses.',
            ]);
        }

        try {
            $nama = DB::transaction(function () use ($id): string {
                $role = DB::selectOne(
                    'SELECT nomor_peran, peran FROM role_vu WHERE nomor_peran = ? FOR UPDATE',
                    [$id]
                );

                abort_if($role === null, 404);

                $pengguna = DB::selectOne(
                    'SELECT COUNT(*) AS jumlah FROM user_vu WHERE nomor_jabatan = ?',
                    [$id]
                );

                if ((int) $pengguna->jumlah > 0) {
                    throw ValidationException::withMessages([
                        'hapus' => "Role \"{$role->peran}\" masih dimiliki {$pengguna->jumlah} pengguna. Pindahkan pengguna tersebut ke role lain terlebih dahulu.",
                    ]);
                }

                DB::delete('DELETE FROM role_vu WHERE nomor_peran = ?', [$id]);

                return $role->peran;
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'hapus' => 'Role tidak dapat dihapus karena masih terhubung dengan data lain.',
            ]);
        }

        return redirect()->route('role.index')->with('success', "Role \"{$nama}\" berhasil dihapus.");
    }
}
