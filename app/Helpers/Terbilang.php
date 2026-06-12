<?php

namespace App\Helpers;

class Terbilang
{
    private static $angka = [
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
        'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    ];

    public static function convert($nilai): string
    {
        $nilai = abs($nilai);
        $nilaiInt = (int) floor($nilai); // Remove decimals

        if ($nilaiInt < 12) {
            return self::$angka[$nilaiInt];
        } elseif ($nilaiInt < 20) {
            return self::$angka[$nilaiInt - 10] . ' Belas';
        } elseif ($nilaiInt < 100) {
            return self::$angka[(int) floor($nilaiInt / 10)] . ' Puluh ' . self::$angka[$nilaiInt % 10];
        } elseif ($nilaiInt < 200) {
            return 'Seratus ' . self::convert($nilaiInt - 100);
        } elseif ($nilaiInt < 1000) {
            return self::$angka[(int) floor($nilaiInt / 100)] . ' Ratus ' . self::convert($nilaiInt % 100);
        } elseif ($nilaiInt < 2000) {
            return 'Seribu ' . self::convert($nilaiInt - 1000);
        } elseif ($nilaiInt < 1000000) {
            return self::convert((int) floor($nilaiInt / 1000)) . ' Ribu ' . self::convert($nilaiInt % 1000);
        } elseif ($nilaiInt < 1000000000) {
            return self::convert((int) floor($nilaiInt / 1000000)) . ' Juta ' . self::convert($nilaiInt % 1000000);
        } elseif ($nilaiInt < 1000000000000) {
            return self::convert((int) floor($nilaiInt / 1000000000)) . ' Milyar ' . self::convert($nilaiInt % 1000000000);
        } elseif ($nilaiInt < 1000000000000000) {
            return self::convert((int) floor($nilaiInt / 1000000000000)) . ' Triliun ' . self::convert($nilaiInt % 1000000000000);
        }

        return '';
    }

    /**
     * Convert value to "Terbilang" string with "Rupiah" suffix.
     */
    public static function rupiah($nilai): string
    {
        $result = trim(self::convert($nilai));
        return $result . ' Rupiah';
    }
}
