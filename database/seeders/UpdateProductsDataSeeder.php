<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateProductsDataSeeder extends Seeder
{
    public function run(): void
    {
        // Update Samsung Galaxy S23
        Product::where('slug', 'samsung-galaxy-s23')->update([
            'description' => 'Samsung Galaxy S23 adalah smartphone flagship terbaru dari Samsung yang menghadirkan performa luar biasa dengan chip Snapdragon 8 Gen 2. Dilengkapi dengan kamera 50MP yang mampu menghasilkan foto berkualitas tinggi dalam berbagai kondisi pencahayaan. Layar Dynamic AMOLED 2X 6.1 inch memberikan pengalaman visual yang memukau dengan refresh rate 120Hz yang smooth untuk gaming dan scrolling.',
            'images' => [
                'https://indodana-web.imgix.net/assets/samsung-galaxy-s23-lavender-thumbnail.png',
                'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=500',
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500',
            ],
            'features' => [
                'Kamera 50MP dengan teknologi AI dan Night Mode',
                'Layar Dynamic AMOLED 2X 6.1 inch dengan refresh rate 120Hz',
                'Chip Snapdragon 8 Gen 2 untuk performa maksimal',
                'Baterai 3900mAh dengan fast charging 25W dan wireless charging',
                'IP68 water and dust resistant - tahan air hingga 1.5 meter',
                'One UI 5.1 berbasis Android 13 dengan update 4 tahun',
                'Gorilla Glass Victus 2 untuk perlindungan layar maksimal',
            ],
            'specifications' => [
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => '8GB LPDDR5X',
                'Storage' => '256GB UFS 4.0',
                'Display' => '6.1" Dynamic AMOLED 2X, 120Hz',
                'Resolution' => '2340 x 1080 pixels',
                'Main Camera' => '50MP f/1.8 OIS',
                'Ultra Wide' => '12MP f/2.2',
                'Telephoto' => '10MP f/2.4 3x Optical Zoom',
                'Front Camera' => '12MP f/2.2',
                'Battery' => '3900mAh',
                'Charging' => '25W Fast Charging, 15W Wireless',
                'OS' => 'Android 13, One UI 5.1',
                'Connectivity' => '5G, WiFi 6E, Bluetooth 5.3',
                'Dimensions' => '146.3 x 70.9 x 7.6 mm',
                'Weight' => '168g',
                'Protection' => 'Gorilla Glass Victus 2, IP68',
            ],
        ]);

        // Update iPhone 15 Pro
        Product::where('slug', 'iphone-15-pro')->update([
            'description' => 'iPhone 15 Pro menghadirkan inovasi terbaru dari Apple dengan chip A17 Pro yang powerful. Desain titanium yang premium dan ringan, dilengkapi dengan kamera 48MP yang menghasilkan foto berkualitas profesional. Dynamic Island memberikan cara baru berinteraksi dengan iPhone Anda.',
            'images' => [
                'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=500',
                'https://images.unsplash.com/photo-1695048133364-1a6e0e6f2c5f?w=500',
            ],
            'features' => [
                'Chip A17 Pro dengan GPU 6-core untuk performa gaming maksimal',
                'Kamera 48MP dengan sensor quad-pixel',
                'Desain titanium aerospace-grade yang ringan dan kuat',
                'Dynamic Island untuk multitasking yang lebih intuitif',
                'Action Button yang dapat dikustomisasi',
                'USB-C dengan USB 3 untuk transfer data super cepat',
                'Always-On Display dengan ProMotion 120Hz',
            ],
            'specifications' => [
                'Processor' => 'A17 Pro chip',
                'Display' => '6.1" Super Retina XDR',
                'Resolution' => '2556 x 1179 pixels',
                'Refresh Rate' => '120Hz ProMotion',
                'Main Camera' => '48MP f/1.78',
                'Ultra Wide' => '12MP f/2.2',
                'Telephoto' => '12MP f/2.8 3x Optical Zoom',
                'Front Camera' => '12MP TrueDepth',
                'Video' => '4K ProRes, Cinematic Mode',
                'Storage Options' => '128GB / 256GB / 512GB / 1TB',
                'Battery' => 'Up to 23 hours video playback',
                'Charging' => 'MagSafe 15W, USB-C',
                'OS' => 'iOS 17',
                'Connectivity' => '5G, WiFi 6E, Bluetooth 5.3',
                'Material' => 'Titanium frame',
                'Protection' => 'Ceramic Shield, IP68',
            ],
        ]);

        // Update ASUS ROG Zephyrus G14
        Product::where('slug', 'asus-rog-zephyrus-g14')->update([
            'description' => 'ASUS ROG Zephyrus G14 adalah laptop gaming premium yang menggabungkan performa powerful dengan portabilitas. Dilengkapi dengan AMD Ryzen 9 dan NVIDIA RTX 4060, laptop ini mampu menjalankan game AAA dengan setting maksimal. Layar 14 inch dengan refresh rate tinggi memberikan pengalaman gaming yang smooth.',
            'features' => [
                'AMD Ryzen 9 7940HS untuk performa multitasking',
                'NVIDIA GeForce RTX 4060 8GB GDDR6',
                'Layar 14" QHD+ 165Hz dengan response time 3ms',
                'Cooling system ROG Intelligent Cooling',
                'Keyboard RGB per-key dengan Aura Sync',
                'Baterai 76WHrs untuk gaming mobile',
                'Audio premium dengan Dolby Atmos',
            ],
            'specifications' => [
                'Processor' => 'AMD Ryzen 9 7940HS',
                'Graphics' => 'NVIDIA RTX 4060 8GB',
                'RAM' => '16GB DDR5',
                'Storage' => '1TB PCIe 4.0 NVMe SSD',
                'Display' => '14" QHD+ (2560x1600)',
                'Refresh Rate' => '165Hz',
                'OS' => 'Windows 11 Home',
                'Battery' => '76WHrs',
                'Weight' => '1.65kg',
                'Ports' => 'USB-C, USB-A, HDMI 2.1, Audio Jack',
            ],
        ]);

        // Update other products with basic data
        $basicUpdates = [
            'macbook-air-m3' => [
                'features' => [
                    'Chip M3 dengan 8-core CPU dan 10-core GPU',
                    'Layar Liquid Retina 13.6 inch',
                    'Baterai hingga 18 jam pemakaian',
                    'MagSafe charging',
                    'Desain fanless - tanpa kipas, tanpa suara',
                ],
                'specifications' => [
                    'Chip' => 'Apple M3',
                    'RAM' => '8GB Unified Memory',
                    'Storage' => '256GB SSD',
                    'Display' => '13.6" Liquid Retina',
                    'Weight' => '1.24kg',
                ],
            ],
        ];

        foreach ($basicUpdates as $slug => $data) {
            Product::where('slug', $slug)->update($data);
        }
    }
}
