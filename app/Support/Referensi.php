<?php

namespace App\Support;

/**
 * Tabel rujukan kode ke label.
 * Kolom jenis (barang) dan badan_hukum (vendor) menyimpan kode satu karakter,
 * sehingga label tampilannya dipetakan di sini.
 */
class Referensi
{
    /** barang.jenis */
    public const JENIS = [
        'G' => 'Gitar Akustik/Klasik',
        'B' => 'Bass',
        'V' => 'Biola',
        'E' => 'Gitar Listrik',
        'D' => 'Drum',
        'A' => 'Amplifier',
        'L' => 'Vinyl Album',
        'K' => 'Aksesori',
    ];

    /** vendor.badan_hukum */
    public const BADAN_HUKUM = [
        '1' => 'PT',
        '2' => 'CV',
        '0' => 'Non Badan Hukum',
    ];

    public static function jenis(?string $kode): string
    {
        return self::JENIS[$kode] ?? '-';
    }

    public static function badanHukum(?string $kode): string
    {
        return self::BADAN_HUKUM[$kode] ?? '-';
    }
}
