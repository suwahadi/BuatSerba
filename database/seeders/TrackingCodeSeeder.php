<?php

namespace Database\Seeders;

use App\Models\GlobalConfig;
use Illuminate\Database\Seeder;

class TrackingCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'key' => 'tracking_code_header',
                'value' => '<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,\'script\',\'dataLayer\',\'GTM-XXXXXX\');</script>
<!-- End Google Tag Manager -->',
                'description' => 'Code to be injected into the <head> tag (e.g., GTM, GA, Meta Pixel).',
                'sort' => 140,
            ],
            [
                'key' => 'tracking_code_body',
                'value' => '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->',
                'description' => 'Code to be injected before the </body> tag.',
                'sort' => 150,
            ],
        ];

        foreach ($configs as $config) {
            GlobalConfig::firstOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
