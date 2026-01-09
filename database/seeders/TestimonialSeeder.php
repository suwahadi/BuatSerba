<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Ridho Pratama',
                'location' => 'Bandung',
                'content' => 'Belanja di BuatSerba sangat memuaskan. Barangnya original dan pengirimannya cepat sampai.',
                'is_active' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Siti Aminah',
                'location' => 'Jakarta',
                'content' => 'Pelayanan customer service-nya sangat ramah dan responsif. Suka banget belanja di sini!',
                'is_active' => true,
                'sort' => 2,
            ],
            [
                'name' => 'Rudi Hartono',
                'location' => 'Surabaya',
                'content' => 'Harga produknya kompetitif banget dibandingkan toko sebelah. Kualitasnya juga oke punya.',
                'is_active' => true,
                'sort' => 3,
            ],
            [
                'name' => 'Dewi Lestari',
                'location' => 'Medan',
                'content' => 'Suka dengan promo-promo yang sering diadain. Hemat banget belanja kebutuhan bulanan di sini.',
                'is_active' => true,
                'sort' => 4,
            ],
            [
                'name' => 'Andi Wijaya',
                'location' => 'Semarang',
                'content' => 'Aplikasi user friendly, mudah digunakan. Proses checkout juga gampang dan banyak pilihan pembayaran.',
                'is_active' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Rina Marlina',
                'location' => 'Yogyakarta',
                'content' => 'Packaging aman dan rapi. Barang sampai dengan selamat tanpa cacat sedikitpun. Recommended seller!',
                'is_active' => true,
                'sort' => 6,
            ],
            [
                'name' => 'Eko Prasetyo',
                'location' => 'Malang',
                'content' => 'Varian produknya lengkap banget. Apa aja yang dicari pasti ada di BuatSerba. Mantap!',
                'is_active' => true,
                'sort' => 7,
            ],
            [
                'name' => 'Susi Susanti',
                'location' => 'Denpasar',
                'content' => 'Pengalaman belanja online terbaik selama ini. Tidak pernah kecewa belanja di BuatSerba. Sukses terus!',
                'is_active' => true,
                'sort' => 8,
            ],
            [
                'name' => 'Hendra Gunawan',
                'location' => 'Makassar',
                'content' => 'Fitur tracking order-nya sangat membantu. Jadi tenang nunggu paket datang karena update-nya real-time.',
                'is_active' => true,
                'sort' => 9,
            ],
            [
                'name' => 'Lina Wati',
                'location' => 'Palembang',
                'content' => 'Sering dapat voucher gratis ongkir. Lumayan banget buat menghemat pengeluaran belanja.',
                'is_active' => true,
                'sort' => 10,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
