<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'about',
                'content' => '<p>BuatSerba adalah platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p><p>BuatSerba adalah platform e-commerce terpercaya yang menyediakan berbagai macam produk berkualitas dengan harga terbaik. Kami berkomitmen untuk memberikan pengalaman belanja online yang mudah, aman, dan menyenangkan bagi seluruh pelanggan kami.</p><p>Dengan koleksi produk yang lengkap dari berbagai kategori, mulai dari elektronik, fashion, peralatan rumah tangga, hingga kebutuhan sehari-hari, BuatSerba hadir sebagai solusi belanja one-stop shopping untuk memenuhi semua kebutuhan Anda.</p><p>Kami menjamin keaslian produk, harga kompetitif, pengiriman cepat, dan layanan pelanggan yang responsif. Kepuasan pelanggan adalah prioritas utama kami, dan kami terus berinovasi untuk memberikan layanan terbaik.</p>',
                'is_active' => 1,
                'sort' => 10,
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => '<p><strong>FAQ</strong></p>
<p><strong>Apa itu buatserba.com?</strong><br>
buatserba.com adalah platform belanja online yang menyediakan berbagai produk untuk kebutuhan sehari-hari dan kebutuhan lainnya.</p>

<p><strong>Bagaimana cara melakukan pemesanan?</strong><br>
Pilih produk yang diinginkan, masukkan ke keranjang, lengkapi data pengiriman, lalu selesaikan pembayaran melalui metode yang tersedia di website.</p>

<p><strong>Apakah saya harus membuat akun untuk berbelanja?</strong><br>
Pengguna dapat melakukan pemesanan sesuai dengan ketentuan yang berlaku di website. Pada beberapa kondisi, data tertentu tetap dibutuhkan untuk keperluan transaksi dan pengiriman.</p>

<p><strong>Metode pembayaran apa saja yang tersedia?</strong><br>
Kami menyediakan berbagai metode pembayaran online yang diproses melalui payment gateway resmi pihak ketiga, termasuk Midtrans.</p>

<p><strong>Apakah pembayaran di website ini aman?</strong><br>
Ya. Seluruh proses pembayaran dilakukan melalui sistem pembayaran pihak ketiga yang berizin. Kami tidak menyimpan data sensitif pembayaran seperti nomor kartu atau kode OTP.</p>

<p><strong>Kapan pesanan saya diproses?</strong><br>
Pesanan akan diproses setelah pembayaran berhasil dikonfirmasi oleh sistem pembayaran. Waktu pemrosesan dapat berbeda tergantung produk dan kondisi operasional.</p>

<p><strong>Bagaimana dengan pengiriman pesanan?</strong><br>
<strong>Apakah melayani pengiriman ke seluruh Indonesia?</strong><br>
Ya. Kami melayani pengiriman ke seluruh wilayah Indonesia sesuai dengan jangkauan mitra logistik.</p>

<p><strong>Apakah saya bisa membatalkan pesanan?</strong><br>
Pembatalan pesanan dapat dilakukan selama pesanan belum diproses atau dikirim. Pesanan yang sudah dikirim tidak dapat dibatalkan dan mengikuti ketentuan retur.</p>

<p><strong>Bagaimana jika produk yang saya terima rusak atau tidak sesuai?</strong><br>
Pengguna dapat mengajukan permohonan retur sesuai dengan ketentuan yang tercantum pada halaman Kebijakan Retur & Refund.</p>

<p><strong>Berapa lama proses retur dan refund?</strong><br>
Proses retur dan refund membutuhkan waktu sesuai dengan hasil verifikasi dan ketentuan penyedia layanan pembayaran. Lama proses dapat berbeda tergantung metode pembayaran yang digunakan.</p>

<p><strong>Apakah semua produk bisa diretur?</strong><br>
Tidak semua produk dapat diretur. Retur hanya dapat diproses apabila memenuhi syarat dan ketentuan yang berlaku.</p>

<p><strong>Bagaimana cara menghubungi layanan pelanggan?</strong><br>
Pengguna dapat menghubungi kami melalui:</p>
<ul>
    <li><strong>Email:</strong> toko@buatserba.com</li>
    <li><strong>WhatsApp:</strong> 0815 8684 3355</li>
    <li><strong>Alamat:</strong> Jalan Jelambar Madya Timur IX No. 281 RT 007 / RW 009, Kelurahan Jelambar Kecamatan Grogol Petamburan Jakarta Barat 11460, Indonesia.</li>
</ul>',
                'is_active' => 1,
                'sort' => 20,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'privacy-policy',
                'content' => '<p><strong>KEBIJAKAN PRIVASI</strong></p>
<p><strong>buatserba.com</strong><br>
<strong>Terakhir diperbarui: 2 Jan 2026</strong></p>

<p><strong>1. Pendahuluan</strong><br>
buatserba.com menghargai dan melindungi privasi setiap pengguna yang mengakses dan menggunakan layanan kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi pengguna sehubungan dengan penggunaan situs web dan layanan transaksi di buatserba.com.</p>
<p>Dengan mengakses dan/atau menggunakan situs buatserba.com, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh isi Kebijakan Privasi ini.</p>

<p><strong>2. Informasi yang Kami Kumpulkan</strong><br>
Kami dapat mengumpulkan informasi berikut dari pengguna:<br>
<strong>a. Informasi Pribadi</strong><br>
* Nama lengkap<br>
* Alamat email<br>
* Nomor telepon<br>
* Alamat pengiriman dan/atau penagihan</p>

<p><strong>b. Informasi Transaksi</strong><br>
* Detail pesanan<br>
* Metode pembayaran yang dipilih<br>
* Status pembayaran dan riwayat transaksi</p>

<p><strong>Catatan: buatserba.com tidak menyimpan data sensitif pembayaran</strong>, seperti nomor kartu kredit, CVV, atau OTP. Seluruh proses pembayaran ditangani oleh penyedia payment gateway pihak ketiga yang berizin (misalnya Midtrans).</p>

<p><strong>c. Informasi Teknis</strong><br>
* Alamat IP<br>
* Jenis perangkat dan browser<br>
* Data cookies dan aktivitas penggunaan situs</p>

<p><strong>3. Penggunaan Informasi</strong><br>
Informasi yang dikumpulkan digunakan untuk tujuan berikut:<br>
* Memproses pesanan dan transaksi pengguna<br>
* Mengelola pengiriman produk<br>
* Memberikan layanan pelanggan dan dukungan<br>
* Mengirimkan informasi terkait pesanan atau layanan<br>
* Meningkatkan keamanan dan performa situs<br>
* Mematuhi kewajiban hukum dan peraturan yang berlaku</p>

<p><strong>4. Cookies</strong><br>
buatserba.com menggunakan cookies untuk:<br>
* Mengingat preferensi pengguna<br>
* Membantu proses transaksi<br>
* Menganalisis penggunaan situs untuk peningkatan layanan</p>
<p>Pengguna dapat mengatur browser untuk menolak cookies, namun hal ini dapat memengaruhi pengalaman penggunaan situs.</p>

<p><strong>5. Keamanan Data</strong><br>
Kami berkomitmen untuk menjaga keamanan data pribadi pengguna dengan langkah-langkah teknis dan organisasi yang wajar, termasuk:<br>
* Pembatasan akses data<br>
* Penggunaan sistem keamanan standar industri<br>
* Kerja sama hanya dengan penyedia layanan yang terpercaya</p>
<p>Meskipun demikian, pengguna memahami bahwa transmisi data melalui internet tidak sepenuhnya bebas risiko.</p>

<p><strong>6. Pembagian Informasi kepada Pihak Ketiga</strong><br>
Kami dapat membagikan informasi pengguna kepada pihak ketiga <strong>hanya bila diperlukan</strong>, antara lain:<br>
* Penyedia payment gateway (misalnya Midtrans)<br>
* Mitra logistik dan pengiriman<br>
* Penyedia layanan teknologi pendukung<br>
* Otoritas hukum jika diwajibkan oleh peraturan perundang-undangan</p>
<p>Kami <strong>tidak menjual atau menyewakan data pribadi pengguna</strong> kepada pihak mana pun.</p>

<p><strong>7. Penyimpanan Data</strong><br>
Data pribadi pengguna akan disimpan selama:<br>
* Diperlukan untuk menjalankan layanan<br>
* Diwajibkan oleh peraturan hukum yang berlaku</p>
<p>Setelah tidak diperlukan, data akan dihapus atau dianonimkan sesuai kebijakan internal kami.</p>

<p><strong>8. Hak Pengguna</strong><br>
Pengguna berhak untuk:<br>
* Mengakses dan memperbarui data pribadi<br>
* Meminta penghapusan data (sesuai ketentuan hukum)<br>
* Mengajukan pertanyaan terkait perlindungan data pribadi</p>
<p>Permintaan dapat disampaikan melalui kontak resmi buatserba.com.</p>

<p><strong>9. Perubahan Kebijakan Privasi</strong><br>
buatserba.com dapat mengubah Kebijakan Privasi ini sewaktu-waktu. Perubahan akan ditampilkan di halaman ini dan berlaku sejak tanggal pembaruan.<br>
Pengguna disarankan untuk meninjau Kebijakan Privasi secara berkala.</p>

<p><strong>10. Kontak Kami</strong><br>
Jika pengguna memiliki pertanyaan atau permintaan terkait Kebijakan Privasi ini, silakan menghubungi kami melalui:</p>
<p>Email: toko@buatserba.com<br>
WhatsApp: 0815 8684 3355</p>
<p>Alamat:<br>
Jalan Jelambar Madya Timur IX No. 281<br>
RT 007 / RW 009, Kelurahan Jelambar<br>
Kecamatan Grogol Petamburan<br>
Jakarta Barat 11460, Indonesia.</p>',
                'is_active' => 1,
                'sort' => 30,
            ],
            [
                'title' => 'Kebijakan Retur & Refund',
                'slug' => 'return-refund-policy',
                'content' => '<p><strong>KEBIJAKAN RETUR & REFUND</strong></p>
<p><strong>buatserba.com</strong><br>
<strong>Terakhir diperbarui: 2 Jan 2026</strong></p>

<p><strong>1. Pendahuluan</strong><br>
Kebijakan Retur & Refund ini mengatur ketentuan pengembalian barang dan pengembalian dana atas transaksi yang dilakukan melalui buatserba.com.<br>
Dengan melakukan transaksi, pengguna dianggap telah membaca, memahami, dan menyetujui kebijakan ini.</p>

<p><strong>2. Ketentuan Umum Retur</strong><br>
Retur dapat diajukan apabila memenuhi salah satu kondisi berikut:<br>
1. Produk diterima dalam kondisi rusak atau cacat.<br>
2. Produk yang diterima tidak sesuai dengan pesanan.<br>
3. Terjadi kesalahan pengiriman dari pihak kami.</p>

<p>Permohonan retur tidak dapat diproses apabila:<br>
- Kerusakan disebabkan oleh penggunaan atau kesalahan pengguna.<br>
- Produk telah digunakan, dicuci, atau diubah.<br>
- Produk tidak dikembalikan dalam kondisi dan kelengkapan awal.</p>

<p><strong>3. Prosedur Pengajuan Retur</strong><br>
1. Pengguna wajib mengajukan permohonan retur melalui kontak resmi dalam jangka waktu <strong>maksimal 3 hari kalender</strong> sejak produk diterima.<br>
2. Permohonan harus disertai bukti pendukung seperti foto atau video produk.<br>
3. Setelah permohonan disetujui, pengguna akan menerima instruksi pengembalian barang.<br>
Barang yang dikirim tanpa persetujuan terlebih dahulu berhak untuk tidak diproses.</p>

<p><strong>4. Pengiriman Barang Retur</strong><br>
1. Produk harus dikirim kembali sesuai instruksi yang diberikan.<br>
2. Biaya pengiriman retur dapat ditanggung oleh pengguna atau kami, tergantung pada penyebab retur.<br>
3. Risiko kerusakan selama pengiriman retur menjadi tanggung jawab pihak pengirim.</p>

<p><strong>5. Ketentuan Refund</strong><br>
Refund akan diproses apabila:<br>
- Retur telah diterima dan diverifikasi.<br>
- Produk dinyatakan memenuhi syarat retur.<br>
Pengembalian dana dilakukan melalui metode pembayaran yang sama atau metode lain sesuai ketentuan penyedia layanan pembayaran.<br>
Proses refund membutuhkan waktu sesuai kebijakan penyedia layanan pembayaran pihak ketiga, termasuk <strong>Midtrans</strong>.</p>

<p><strong>6. Bentuk Pengembalian</strong><br>
Pengembalian dapat berupa:<br>
1. Pengembalian dana.<br>
2. Penggantian produk.<br>
3. Bentuk kompensasi lain yang disepakati bersama.<br>
Keputusan bentuk pengembalian ditentukan berdasarkan hasil evaluasi.</p>

<p><strong>7. Pembatalan Pesanan</strong><br>
Pembatalan pesanan hanya dapat dilakukan selama pesanan belum diproses atau dikirim. Pesanan yang telah dikirim tidak dapat dibatalkan dan mengikuti ketentuan retur.</p>

<p><strong>8. Batasan Tanggung Jawab</strong><br>
Kami tidak bertanggung jawab atas keterlambatan atau kegagalan pengembalian akibat data yang tidak lengkap, kesalahan pengguna, atau faktor di luar kendali wajar kami.</p>

<p><strong>9. Perubahan Kebijakan</strong><br>
Kebijakan Retur & Refund ini dapat diubah sewaktu-waktu. Perubahan berlaku sejak ditampilkan di situs buatserba.com.</p>

<p><strong>10. Kontak</strong><br>
Untuk pengajuan retur atau pertanyaan terkait kebijakan ini, silakan hubungi kami melalui:<br>
Email: toko@buatserba.com<br>
WhatsApp: 0815 8684 3355</p>',
                'is_active' => 1,
                'sort' => 40,
            ],
            [
                'title' => 'Syarat & Ketentuan',
                'slug' => 'terms-conditions',
                'content' => '<p><strong>SYARAT DAN KETENTUAN</strong></p>
<p><strong>buatserba.com</strong><br>
Terakhir diperbarui: <em>2 Jan 2026</em></p>

<p><strong>1. Pendahuluan</strong><br>
Syarat dan Ketentuan ini mengatur penggunaan situs web buatserba.com beserta seluruh layanan, fitur, dan transaksi yang tersedia di dalamnya.<br>
Dengan mengakses, mendaftar, atau melakukan transaksi melalui buatserba.com, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh isi Syarat dan Ketentuan ini.</p>

<p><strong>2. Definisi</strong><br>
*   <strong>Pengguna</strong> adalah setiap pihak yang mengakses dan/atau menggunakan layanan buatserba.com.<br>
*   <strong>Kami</strong> adalah pengelola dan penyedia layanan buatserba.com.<br>
*   <strong>Produk</strong> adalah barang yang ditawarkan melalui situs buatserba.com.<br>
*   <strong>Transaksi</strong> adalah proses pemesanan, pembayaran, dan pengiriman produk melalui situs.</p>

<p><strong>3. Ketentuan Umum Penggunaan</strong><br>
1.  Pengguna wajib memberikan informasi yang benar, lengkap, dan akurat saat melakukan transaksi.<br>
2.  Pengguna bertanggung jawab atas seluruh aktivitas yang dilakukan melalui akun atau data yang digunakan.<br>
3.  Kami berhak menolak, membatasi, atau menghentikan layanan apabila ditemukan indikasi penyalahgunaan, penipuan, atau pelanggaran terhadap Syarat dan Ketentuan ini.</p>

<p><strong>4. Produk dan Informasi</strong><br>
1.  Kami berupaya menampilkan informasi produk secara akurat, termasuk deskripsi, harga, dan ketersediaan.<br>
2.  Perbedaan kecil pada warna, ukuran, atau tampilan dapat terjadi akibat perbedaan perangkat atau pencahayaan.<br>
3.  Kami berhak mengubah, memperbarui, atau menghentikan penjualan produk tertentu tanpa pemberitahuan sebelumnya.</p>

<p><strong>5. Harga dan Pembayaran</strong><br>
1.  Harga yang tercantum adalah harga yang berlaku saat pemesanan dilakukan.<br>
2.  Pembayaran dilakukan melalui metode pembayaran yang tersedia di situs.<br>
3.  Seluruh proses pembayaran ditangani oleh penyedia layanan pembayaran pihak ketiga yang berizin, termasuk <strong>Midtrans</strong>.<br>
4.  Kami tidak menyimpan data sensitif pembayaran pengguna, seperti nomor kartu, CVV, atau kode OTP.<br>
5.  Transaksi dianggap sah setelah pembayaran berhasil diverifikasi oleh sistem pembayaran.</p>

<p><strong>6. Pengiriman</strong><br>
1.  Pengiriman dilakukan melalui mitra logistik yang bekerja sama dengan kami.<br>
2.  Estimasi waktu pengiriman bersifat perkiraan dan dapat berbeda tergantung lokasi dan kondisi eksternal.<br>
3.  Risiko keterlambatan akibat faktor di luar kendali kami (cuaca, bencana, gangguan operasional logistik) tidak menjadi tanggung jawab kami.</p>

<p><strong>7. Retur dan Refund</strong><br>
Ketentuan terkait pengembalian barang dan pengembalian dana diatur secara terpisah dalam halaman <strong>Kebijakan Retur & Refund</strong> dan merupakan bagian yang tidak terpisahkan dari Syarat dan Ketentuan ini.</p>

<p><strong>8. Pembatalan Transaksi</strong><br>
1.  Pembatalan pesanan dapat dilakukan sesuai dengan status pesanan dan ketentuan yang berlaku.<br>
2.  Kami berhak membatalkan transaksi apabila terjadi kesalahan sistem, kesalahan harga, atau indikasi pelanggaran.<br>
3.  Pengembalian dana, jika ada, akan diproses sesuai kebijakan yang berlaku.</p>

<p><strong>9. Hak Kekayaan Intelektual</strong><br>
Seluruh konten di buatserba.com, termasuk teks, gambar, logo, dan desain, merupakan milik kami atau pihak yang bekerja sama dengan kami dan dilindungi oleh peraturan perundang-undangan yang berlaku.<br>
Pengguna dilarang menggunakan, menyalin, atau mendistribusikan konten tanpa izin tertulis.</p>

<p><strong>10. Batasan Tanggung Jawab</strong><br>
1.  Kami tidak bertanggung jawab atas kerugian tidak langsung, kerugian bisnis, atau kerugian akibat penggunaan situs di luar kendali wajar kami.<br>
2.  Layanan disediakan sebagaimana adanya dan dapat mengalami gangguan teknis sewaktu-waktu.</p>

<p><strong>11. Perubahan Layanan dan Ketentuan</strong><br>
Kami berhak untuk mengubah, memperbarui, atau menyesuaikan Syarat dan Ketentuan ini sewaktu-waktu. Perubahan akan berlaku sejak ditampilkan di situs buatserba.com.</p>

<p><strong>12. Hukum yang Berlaku</strong><br>
Syarat dan Ketentuan ini diatur dan ditafsirkan berdasarkan hukum yang berlaku di Republik Indonesia.</p>

<p><strong>13. Kontak</strong><br>
Untuk pertanyaan terkait Syarat dan Ketentuan ini, pengguna dapat menghubungi kami melalui:</p>
<p><strong>Email:</strong> toko@buatserba.com<br>
<strong>WhatsApp:</strong> 0815 8684 3355</p>
<p><strong>Alamat:</strong><br>
Jalan Jelambar Madya Timur IX No. 281<br>
RT 007 / RW 009, Kelurahan Jelambar<br>
Kecamatan Grogol Petamburan<br>
Jakarta Barat 11460, Indonesia.</p>',
                'is_active' => 1,
                'sort' => 50,
            ],
        ];

        foreach ($pages as $page) {
            \App\Models\Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
