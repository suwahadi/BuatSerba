<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'key' => 'site_name',
                'value' => 'Buatserba.com',
                'description' => 'Nama situs/brand yang ditampilkan di aplikasi.',
                'sort' => 10,
            ],
            [
                'key' => 'slogan',
                'value' => 'Semua serba ada',
                'description' => 'Slogan/tagline yang ditampilkan di aplikasi.',
                'sort' => 20,
            ],
            [
                'key' => 'company_name',
                'value' => 'CV Laris Manis',
                'description' => 'Nama perusahaan/entitas bisnis.',
                'sort' => 30,
            ],
            [
                'key' => 'phone',
                'value' => '081586843355',
                'description' => 'Nomor telepon utama untuk layanan pelanggan.',
                'sort' => 50,
            ],
            [
                'key' => 'whatsapp',
                'value' => '081586843355',
                'description' => 'Nomor WhatsApp untuk layanan pelanggan.',
                'sort' => 60,
            ],
            [
                'key' => 'email',
                'value' => 'cs@buatserba.com',
                'description' => 'Email layanan pelanggan.',
                'sort' => 70,
            ],
            [
                'key' => 'admin_email',
                'value' => 'buatserba@tempverify.com',
                'description' => 'Email Admin tujuan notifikasi',
                'sort' => 71,
            ],
            [
                'key' => 'address',
                'value' => 'Jl. Jelambar 1 No. 123, Grogol, Jakarta 12011',
                'description' => 'Alamat perusahaan untuk ditampilkan pada halaman kontak/dokumen.',
                'sort' => 80,
            ],
            [
                'key' => 'prefix_trx',
                'value' => 'BSR-',
                'description' => 'Prefix untuk nomor transaksi/pesanan.',
                'sort' => 90,
            ],
            [
                'key' => 'expiration_time',
                'value' => '480',
                'description' => 'Batas waktu pembayaran dalam menit (8 jam = 480 menit).',
                'sort' => 100,
            ],
            [
                'key' => 'manual_bank_name',
                'value' => 'BCA',
                'description' => 'Nama bank untuk pembayaran transfer manual.',
                'sort' => 110,
            ],
            [
                'key' => 'manual_bank_account_number',
                'value' => '2373018881',
                'description' => 'Nomor rekening bank untuk transfer manual.',
                'sort' => 120,
            ],
            [
                'key' => 'manual_bank_account_name',
                'value' => 'CV Laris Manis',
                'description' => 'Nama pemilik rekening untuk transfer manual.',
                'sort' => 130,
            ],
        ];

        foreach ($configs as $config) {
            \App\Models\GlobalConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
