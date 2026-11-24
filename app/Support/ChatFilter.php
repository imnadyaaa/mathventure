<?php

namespace App\Support;

/**
 * Sensor kata tidak pantas dan pemblokir kontak untuk chat.
 *
 * - sensor()            : mengganti kata kasar dengan bintang (***), pesan tetap terkirim.
 * - pelanggaranKontak() : menolak pesan berisi tautan, email, nomor HP, atau ajakan pindah aplikasi.
 */
class ChatFilter
{
    /**
     * Daftar kata kasar. Tambahkan sesuai kebutuhan.
     * Kata dengan 6 huruf atau lebih dicocokkan sebagai potongan kata,
     * kata yang lebih pendek harus cocok persis (supaya kata biasa tidak ikut tersensor).
     */
    private const KATA = [
        'anjing', 'anjir', 'anjrit', 'anjg', 'asu', 'bangsat', 'bgst', 'bajingan', 'babi', 'bangke', 'kampret',
        'kontol', 'kntl', 'memek', 'ngentot', 'ngewe', 'jancok', 'jancuk', 'goblok', 'tolol', 'idiot',
        'pelacur', 'lonte', 'perek', 'bokep', 'porno', 'sange', 'titit', 'tai', 'kntl', 
        'fuck', 'shit', 'bitch', 'bastard', 'asshole', 'dick', 'pussy', 'slut', 'whore', 
    ];

    /** Kata yang dipakai untuk mengajak pindah ke aplikasi lain. */
    private const SOSMED = [
        'wa', 'ig', 'whatsapp', 'instagram', 'telegram', 'discord', 'tiktok', 'snapchat', 'facebook', 'fb', 'tt',
    ];

    public static function sensor(string $teks): string
    {
        // 1. Huruf yang dipisah-pisah, misalnya "a.n.j.i.n.g" atau "b a b i"
        $teks = preg_replace_callback(
            '/(?<![\p{L}\p{N}])(?:[\p{L}\p{N}][\s.\-_*]+){2,}[\p{L}\p{N}](?![\p{L}\p{N}])/u',
            fn ($m) => self::kasar($m[0]) ? self::bintang($m[0]) : $m[0],
            $teks
        );

        // 2. Kata biasa
        return preg_replace_callback(
            '/[\p{L}\p{N}@$!]+/u',
            fn ($m) => self::kasar($m[0]) ? self::bintang($m[0]) : $m[0],
            $teks
        );
    }

    /** Mengembalikan pesan penolakan, atau null kalau aman. */
    public static function pelanggaranKontak(string $teks): ?string
    {
        $pesan = 'Demi keamananmu, jangan membagikan kontak, tautan, atau akun media sosial ya.';

        // Tautan atau nama situs
        if (preg_match('~(https?://|www\.|\b[a-z0-9-]+\.(com|net|org|id|co|io|me|ly|gg|xyz|link|app)\b)~i', $teks)) {
            return $pesan;
        }
        // Email
        if (preg_match('/[\w.+-]+@[\w-]+\.[\w.]+/u', $teks)) {
            return $pesan;
        }
        // Nomor HP Indonesia (08xx atau +628xx)
        if (preg_match('/(?<!\d)(?:\+?62|0)8\d[\d\s.\-]{6,}/', $teks)) {
            return $pesan;
        }
        // Nama akun, misalnya @budi_123
        if (preg_match('/(^|\s)@\w{3,}/u', $teks)) {
            return $pesan;
        }
        // Ajakan pindah ke aplikasi lain
        $kata = preg_split('/[^a-z0-9]+/', mb_strtolower($teks, 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($kata as $k) {
            if (in_array($k, self::SOSMED, true)) {
                return $pesan;
            }
        }

        return null;
    }

    private static function kasar(string $teks): bool
    {
        $n = self::normalisasi($teks);
        if ($n === '') {
            return false;
        }
        foreach (self::kataBersih() as $k) {
            if (strlen($k) >= 6 ? str_contains($n, $k) : $n === $k) {
                return true;
            }
        }
        return false;
    }

    /** Huruf kecil, angka/simbol yang mirip huruf diganti, huruf berulang jadi satu. */
    private static function normalisasi(string $teks): string
    {
        $t = mb_strtolower($teks, 'UTF-8');
        $t = strtr($t, [
            '0' => 'o', '1' => 'i', '3' => 'e', '4' => 'a',
            '5' => 's', '7' => 't', '@' => 'a', '$' => 's', '!' => 'i',
        ]);
        $t = preg_replace('/[^a-z]/', '', $t);

        return preg_replace('/(.)\1+/', '$1', $t);
    }

    private static function kataBersih(): array
    {
        static $bersih = null;

        return $bersih ??= array_map([self::class, 'normalisasi'], self::KATA);
    }

    private static function bintang(string $s): string
    {
        return str_repeat('*', mb_strlen($s));
    }
}