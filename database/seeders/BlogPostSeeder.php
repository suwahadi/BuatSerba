<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class BlogPostSeeder extends Seeder
{
    private array $loremParagraphs = [
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
        
        "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.",
        
        "At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.",
        
        "Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.",
        
        "Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
        
        "Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?",
    ];

    public function run(): void
    {
        $categories = BlogCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('Blog categories not found. Run BlogCategorySeeder first.');
            return;
        }

        $posts = [
            [
                'title' => '10 Tips Sukses dalam Berbisnis Online',
                'category_slug' => 'tips',
            ],
            [
                'title' => 'Cara Membangun Motivasi Diri untuk Mencapai Target',
                'category_slug' => 'motivasi',
            ],
            [
                'title' => 'Tren Digital Marketing yang Wajib Diketahui di Tahun Ini',
                'category_slug' => 'trend',
            ],
            [
                'title' => 'Strategi Efektif Meningkatkan Penjualan Produk',
                'category_slug' => 'tips',
            ],
            [
                'title' => 'Kisah Inspiratif Pengusaha Muda yang Berhasil',
                'category_slug' => 'motivasi',
            ],
            [
                'title' => 'Perkembangan Teknologi E-Commerce Terkini',
                'category_slug' => 'trend',
            ],
        ];

        foreach ($posts as $index => $postData) {
            $category = $categories->firstWhere('slug', $postData['category_slug']);
            
            if (!$category) {
                continue;
            }

            $slug = BlogPost::generateUniqueSlug($postData['title']);
            
            $content = implode("\n\n", array_slice($this->loremParagraphs, 0, 5));
            
            $thumbnailPath = $this->generatePlaceholderImage($slug, $index);

            BlogPost::create([
                'category_id' => $category->id,
                'title' => $postData['title'],
                'slug' => $slug,
                'thumbnail' => $thumbnailPath,
                'content' => $content,
                'meta_seo' => [
                    [
                        'title' => $postData['title'],
                        'description' => Str::limit(strip_tags($content), 160),
                        'og_title' => null,
                        'og_description' => null,
                        'og_image' => null,
                        'twitter_card' => 'summary_large_image',
                    ]
                ],
                'is_active' => true,
                'view_count' => rand(50, 500),
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function generatePlaceholderImage(string $slug, int $index): string
    {
        $colors = [
            ['bg' => '#3B82F6', 'text' => '#FFFFFF'],
            ['bg' => '#10B981', 'text' => '#FFFFFF'],
            ['bg' => '#F59E0B', 'text' => '#FFFFFF'],
            ['bg' => '#EF4444', 'text' => '#FFFFFF'],
            ['bg' => '#8B5CF6', 'text' => '#FFFFFF'],
            ['bg' => '#EC4899', 'text' => '#FFFFFF'],
        ];

        $color = $colors[$index % count($colors)];
        
        $image = Image::create(1200, 800, $color['bg'])
            ->text(substr(strtoupper($slug), 0, 2), 600, 400, function ($font) use ($color) {
                $font->size(200);
                $font->color($color['text']);
                $font->align('center');
                $font->valign('middle');
            });

        $filename = "{$slug}.webp";
        $path = "blog/{$filename}";
        
        Storage::disk('public')->put($path, (string) $image->toWebp(90));

        return $path;
    }
}
